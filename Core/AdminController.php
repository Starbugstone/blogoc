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
    protected $alertBox;

    public function __construct(Container $container)
    {

        $this->loadModules[] = 'AlertBox';
        parent::__construct($container);

        //Sending the auth level to the data
        $this->data['userRole'] = $this->auth->getUserRole();
        $this->data['userLevel'] = $this->auth->getUserLevel();

    }

    /**
     * Only allow admin
     */
    protected function onlyAdmin()
    {
        if (!$this->auth->isAdmin()) {
            $this->alertBox->setAlert("Only admins can access this", 'error');
            $this->container->getResponse()->redirect('/admin');
        }
    }

    /**
     * Only allow post messages
     */
    protected function onlyPost()
    {
        //is post
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('/admin');
        }
    }


}