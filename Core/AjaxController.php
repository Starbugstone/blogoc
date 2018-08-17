<?php
namespace Core;



abstract class AjaxController extends Controller{

    protected $request;


    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->request = $container->getRequest(); //adding our request object as it will be needed in the ajax calls
        //we only allow xmlHTTPRequests here for security
        if(!$this->request->isXmlRequest()){
            throw new \Exception('Call not permitted');
        }
    }

}