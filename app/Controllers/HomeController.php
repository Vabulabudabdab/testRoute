<?php

namespace App\Controllers;

use app\QueryBuilder;
use League\Plates\Engine;

class HomeController {

    public function index($vars) {
        $templates = new Engine('../app/Views');

        echo $templates->render('homepage', ['name'=>'John']);
    }

    public function about($vars) {
        $templates = new Engine('../app/Views');

        echo $templates->render('about', ['title'=>'User Profile']);
    }

}