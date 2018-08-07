<?php
namespace App\Models;

class Includes{
    public function getMenu(){
        //this shall be replaced by a model call
        $data = [
            'category1' => '/cat/1',
            'category2' => '/cat/2',
            'category3' => '/cat/3'
        ];

        return $data;
    }

    public function getJumbotron(){
        $data = [];
        return $data;
    }

}