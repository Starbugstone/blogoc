<?php

namespace App\Models;

use Core\Model;
use Core\Traits\StringFunctions;

class SlugModel extends Model
{
    use StringFunctions;

    /**
     * is the slug unique, used when updating the slug
     * @param string $slug the slug to search for
     * @param string $table the table to search in
     * @param string $columnName the name of the slug column
     * @return bool
     * @throws \ErrorException
     */
    public function isUnique(string $slug, string $table, string $columnName): bool
    {

        if (!$this->isAlphaNum($table)) {
            throw new \ErrorException("Invalid table name " . $table);
        }

        if (!$this->isAlphaNum($columnName)) {
            throw new \ErrorException("Invalid Column name " . $columnName);
        }

        $slugTbl = $this->getTablePrefix($table);

        $sql = "SELECT * FROM $slugTbl WHERE $columnName = :slug";
        $this->query($sql);
        $this->bind(':slug', $slug);
        $this->execute();
        return !$this->stmt->rowCount() > 0;
    }

    /**
     * get the ID of the row from the slug
     * @param string $slug the slug to search
     * @param string $table the table to search in
     * @param string $columnName the slug column name
     * @param string $idColumn the id column name
     * @return int the id of the row
     * @throws \ErrorException
     */
    public function getIdFromSlug(string $slug, string $table, string $columnName, string $idColumn): int
    {
        if (!$this->isAlphaNum($table)) {
            throw new \ErrorException("Invalid table name " . $table);
        }

        if (!$this->isAlphaNum($columnName)) {
            throw new \ErrorException("Invalid Slug Column name " . $columnName);
        }

        if (!$this->isAlphaNum($idColumn)) {
            throw new \ErrorException("Invalid ID Column name " . $columnName);
        }

        $slugTbl = $this->getTablePrefix($table);

        $sql = "SELECT $idColumn FROM $slugTbl WHERE $columnName = :slug";
        $this->query($sql);
        $this->bind(":slug", $slug);
        $this->execute();
        if (!$this->stmt->rowCount() > 0) {
            return 0;
        }
        return $this->stmt->fetchColumn();

    }
}