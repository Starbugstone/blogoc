<?php

namespace Core\Modules;
/**
 * Gets and sets the alert box message to be sent to the front
 * Class AlertBox
 * @package Core\Modules
 */
class AlertBox extends Module
{
    /**
     * @var array the diffrent allowed alert types
     */
    private $allowedTypes = [
        'success',
        'info',
        'warning',
        'error'
    ];

    /**
     * sets our alert message
     * @param string $message the alert message
     * @param string $type the type of alert
     * @throws \Exception
     */
    public function setAlert(string $message, $type = 'success')
    {
        //make sure we have the right type or throw an error
        try {
            if (!in_array($type, $this->allowedTypes)) {
                throw new \Exception("Invalid toastr alert type");
            }
        } catch (\Exception $e) {
            echo "<pre>alerter error :" . $e.'</pre>'; //TODO See how to handle better, perhaps with a custom error
            die();
        }

        $message = [
            'type' => $type,
            'message' => $message
        ];
        $session = $this->container->getSession();


        $alert = $session->get('alert_messages');


        $alert[] = $message;

        $session->set('alert_messages', $alert);

    }

    /**
     * Checks if we have any unsent alerts
     * @return bool
     */
    public function alertsPending()
    {
        $session = $this->container->getSession();

        return $session->isParamSet('alert_messages');
    }

    /**
     * grabs the pending alerts from teh session and returns the array
     *  It then empties the alerts to avoid showing the same alert twice
     * @return array
     */
    public function getAlerts(): array
    {
        if (!$this->alertsPending()) {
            return [];
        }
        $session = $this->container->getSession();
        $alerts = $session->get('alert_messages');
        //could return null and need to send back an array. this should never happen since we checked the alerts pending but scrutinizer complains !
        if(is_null($alerts)){
            $alerts = [];
        }
        $session->remove('alert_messages');
        return $alerts;


    }
}