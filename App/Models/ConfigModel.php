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
    public function getAllConfig():array
    {
        $returnData = [];
        //Grabbing the configs class
        $sql = 'select * from configs_class order by class';
        $this->query($sql);
        $this->execute();
        $configClass = $this->stmt->fetchAll();

        //getting the configurations grouped by class
        $sql = "select idconfigs, configs_name, configs_value from configs where configs_class_idconfigsclass = :classId";
        $this->query($sql);

        foreach ($configClass as $class) {
            //we remove the first 3 characters as they are used for ordering (10_global_site_configuration)
            $className = substr($class->class, 3);

            $this->bind(':classId', $class->idconfigsclass);
            $this->execute();
            $returnData[$className] = $this->stmt->fetchAll();
        }
        return $returnData;
    }
}