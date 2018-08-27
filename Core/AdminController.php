<?php

namespace Core;

/**
 * The parent controller for all admin section controllers
 * Class AdminController
 * @package Core
 */
abstract class AdminController extends Controller
{
    /**
     * Out placeholders for modules
     * @var object
     */
    protected $auth;
    protected $alertBox;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'Auth';
        $this->loadModules[] = 'AlertBox';
        parent::__construct($container);

        //Sending the auth level to the data
        $this->data['userRole'] = $this->auth->getUserRole();
        $this->data['userLevel'] = $this->auth->getUserLevel();
    }

    protected function onlyAdmin(){
        if(!$this->auth->isAdmin()){
            $this->alertBox->setAlert("Only admins can access this", 'error');
            $this->container->getResponse()->redirect('/admin');
        }
    }


}