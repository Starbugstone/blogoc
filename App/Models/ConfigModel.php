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
        $sql = 'SELECT configs.idconfigs, configs.configs_name, configs.configs_value, configs_class.class as class  FROM blogoc.configs 
                inner join blogoc.configs_class on configs.configs_class_idconfigsclass = configs_class.idconfigsclass
                order by class;';
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
}