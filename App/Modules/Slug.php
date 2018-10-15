<?php

namespace App\Modules;

use Cocur\Slugify\Slugify;
use Core\Modules\Module;

class Slug extends Module{
    public function isSlugValid(string $slug):bool
    {
        $slugify = new Slugify();
        $validSlug = $slugify->slugify($slug);
        if($slug !== $validSlug)
        {
            return false;
        }

        return true;
    }
}