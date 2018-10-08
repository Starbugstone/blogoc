<?php

namespace App\Models;

use Core\Model;

class UserModel extends Model
{

    /**
     * Get all the data about a user for posts (Author).
     * @param int $authorId
     * @return array
     * @throws \ReflectionException
     */
    public function getAuthorDetails(int $authorId)
    {
        return $this->getRowById($authorId);
    }
}