# Déploiement automatique

À chaque push sur `main` (ou via **Actions → Deploy ERP → Run workflow**), GitHub :

1. contrôle la syntaxe de tous les fichiers PHP et s'arrête en cas d'erreur ;
2. se connecte au serveur en SSH et y lance `deploy/remote.sh`, qui fait un
   `git pull --ff-only` de `main` dans le clone du serveur.

Le script s'arrête sans rien toucher si le clone du serveur n'est pas sur `main`,
ou si des fichiers suivis par Git y ont été modifiés à la main.

Il ne fait jamais automatiquement, mais affiche un avertissement dans GitHub :

- les nouvelles migrations SQL de `app/migrations` à appliquer ;
- `composer install` quand `app/public/composer.lock` a changé ;
- les clés de `app/.env.example` absentes du `.env` du serveur (sans afficher les valeurs).

Le pull utilise le jeton temporaire de l'exécution GitHub : il continue de marcher
quand le dépôt passe en privé, sans identifiants GitHub stockés sur le serveur.

## Mise en place (une seule fois)

**1. Sur le serveur**, avec l'utilisateur qui fait d'habitude le `git pull` :

```bash
ssh-keygen -t ed25519 -N "" -C "github-deploy-erp" -f ~/.ssh/github_deploy_erp
cat ~/.ssh/github_deploy_erp.pub >> ~/.ssh/authorized_keys
cat ~/.ssh/github_deploy_erp                   # clé privée → secret DEPLOY_SSH_KEY
cd /chemin/de/l/erp && git rev-parse --show-toplevel   # → secret DEPLOY_PATH
```

**2. Dans GitHub**, *Settings → Secrets and variables → Actions → New repository secret* :

| Secret           | Valeur                                                   |
|------------------|----------------------------------------------------------|
| `DEPLOY_HOST`    | IP publique du serveur                                   |
| `DEPLOY_USER`    | utilisateur SSH (celui de l'étape 1)                     |
| `DEPLOY_PATH`    | dossier affiché par `git rev-parse --show-toplevel`      |
| `DEPLOY_SSH_KEY` | contenu complet de `~/.ssh/github_deploy_erp`            |
| `DEPLOY_PORT`    | seulement si SSH n'écoute pas sur le port 22             |

Tant que ces secrets manquent, le workflow ne fait que le contrôle de syntaxe.

**3. Tester** : *Actions → Deploy ERP → Run workflow*.
