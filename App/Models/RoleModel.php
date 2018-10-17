<?php

namespace App\Models;

use Core\Constant;
use Core\Container;
use Core\Model;

class RoleModel extends Model{

    private $roleTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->roleTbl = $this->getTablePrefix("roles");
    }

    /**
     * gets the list of all the roles
     * @return array
     * @throws \ReflectionException
     */
    public function getRoleList()
    {
        return $this->getResultSet();
    }
}