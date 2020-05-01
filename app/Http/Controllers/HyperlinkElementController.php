<?php

namespace App\Http\Controllers;

class HyperlinkElementController extends ElementController
{
    public function __construct($elementData, $cli, $language)
    {
        parent::__construct($elementData, $cli, $language);
        $this->setUrl();
        $this->unsetElementProperties();
    }

    private function setUrl()
    {
        $this->elementValue = json_decode($this->elementValue, true);
        $url = new \stdClass();
        $url->{ENGLISH} = $this->elementValue[ENGLISH_WEB_KEY];
        $url->{BENGALI} = $this->elementValue[BENGALI_WEB_KEY];
        $this->elementValue = $url;
    }
}
