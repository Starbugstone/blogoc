<?php

namespace App\Models;

use Core\Model;

class UserModel extends Model{

    public function getAuthorDetails(int $authorId)
    {
        return $this->getRowById($authorId);
    }
}