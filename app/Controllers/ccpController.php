<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Views;

class ccpController extends Controller{

    public function __construct() {}

    public static function index($params)
    {

    }

    public static function getMethods()
    {
        return ["index"];
    }

}