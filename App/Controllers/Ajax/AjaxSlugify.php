<?php

namespace App\Controllers\Ajax;

use Core\AjaxController;
use Cocur\Slugify\Slugify;

class AjaxSlugify extends AjaxController
{
    /**
     * @param string $string the string to slugify
     * @return string slugified string
     * @throws \Exception Cocur\Slugify\Slugify error
     */
    public function slugifyString()
    {
        $string = $this->request->getData("slugText-update");
        $result = array();
        $slugify = new Slugify();
        $result['slug'] = $slugify->slugify($string);
        echo json_encode($result);
    }
}