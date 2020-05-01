<?php

namespace App\Http\Controllers;

class ElementFactoryController
{
    private $elementData;
    private $language;
    private $cli;

    public function __construct($elementData, $cli, $language)
    {
        $this->elementData = $elementData;
        $this->cli = $cli;
        $this->language = $language;
    }

    public function getElement()
    {
        $element = null;
        if ($this->elementData['type'] == ELEMENT_TYPE_PARAGRAPH) {
            $element = new ParagraphElementController($this->elementData, $this->cli, $this->language);
        } elseif ($this->elementData['type'] == ELEMENT_TYPE_BUTTON) {
            $element = new ButtonElementController($this->elementData, $this->cli, $this->language);
        } elseif ($this->elementData['type'] == ELEMENT_TYPE_TABLE) {
            $element = new TableElementController($this->elementData, $this->cli, $this->language);
        } elseif ($this->elementData['type'] == ELEMENT_TYPE_HYPERLINK) {
            $element = new HyperlinkElementController($this->elementData, $this->cli, $this->language);
        } elseif ($this->elementData['type'] == ELEMENT_TYPE_INPUT) {
            $element = new InputElementController($this->elementData, $this->cli, $this->language);
        }

        if($element->hasError){
            $element = new ErrorElementController($this->language);
            $element = $element->getApiErrorMsg();
        }
        return $element;
    }
}
