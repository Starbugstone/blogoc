<?php
namespace App\Models;

use Core\Model;

class Debugs extends Model{
    public function getClass(){
        $this->test();
    }
}