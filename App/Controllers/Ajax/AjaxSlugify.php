<?php

namespace App\Controllers\Ajax;

use Core\AjaxController;
use Cocur\Slugify\Slugify;
use Core\JsonException;
use Core\Traits\StringFunctions;
use Core\Container;

class AjaxSlugify extends AjaxController
{
    use StringFunctions;

    private $slugify;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->slugify = new Slugify();
    }

    /**
     * @return string slugified string
     * @throws JsonException
     */
    public function slugifyString()
    {
        //only admins can update the slug
        $this->onlyAdmin();
        $this->onlyPost();
        $slug = $this->request->getData("slugText-update");
        $result = array();
        $result['slug'] = $this->slugify->slugify($slug);
        echo json_encode($result);
    }
}