<?php

namespace App\Controllers;

use League\Plates\Engine;

class HomeController {
    private $templates;
    public function __construct() {
        $this->templates = new Engine('../app/Views');
    }

    public function index($vars) {
        echo $this->templates->render('homepage', ['name'=>'John']);
    }

    public function about() {
        echo $this->templates->render('about', ['title'=>'User Profile']);
    }

}