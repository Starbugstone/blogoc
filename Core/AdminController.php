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
     * The request object to handle all gets and posts
     * @var Dependency\Request
     *
     */
    protected $request;

    /**
     * The response module to handle response messages
     * @var Dependency\Response
     */
    protected $response;

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

        $this->request = $container->getRequest(); //adding our request object as it will be needed in the ajax calls
        $this->response = $container->getResponse();

        //Sending the auth level to the data
        $this->data['userRole'] = $this->auth->getUserRole();
        $this->data['userLevel'] = $this->auth->getUserLevel();

    }

    protected function onlyAdmin()
    {
        if (!$this->auth->isAdmin()) {
            $this->alertBox->setAlert("Only admins can access this", 'error');
            $this->container->getResponse()->redirect('/admin');
        }
    }


}