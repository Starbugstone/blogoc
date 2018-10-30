<?php

namespace App\Modules;

use Core\Container;
use Core\Modules\Module;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class SendMail extends Module
{

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
        $this->transport = (new Swift_SmtpTransport($this->siteConfig["SMTP_server"],
            (int)$this->siteConfig["SMTP_port"]))
            ->setUsername($this->siteConfig["SMTP_user"])
            ->setPassword($this->siteConfig["SMTP_pass"]);

        // Create the Mailer using your created Transport
        $this->mailer = new Swift_Mailer($this->transport);
    }

    /**
     * Send an Email
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string|null $from
     * @return int
     */
    public function send(string $to, string $subject, string $message, string $from = null)
    {
        // Create a message
        $message = (new Swift_Message($subject))
            ->setTo([$to])
            ->setBody($message, 'text/html');

        if ($from === null) {
            //if we haven't set a from, get the config value
            $from = $this->siteConfig["SMTP_from"];
        }

        $message->setFrom([$from]);
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
        $url .= "password/reset/get?token=" . $token;
        $url .= "&userId=" . $userId;

        $message = "<h1>Message from <a href='" . $this->container->getRequest()->getBaseUrl() . "'>" . $this->siteConfig["site_name"] . "</a></h1>";
        $message .= "<p>You have asked to reset your password, please click <a href=\"" . $url . "\">Here</a> to define a new password</p>";

        $this->send($to, "Define New Password", $message);
    }

    /**
     * sent the reset password mail
     * @param string $to
     * @param string $token
     * @param int $userId
     */
    public function sendNewPasswordMail(string $to, string $token, int $userId)
    {
        $url = $this->container->getRequest()->getBaseUrl();
        $url .= "password/reset/get?token=" . $token;
        $url .= "&userId=" . $userId;
        $message = "<h1>Message from <a href='" . $this->container->getRequest()->getBaseUrl() . "'>" . $this->siteConfig["site_name"] . "</a></h1>";
        $message .= "<h2>Welcome to the site</h2>";
        $message .= "<p>You have sucsessfuly created an account, now all you need to do is <a href=\"" . $url . "\">Create your new password</a></p>";
        $message .= "<p>Have fun</p>";

        $this->send($to, "Define Password at " . $this->siteConfig["site_name"], $message);
    }
}