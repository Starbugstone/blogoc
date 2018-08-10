<?php
namespace App\Models;

use Core\Model;

class Debug extends Model{
    public function getClass(){
        $this->test();
    }
}