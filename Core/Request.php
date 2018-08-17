<?php
namespace Core;

/**
 * Class Request
 * @package Core
 *
 * we are dealing with get, post and cookies here, all Superglobals
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class Request{
    /**
     * @param $key
     * @return null
     * @throws \Exception
     */
    public function getData($key){
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if($requestMethod === 'GET')
        {
            return $_GET[$key] ?? null;
        }
        if($requestMethod === 'POST')
        {
            return $_POST[$key] ?? null;
        }
        throw new \Exception("Unknown Request Method");
    }

    public function isXmlRequest(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            return true;
        }
        return false;
    }
}