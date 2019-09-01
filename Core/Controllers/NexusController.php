<?php
class NexusController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    { }

    public function GetContent()
    {
        $this->model->GetContent();
    }
}
