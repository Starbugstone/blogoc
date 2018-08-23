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
        $contentType = 'Content-Type: ' . $headerType;

        header($contentType);
    }

    /**
     * redirects the user to a different page
     * @param string $url
     */
    public function redirect(string $url = ''): void
    {
        //if the url was passed with a forward slash, remove it as it will be added later.
        if ($url !== '') {
            if ($url[0] === '/') {
                $url = substr($url, 1);
            }
        }

        header("location: /" . $url);
        exit(); //after redirect do not execute anything else from the function
    }
}