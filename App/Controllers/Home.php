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

    public function index()
    {
        $Includes = new \App\Models\Includes($this->getContainer());
        $this->data['navigation'] = $Includes->getMenu();
        $this->data['jumbotron'] = $Includes->getJumbotron();
        $this->renderView('Home.twig', $this->data);
    }
}