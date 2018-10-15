<?php

namespace App\Modules;

use Core\Container;
use Core\Modules\Module;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class SendMail extends Module{

    private $siteConfig;

    private $mailer;
    private $transport;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $config = new SiteConfig($this->container);
        $this->siteConfig = $config->getSiteConfig();

        // Create the Transport for mail sending
        //$config = $this->siteConfig->getSiteConfig();
        $this->transport = (new Swift_SmtpTransport($this->siteConfig["SMTP_server"], (int)$this->siteConfig["SMTP_port"]))
            ->setUsername($this->siteConfig["SMTP_user"])
            ->setPassword($this->siteConfig["SMTP_pass"])
        ;

        // Create the Mailer using your created Transport
        $this->mailer = new Swift_Mailer($this->transport);

    }

    /**
     * Send an Email
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return int
     */
    public function send(string $to, string $subject, string $message)
    {
        // Create a message
        $message = (new Swift_Message($subject))
            ->setFrom([$this->siteConfig["SMTP_from"]])
            ->setTo([$to])
            ->setBody($message)
        ;

        // Send the message
        return $this->mailer->send($message);
    }

    /**
     * sent the reset password mail
     * @param string $to
     * @param string $token
     * @param int $userId
     */
    public function sendResetPasswordMail(string $to, string $token, int $userId)
    {
        $url = $this->container->getRequest()->getBaseUrl();
        $url .= "password/reset/get?token=".$token;
        $url .= "&userId=".$userId;

        $message = "<a href=\"".$url."\">Define new password</a>";

        $this->send($to, "Define Password", $message );

    }
}