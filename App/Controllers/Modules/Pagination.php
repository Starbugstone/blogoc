<?php

namespace App\Modules;

use Core\Constant;
use Core\Modules\Module;
use Core\Traits\StringFunctions;

class Pagination extends Module
{

    use StringFunctions;

    /**
     * gets the pagination and returns the required information to set up previous / next pages
     * @param string $page the page number in format "page-1"
     * @param int $totalRows the total number of rows
     * @param int $rowsPerPage the number of rows per page, by default, grabbed from the core constant file
     * @return array the page number, the offset and the page total.
     * @throws \Exception
     */
    public function getPagination(string $page, int $totalRows, int $rowsPerPage = Constant::POSTS_PER_PAGE): array
    {
        $page = strtolower($page);
        if (!$this->startsWith($page, "page-")) {
            throw new \Exception("Pagination Error", "404");
        }
        $pageNo = $this->removeFromBeginning($page, "page-");
        if(!is_int($pageNo)){
            throw new \Exception("Invalid page number");
        }
        $offset = ($pageNo - 1) * $rowsPerPage;
        $totalPages = ceil($totalRows / $rowsPerPage);

        if ($pageNo > $totalPages) {
            throw new \Error("Pagination Number not found", "404");
        }

        return array(
            "pageNo" => $pageNo,
            "offset" => $offset,
            "totalPages" => $totalPages
        );

    }
}