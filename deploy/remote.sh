# Exécuté SUR le serveur par .github/workflows/deploy.yml, via `ssh … bash -s`.
# Le workflow ajoute en tête : DEPLOY_PATH, GH_TOKEN, GH_REPO, DEPLOY_SHA.
set -euo pipefail

cd "$DEPLOY_PATH"
cd "$(git rev-parse --show-toplevel)"

branch=$(git rev-parse --abbrev-ref HEAD)
if [ "$branch" != "main" ]; then
    echo "::error::Le clone du serveur est sur « $branch », pas sur main : rien n'a été touché."
    exit 1
fi

before=$(git rev-parse HEAD)

# Jeton de l'exécution GitHub (valable pendant le job seulement) : le pull marche
# même si le dépôt est privé, sans identifiants permanents sur le serveur.
auth_args=()
if [ -n "${GH_TOKEN:-}" ]; then
    auth=$(printf 'x-access-token:%s' "$GH_TOKEN" | base64 -w0)
    auth_args=(-c "http.https://github.com/.extraheader=AUTHORIZATION: basic $auth")
fi
# --ff-only : si quelqu'un a modifié des fichiers suivis sur le serveur, on s'arrête
# au lieu d'écraser ou de fusionner.
git "${auth_args[@]}" pull --ff-only "https://github.com/$GH_REPO.git" main

after=$(git rev-parse HEAD)
if [ "$before" = "$after" ]; then
    echo "Déjà à jour ($after)."
else
    echo "Mis à jour : ${before:0:7} → ${after:0:7}"
    git log --oneline "$before..$after"
fi
[ "$after" = "$DEPLOY_SHA" ] || echo "Note : main a avancé depuis ce déclenchement, le serveur est sur ${after:0:7}."

# Dépendances Composer (vendor/ n'est pas dans Git) : jamais lancé automatiquement,
# le PHP du serveur n'est pas forcément celui du conteneur de l'application.
if ! git diff --quiet "$before" "$after" -- app/public/composer.lock; then
    echo "::warning::app/public/composer.lock a changé : lancez composer install dans app/public."
fi

# Migrations SQL : jamais appliquées automatiquement
new_migrations=$(git diff --name-only --diff-filter=A "$before" "$after" -- app/migrations)
if [ -n "$new_migrations" ]; then
    echo "::warning::Nouvelles migrations à appliquer à la main : $(echo $new_migrations)"
fi

# Clés présentes dans .env.example mais absentes du .env du serveur (valeurs jamais affichées)
if [ -f app/.env ]; then
    missing=$(comm -23 <(grep -oE '^[A-Z_]+=' app/.env.example | sort -u) <(grep -oE '^[A-Z_]+=' app/.env | sort -u) | tr -d '=' | xargs)
    [ -z "$missing" ] || echo "::warning::Clés absentes de app/.env sur le serveur : $missing"
else
    echo "::warning::app/.env introuvable sur le serveur."
fi
