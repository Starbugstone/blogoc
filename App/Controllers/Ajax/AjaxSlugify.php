<?php

namespace App\Controllers\Ajax;

use App\Models\SlugModel;
use Core\AjaxController;
use Cocur\Slugify\Slugify;
use Core\JsonException;
use Core\Traits\StringFunctions;

class AjaxSlugify extends AjaxController
{
    use StringFunctions;
    /**
     * @param string $string the string to slugify
     * @return string slugified string
     * @throws \Exception Cocur\Slugify\Slugify error
     */
    public function slugifyString()
    {
        //only admins can update the slug
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new JsonException('Call is not post');
        }
        $slug = $this->request->getData("slugText-update");
        $result = array();
        $slugify = new Slugify();
        $result['slug'] = $slugify->slugify($slug);
        echo json_encode($result);
    }
}