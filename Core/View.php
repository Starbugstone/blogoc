<?php
namespace Core;
/*
 * View
 */

class View{
    /*
     * render view using twig
     *
     * @params string $template  the template file
     * @params array $args  Associative array of data to display in the view (optional)
     *
     * return @void
     */

    public static function renderTemplate($template, $args = []):void{
        static $twig = null;
        if ($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__).'/App/Views');
            $twig = new \Twig_Environment($loader);
        }

        echo $twig->render($template, $args);
    }

    public static function returnTemplate($template, $args = []){
        static $twig = null;
        if ($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__).'/App/Views');
            $twig = new \Twig_Environment($loader);
        }

        return $twig->render($template, $args);
    }
}