<?php
namespace Core;

/**
 * Class View
 * @package Core
 */

class View{
    /**
     * render view using twig
     *
     * @param string $template the template file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * return @void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function renderTemplate($template, $args = []):void{
        static $twig = null;
        if ($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__).'/App/Views');
            $twig = new \Twig_Environment($loader);
        }

        echo $twig->render($template, $args);
    }

}