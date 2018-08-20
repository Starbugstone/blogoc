<?php

namespace Core;


/**
 * Class Csrf
 * @package Core
 */
class Csrf
{
    /**
     * @var Container dependency injector
     */
    private $container;

    /**
     * our session object
     * @var Dependency\Session|session
     */
    private $session;

    /**
     * On construct, we immediately set the CSRF token
     *
     * Csrf constructor.
     * @param Container $container
     *
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->session = $this->container->getSession();
        //Setting up csrf token security for all calls
        $this->setCsrf();
    }


    /**
     * We set our CSRF token if none is already set
     *
     */
    public function setCsrf():void
    {
        if (!$this->session->isParamSet('csrf_token')) {
            try {
                $rand = random_bytes(32);
                $hash = bin2hex($rand);
                $this->session->set('csrf_token', $hash);
            } catch (\Exception $e) {
                echo 'Random generator not present on server: ' . $e->getMessage();
            }
        }
    }

    /**
     * Gets the Csrf stored in the session
     * @return mixed
     */
    public function getCsrfKey()
    {
        return $this->session->get('csrf_token');
    }

    /**
     * Checks if the csrf_token passed in the header is the same as the token stored in the session
     *
     * @throws JsonException
     */
    public function checkCsrf():void
    {
        header('Content-Type: application/json');

        $headers = $this->container->getHeaders();

        if (!isset($headers['csrf_token'])) {
            throw new JsonException('No CSRF token.');
        }

        if ($headers['csrf_token'] !== $this->getCsrfKey()) {
            throw new JsonException('Wrong CSRF token.');
        }
    }
}