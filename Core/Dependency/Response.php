<?php

namespace Core\Dependency;

/**
 * Creating all the response messages
 * Class Response
 * @package Core\Dependency
 */
class Response
{

    /**
     * Setting the header type. useful for Json returns
     * @param string $type
     */
    public function setHeaderContentType(string $type)
    {
        switch ($type) {
            case 'json':
                $headerType = 'application/json';
                break;
            default:
                $headerType = 'text/html';
        }
        $contentType = 'Content-Type: '.$headerType;

        header($contentType);
    }

    public function redirect(string $url = ''): void
    {
        header("location: /".$url);
        die(); //after redirect do not execute anything else from the function
    }
}