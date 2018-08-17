<?php

namespace Core;



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
    public function setCsrf()
    {
        if (!$this->session->isParamSet('csrf_token')) {
            try {
                $rand = random_bytes(32);
            } catch (\Exception $e) {
                echo 'Random generator not present on server: ' . htmlspecialchars($e->getMessage());
            }
            $hash = bin2hex($rand);
            $this->session->set('csrf_token', $hash);
        }
    }

    /**
     * Gets the Csrf stored in the session
     * @return mixed
     */
    public function getCsrf()
    {
        return $this->session->get('csrf_token');
    }

    /**
     * Checks if the csrf_token passed in the header is the same as the token stored in the session
     *
     * @throws error json if no csrf token is found or the token is wrong
     */
    public function checkCsrf()
    {
        header('Content-Type: application/json');

        $headers = apache_request_headers();

        if (!isset($headers['csrf_token'])) {
            exit(json_encode(['error' => 'No CSRF token.']));
        }

        if ($headers['csrf_token'] !== $this->getCsrf()) {
            exit(json_encode(['error' => 'Wrong CSRF token.']));
        }
    }
}