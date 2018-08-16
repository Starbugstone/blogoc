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
     */
    public function getData($key){
        return $_GET[$key] ?? null;
    }
}