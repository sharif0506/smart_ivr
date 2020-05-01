<?php

namespace App\Http\Controllers;

class InputElementController extends ElementController
{
    public function __construct($elementData, $cli, $language)
    {
        parent::__construct($elementData, $cli, $language);
    }
}
