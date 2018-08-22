<?php

namespace App\Controllers;

use App\Models\IncludesModel;

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
        $Includes = new IncludesModel($this->container);
        $this->data['navigation'] = $Includes->getMenu();
        $this->data['jumbotron'] = $Includes->getJumbotron();
        $this->renderView('Home');
    }
}