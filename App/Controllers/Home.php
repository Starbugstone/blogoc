<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\UserModel;
use Core\Config;
use Core\Container;
use Core\Traits\StringFunctions;

/**
 * Class Home
 *
 * The home page
 *
 * @package App\Controllers
 */
class Home extends \Core\Controller
{

    use StringFunctions;

    protected $siteConfig;
    protected $sendMail;

    private $config;
    private $userModel;
    private $postModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'SendMail';
        parent::__construct($container);

        $this->config = $this->siteConfig->getSiteConfig();
        $this->userModel = new UserModel($this->container);
        $this->postModel = new PostModel($this->container);
        if($this->auth->isuser())
        {
            $this->data["user"] = $this->userModel->getUserDetailsById((int)$this->session->get("userId"));
        }
    }

    /**
     * test the capcha
     * @param string $gCapchaResponse
     * @return bool
     */
    private function testCapcha(string $gCapchaResponse):bool
    {
        $error = false;
        if(Config::GOOGLE_RECAPCHA_PUBLIC_KEY !== "" && Config::GOOGLE_RECAPCHA_SECRET_KEY !== "")
        {
            if(empty($gCapchaResponse))
            {
                $error = true;
                $this->alertBox->setAlert('Capcha not set', 'error');
            }
            //check the capcha
            $grequest = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.Config::GOOGLE_RECAPCHA_SECRET_KEY.'&response='.$gCapchaResponse);
            // The result is in a JSON format. Decoding..
            $gresponse = json_decode($grequest);
            if(!$gresponse->success)
            {
                $error = true;
                $this->alertBox->setAlert('Capcha Error', 'error');
            }
        }
        return $error;
    }

    /**
     * Show the front page
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index()
    {
        $frontPosts = $this->postModel->getFrontPosts();

        $this->data['configs'] = $this->config;
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data['jumbotron'] = true;
        $this->data['front_posts'] = $frontPosts;


        //check if have prefilled form data and error messages
        $this->data["contactInfo"] = $this->session->get("contactInfo");
        $this->data["contactErrors"] = $this->session->get("contactErrors");

        //remove the set data as it is now sent to the template
        $this->session->remove("contactInfo");
        $this->session->remove("contactErrors");


        $this->renderView('Home');
    }

    public function contact()
    {

        $this->data['configs'] = $this->config;
        $this->data['navigation'] = $this->siteConfig->getMenu();

        //check if have prefilled form data and error messages
        $this->data["contactInfo"] = $this->session->get("contactInfo");
        $this->data["contactErrors"] = $this->session->get("contactErrors");

        //remove the set data as it is now sent to the template
        $this->session->remove("contactInfo");
        $this->session->remove("contactErrors");

        $this->renderView('Contact');
    }


    /**
     * Send the contact form with error checking
     * @throws \Exception
     */
    public function sendContactForm()
    {
        $this->onlyPost();

        //verify input values (html special chars ?)
        $to = $this->config["admin_email_address"];
        $message = $this->request->getDataFull();

        //Error checking

        //check all the fields
        $error = false;
        $contactErrors = new \stdClass();

        if ($message["contactName"] == "") {
            $error = true;
            $contactErrors->contactName = "Name must not be empty";
        }
        if ($message["contactEmail"] == "") {
            $error = true;
            $contactErrors->contactEmail = "Email must not be empty";
        }
        if ($message["contactSubject"] == "") {
            $error = true;
            $contactErrors->contactSubject = "Subject must not be empty";
        }
        if ($message["contactMessage"] == "") {
            $error = true;
            $contactErrors->contactMessage = "Message must not be empty";
        }
        if (!$this->isEmail($message["contactEmail"])) {
            $error = true;
            $contactErrors->contactEmail = "email is not valid";
        }

        $capchaError = $this->testCapcha($message["g-recaptcha-response"]);

        if($capchaError === true)
        {
            $error = true;
        }

        //If we found an error, return data to the register form and no create
        if ($error) {
            $this->session->set("contactInfo", $message);
            $this->session->set("contactErrors", $contactErrors);
            $this->response->redirect("/home/contact");
        }

        $config = $this->siteConfig->getSiteConfig();

        //from here all is good, send mail
        $userName = htmlspecialchars($message["contactName"]);
        $subject = "Contact from ".$config["site_name"]." : ";
        $subject .= htmlspecialchars($message["contactSubject"]);
        $textMessage = "<h1>message sent by ".$userName."</h1>";
        $textMessage .= "<p>from : <a href='mailto:".$message["contactEmail"]."'>".$message["contactEmail"]."</a></p>";
        $textMessage .= htmlspecialchars($message["contactMessage"]);
        $from = $config["SMTP_from"];

        $this->sendMail->send($to, $subject, $textMessage, $from);

        $this->alertBox->setAlert('Email sent');
        $this->response->redirect();
    }
}