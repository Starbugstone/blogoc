<?php

namespace App\Controllers;

/**
 * Class Home
 *
 * The home page
 *
 * @package App\Controllers
 */
class Home extends \Core\Controller
{

    /**
     * Home constructor. This will grab all the includes and store in appropriate arrays
     */
    public function __construct()
    {
        parent::__construct();
        $Includes = new \App\Models\Includes();
        $this->data['navigation'] = $Includes->getMenu();
        $this->data['jumbotron'] = $Includes->getJumbotron();
    }

    public function index()
    {

        $this->view->renderTemplate('Home.twig', $this->data);
    }
}