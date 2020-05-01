<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public $dataProvider;
    public $dataLogger;
    public $response;
    public $responseManager;
    public $cache;
}
