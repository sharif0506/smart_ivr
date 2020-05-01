<?php

namespace App\Http\Controllers;

class ButtonElementController extends ElementController
{
    public function __construct($elementData, $cli, $language)
    {
        parent::__construct($elementData, $cli, $language);
        $this->unsetElementProperties();
    }

}
