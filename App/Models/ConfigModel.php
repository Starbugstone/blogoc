<?php

namespace App\Models;

use Core\Container;
use Core\Model;

/**
 * Class ConfigModel
 * @package App\Models
 */
class ConfigModel extends Model
{

    private $configsTbl;
    private $configsClassTbl;
    private $configTypeTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->configsTbl = $this->getTablePrefix('configs');
        $this->configsClassTbl = $this->getTablePrefix('configs_class');
        $this->configTypeTbl = $this->getTablePrefix('configs_type');
    }

    /**
     * Returns all the configs orderd with their class names
     * @return array
     * @throws \Exception
     */
    public function getAllConfigOrdered(): array
    {
        $returnData = [];
        //getting our tables

        $sql = "SELECT idconfigs, configs_name, configs_type_name, configs_value, class FROM  $this->configsTbl 
                INNER JOIN $this->configsClassTbl ON $this->configsTbl.configs_class_idconfigsclass = $this->configsClassTbl.idconfigsclass 
                INNER JOIN $this->configTypeTbl ON $this->configsTbl.configs_type_idconfigs_type = $this->configTypeTbl.idconfigs_type
                ORDER BY class, $this->configsTbl.order, configs_name;";

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
        $sql = "UPDATE $this->configsTbl SET configs_value = :param WHERE idconfigs = :id";
        $this->query($sql);
        $this->bind(':param', $param);
        $this->bind(':id', $idTable);
        return $this->finalExecute();

    }
}