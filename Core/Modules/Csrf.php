<?php

namespace Core\Modules;

use Core\Container;
use Core\JsonException;

/**
 * Class Csrf
 * @package Core
 */
class Csrf extends Module
{
    /**
     * our session object
     * @var \Core\Dependency\Session
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
        parent::__construct($container);
        $this->session = $this->container->getSession();
        //Setting up csrf token security for all calls
        $this->setCsrf();
    }


    /**
     * We set our CSRF token if none is already set
     *
     */
    public function setCsrf(): void
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
     * Checks if the csrf_token passed in the header via JSON is the same as the token stored in the session
     *
     * @throws JsonException
     */
    public function checkJsonCsrf(): void
    {

        $this->container->getResponse()->setHeaderContentType('json');

        $headers = $this->container->getRequest()->getHeaders();

        if (!isset($headers['Csrftoken'])) {
            throw new JsonException('No CSRF token.');
        }

        if ($headers['Csrftoken'] !== $this->getCsrfKey()) {
            throw new JsonException('Wrong CSRF token.');
        }
    }
}