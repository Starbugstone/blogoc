<?php
namespace App\Models;

class Menu{
    public function getMenu(){
        //this shall be replaced by a model call
        $data['navigation'] = [
            'category1' => '/cat/1',
            'category2' => '/cat/2',
            'category3' => '/cat/3'
        ];

        return $data;

    }
}