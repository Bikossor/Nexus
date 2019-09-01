<?php
require_once './core/libraries/Controller.php';
require_once './core/exceptions/ControllerException.php';

class Application
{
    public function __construct()
    { }

    public function run()
    {
        if (RewriteUrl) {
            $path = isset($_GET['uri']) ? explode('/', filter_var(rtrim($_GET['uri'], '/')), FILTER_SANITIZE_URL) : null;
        } else {
            $path = isset($_GET['uri']) ? $_GET['uri'] : null;
        }

        if (empty($path[0])) {
            require_once './core/controllers/IndexController.php';
            $controller = new IndexController();
            $controller->loadModel('index');
        } else {
            $nameController = $path[0] . 'Controller';
            $pathController = './core/controllers/' . $nameController . '.php';

            if (file_exists($pathController)) {
                require_once $pathController;
                $controller = new $nameController;
                $controller->loadModel($path[0]);
            } else {
                require_once './core/controllers/ErrorController.php';
                require_once "./Core/Enums/HttpStatus.php";

                $controller = new ErrorController();
                $controller->renderHttpStatus(HttpStatus::NOT_FOUND);

                return;
            }
        }

        if (isset($path[1])) {
            if (isset($path[2])) {
                $controller->{$path[1]}($path[2]);
            } elseif (method_exists($controller, $path[1])) {
                $controller->{$path[1]}();
            } else {
                require_once './core/controllers/ErrorController.php';
                require_once "./Core/Enums/HttpStatus.php";

                $controller = new ErrorController();
                $controller->renderHttpStatus(HttpStatus::NOT_FOUND);

                return;
            }
        }

        $controller->index();
    }
}
