<?php
require_once "./Core/Enums/HttpStatus.php";

class ErrorController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function renderHttpStatus(int $statusCode): void
    {
        switch ($statusCode) {
            default:
            case HttpStatus::NOT_FOUND:
                header("HTTP/1.1 404 Not Found");
                $this->view->render('error/404');
                break;
        }
    }
}
