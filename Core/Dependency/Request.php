<?php

namespace Core\Dependency;

/**
 * Class Request
 * @package Core
 *
 * we are dealing with get, post and cookies here, all Superglobals
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class Request
{

    /**
     * gets the data from a get or a post request
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function getData($key)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'GET') {
            return $_GET[$key] ?? null;
        }
        if ($requestMethod === 'POST') {
            return $_POST[$key] ?? null;
        }
        throw new \Exception("Unknown Request Method");
    }

    /**
     * checks if the request is a XML HTTP REQUEST
     * @return bool
     */
    public function isXmlRequest()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        return false;
    }

    /**
     * gets the referer of the request
     * @return string|null
     */
    public function getReferer()
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * constructs the base url of the site
     * @return string
     */
    public function getBaseUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'];
        $https = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        return $https.'://'.$host.'/';
    }

    /**
     * Gettint the headers
     * @return array
     */
    public function getHeaders(): array
    {
        if (apache_request_headers() != false) {
            return apache_request_headers();
        }
        return [];
    }

}