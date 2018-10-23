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
        if ($this->isGet()) {
            return $_GET[$key] ?? null;
        }
        if ($this->isPost()) {
            return $_POST[$key] ?? null;
        }
        throw new \Exception("Unknown Request Method");
    }

    /**
     * gets the full data from a get or a post request
     * @return mixed
     * @throws \Exception
     */
    public function getDataFull()
    {
        if ($this->isGet()) {
            return $_GET ?? null;
        }
        if ($this->isPost()) {
            return $_POST ?? null;
        }
        throw new \Exception("Unknown Request Method");
    }

    /**
     * is the call a post
     * @return bool
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * is the call a get
     * @return bool
     */
    public function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * get the current uri for routing
     * @return mixed
     */
    public function getUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * checks if the request is a XML HTTP REQUEST
     * @return bool
     */
    public function isXmlRequest():bool
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
    public function getReferer():string
    {
        return $_SERVER['HTTP_REFERER'] ?? "";
    }

    /**
     * constructs the base url of the site
     * @return string
     */
    public function getBaseUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'];
        $https = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        return $https . '://' . $host . '/';
    }

    /**
     * Gettint the headers
     * @return array
     */
    public function getHeaders(): array
    {
        return apache_request_headers() ?: [];
    }

    /**
     * Getting the uploaded file
     * @return mixed
     */
    public function getUploadedFiles()
    {
        reset($_FILES);
        return current($_FILES);
    }

    /**
     * getting the server document root
     */
    public function getDocumentRoot():string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

}