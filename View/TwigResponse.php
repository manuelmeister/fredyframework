<?php
/**
 * Created by PhpStorm.
 * User: Robin
 * Date: 27.03.14
 * Time: 23:49
 */

namespace View;


abstract class TwigResponse extends Response {

    /**
     * @var \Twig_TemplateInterface
     */
    protected  $view;

    /**
     * @var array
     */
    protected $variables = [];

    public function __construct($templatePath){
        $loader = new \Twig_Loader_Filesystem('View/Templates');
        $twig = new \Twig_Environment($loader);
        $this->view = $twig->loadTemplate($templatePath);
    }

    public function setTwigVariables($variables){
        $this->variables = array_merge($this->variables, $variables);
    }

    public function render(){
        echo $this->view->render($this->variables);
    }
}