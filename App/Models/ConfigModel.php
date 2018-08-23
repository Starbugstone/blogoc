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
    public function getAllConfig(): array
    {
        $returnData = [];
        //getting our tables
        $configsTbl = $this->getTablePrefix('configs');
        $configsClassTbl = $this->getTablePrefix('configs_class');
        $sql = "SELECT idconfigs, configs_name, configs_value, class FROM  $configsTbl 
                inner join $configsClassTbl on $configsTbl.configs_class_idconfigsclass = $configsClassTbl.idconfigsclass
                order by class;";

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

    public function updateConfig(int $id, string $param){
        echo $id.' - '.$param.'<br>';
        //TODO need to push to sql
    }
}