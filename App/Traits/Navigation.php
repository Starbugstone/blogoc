<?php
namespace App\Traits;

/**
 * Trait Navigation
 * @package App\Traits
 */

trait Navigation{

    /**
     * @return array
     */
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
