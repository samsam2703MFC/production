<?php
namespace App\Controllers\Web\Auth;

use App\Core\Web\BaseController;
use App\Services\Auth\LoginService;

/**
 * login class
 */
class LoginController extends BaseController
{

    private LoginService $loginService;
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function index()
    {
        $this->view('auth/login', []);
    }

    public function login()
    {
        $_POST['login'] = "admin";
        $_POST['password'] = "admin";

        if(!$this->loginService->login($_POST['login'], $_POST['password'])) {
            $this->view('auth/login', []);
        } else {
            header('Location: ' . ROOT . '/dashboard');
        }
    }

}
