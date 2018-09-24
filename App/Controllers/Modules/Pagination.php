<?php
namespace App\Modules;

use Core\Constant;
use Core\Modules\Module;
use Core\Traits\StringFunctions;

class Pagination extends Module{

    use StringFunctions;

    public function getPagination(string $page, int $totalPosts):array
    {
        if(!$this->startsWith($page, "page-"))
        {
            throw new \Exception("Pagination Error", "404");
        }
        $pageNo = $this->removeFromBeginning($page, "page-");
        $offset = ($pageNo-1) * Constant::POSTS_PER_PAGE;
        $totalPages = ceil($totalPosts / Constant::POSTS_PER_PAGE);

        if($pageNo > $totalPages)
        {
            throw new \Error("Pagination Number not found", "404");
        }

        return array(
          "pageNo" => $pageNo,
          "offset" => $offset,
          "totalPages" => $totalPages
        );

    }
}