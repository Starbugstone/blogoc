<?php
namespace Core;

abstract class AdminController extends Controller{
    public function __construct(Container $container)
    {
        parent::__construct($container);

    }

    public function getUserLevel(){
        //get session level from the actual $_SESSION
        //we could use a binary system for the rights but not much granular levels to take care of
        $sessionLevel = 0;
        if($sessionLevel === 1){
            return 'User';
        }
        if($sessionLevel === 2){
            return 'Admin';
        }
        return 'Visitor'; //we should do a redirect to home with message popup. Need to implement popups
    }
}