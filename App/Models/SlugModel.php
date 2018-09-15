<?php

namespace App\Models;

use Core\Model;

class SlugModel extends Model
{


    public function isUnique(string $slug, string $table, string $columnName)
    {

        if (!preg_match("/^[A-Za-z0-9_-]+$/", $table)) {
            throw new \ErrorException("Invalid table name " . $table);
        }

        if (!preg_match("/^[A-Za-z0-9_-]+$/", $columnName)) {
            throw new \ErrorException("Invalid Column name " . $columnName);
        }

        $slugTbl = $this->getTablePrefix($table);

        $sql = "SELECT * FROM $slugTbl WHERE $columnName = :slug";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        return !$stmt->rowCount() > 0;

    }
}