<?php

namespace Core;


/**
 * Class AjaxController
 * @package Core
 *
 * out parent controller for all ajax calls
 *
 */
abstract class AjaxController extends Controller
{

    /**
     * The request object to handle all gets and posts
     * @var Dependency\Request|Request
     *
     */
    protected $request;

    /**
     * On construction, we imediatly check for security and bail out on the first sign of fraude
     * Only allow XmlHTTPRequests or throw an exception
     * Only allowed to call if Csrf token is valid or throw a json error
     * AjaxController constructor.
     * @param Container $container
     * @throws \Exception
     *
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->request = $container->getRequest(); //adding our request object as it will be needed in the ajax calls

        //we only allow xmlHTTPRequests here for security
        $this->checkXlmRequest();
        $this->checkReferer();
        $this->csrf->checkCsrf();
    }

    /**
     * Checks if we have an Xml Http request and throws an error if not
     * @throws \ErrorException
     */
    private function checkXlmRequest():void
    {
        if (!$this->request->isXmlRequest()) {
            throw new \ErrorException('Call not permitted', 404);
        }
    }

    /**
     * Check if the request is coming from the same domain as the base url of the site
     * @throws JsonException
     */
    private function checkReferer():void
    {

        $referer = $this->request->getReferer();
        $baseUrl = $this->request->getBaseUrl();
        $inUrl = strpos($referer, $baseUrl);
        if ($inUrl === false || $inUrl > 0) { //not at start of referer
            if ($referer !== null) {//the referer can be null with certain navigators, so don't block on that
                throw new JsonException('Illegal referer.');

            }

        }
    }

    /**
     * Construct our json reply message
     * @param null $message
     * @param int $code
     * @return string json encoded message
     */
    public function jsonResponse($message = null, $code = 200):string
    {
        // clear the old headers
        //header_remove(); //->this removes our csrf error checking so no go for the moment.
        // set the actual code
        http_response_code($code);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        // treat this as json
        header('Content-Type: application/json');
        $status = array(
            200 => '200 OK',
            400 => '400 Bad Request',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error'
        );
        // ok, validation error, or failure
        header('Status: ' . $status[$code]);
        // return the encoded json
        return json_encode(array(
            'status' => $code < 300, // success or not?
            'message' => $message
        ));
    }


}