<?php

namespace App\Models;

use Core\Model;

class SlugModel extends Model
{


    public function isUnique(string $slug, string $table, string $columnName)
    {
        $slugTbl = $this->getTablePrefix($table);

        //TODO verify vars

        $sql = "SELECT * FROM $slugTbl WHERE $columnName = :slug";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        return !$stmt->rowCount() > 0;

    }
}