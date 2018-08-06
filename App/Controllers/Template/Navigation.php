<?php
namespace App\Controllers\Template;

use \Core\View;

class Navigation{
    public function index(){
        $data['menu1'] = [
            'home' => '/',
            'category1' => '/cat1/',
            'category2' => '/cat2/',
            'category3' => '/cat3/'
        ];

        return View::returnTemplate('Template/Navigation.twig', $data);

    }
}

