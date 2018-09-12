<?php
namespace App\Models;

use Core\Model;

class TagsModel extends Model{
    public function getTags(): array
    {
        return $this->getResultSet('tags');
    }
}