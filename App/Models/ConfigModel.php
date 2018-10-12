<?php

namespace App\Models;

use Core\Model;

/**
 * Class ConfigModel
 * @package App\Models
 */
class ConfigModel extends Model
{
    /**
     * Returns all the configs orderd with their class names
     * @return array
     * @throws \Exception
     */
    public function getAllConfigOrdered(): array
    {
        $returnData = [];
        //getting our tables
        $configsTbl = $this->getTablePrefix('configs');
        $configsClassTbl = $this->getTablePrefix('configs_class');
        $configTypeTbl = $this->getTablePrefix('configs_type');
        $sql = "SELECT idconfigs, configs_name, configs_type_name, configs_value, class FROM  $configsTbl 
                INNER JOIN $configsClassTbl ON $configsTbl.configs_class_idconfigsclass = $configsClassTbl.idconfigsclass 
                INNER JOIN $configTypeTbl ON $configsTbl.configs_type_idconfigs_type = $configTypeTbl.idconfigs_type
                ORDER BY class, $configsTbl.order, configs_name;";

        $this->query($sql);
        $this->execute();
        $configClass = $this->fetchAll();
        foreach ($configClass as $class) {
            //we remove the first 3 characters as they are used for ordering (10_global_site_configuration)
            $className = substr($class->class, 3);

            if (!isset($returnData[$className])) {
                $returnData[$className] = [];
            }
            $returnData[$className][] = $class;
        }
        return $returnData;
    }

    /**
     * gets the entire config table
     * @return array
     * @throws \ReflectionException
     */
    public function getAllConfig()
    {
        return $this->getResultSet();
    }

    /**
     * updates the site config table
     * @param int $idTable
     * @param string $param parameter to update
     * @return bool update success
     * @throws \Exception error
     */
    public function updateConfig(int $idTable, string $param): bool
    {
        $configsTbl = $this->getTablePrefix('configs');
        $sql = "UPDATE $configsTbl SET configs_value = :param WHERE idconfigs = :id";
        $this->query($sql);
        $this->bind(':param', $param);
        $this->bind(':id', $idTable);
        return $this->execute();

    }
}