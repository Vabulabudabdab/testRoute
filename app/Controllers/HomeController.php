<?php

namespace App\Controllers;

use App\QueryBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;
use PDO;
class HomeController {
    private $templates;
    private $auth;
    private $querybuilder;

    public function __construct(QueryBuilder $queryBuilder, Engine $engine, \Delight\Auth\Auth $auth) {
        $this->querybuilder = $queryBuilder;
        $this->templates = $engine;
        $this->auth = $auth;
    }

    public function index() {
//        $this->auth->admin()->addRoleForUserById(1, \Delight\Auth\Role::ADMIN);
        d($this->auth->isLoggedIn());
        d($this->auth->getRoles());
        d($this->querybuilder);
        echo $this->templates->render('homepage', ['name'=>'John']);
    }

    public function about() {

        try {
            $userId = $this->auth->register("test2@example.com", "exampl", "user1", function ($selector, $token) {
                echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email)';
                echo '  For emails, consider using the mail(...) function, Symfony Mailer, Swiftmailer, PHPMailer, etc.';
                echo '  For SMS, consider using a third-party service and a compatible SDK';
            });

            echo 'We have signed up a new user with the ID ' . $userId;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
        echo "about work";
    }

    public function email_verification(){
        try {
            $this->auth->confirmEmail("z6BJoPtpwWb0qTfm","-BIyImSzlgN4iY-A");

            echo 'Email address has been verified';
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            die('Invalid token');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            die('Token expired');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('Email address already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }


    public function login() {
        try {
            $this->auth->login("test2@example.com", "exampl");
            $_SESSION['log'] = "test2@example.com";
            echo $_SESSION['log'];
            echo 'User is logged in';
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Wrong email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Wrong password');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            die('Email not verified');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }
}