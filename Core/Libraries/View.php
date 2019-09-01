<?php
class View
{
    public function __construct()
    {
        require_once './core/exceptions/ViewException.php';
    }

    public function render($viewName)
    {
        $viewPath = './core/views/' . $viewName . '.phtml';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new ViewException("View \"$viewName\" doesn't exist!");
        }
    }
}
