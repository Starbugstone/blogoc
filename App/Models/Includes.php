<?php
namespace App\Models;

use Core\Model;

/**
 * Class Includes
 * All the included views data (menu, jumbotron, ...)
 * the returned data should then be stored in a $data['var'] and the view will check if var is set
 * @package App\Models
 */

class Includes extends Model {
    /**
     * get all the menu elements
     * @return array
     */
    public function getMenu():array{
        //this shall be replaced by a model call
        $data = [
            'category1' => '/cat/1',
            'category2' => '/cat/2',
            'category3' => '/cat/3'
        ];

        return $data;
    }

    /**
     * Gets the Jumbotron elements
     * @return array
     */
    public function getJumbotron():array{
        $data = [];
        return $data;
    }

}