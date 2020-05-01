<?php

namespace App\Http\Controllers;

class ParagraphElementController extends ElementController
{
    public function __construct($elementData, $cli, $language)
    {
        parent::__construct($elementData, $cli, $language);
        $this->dataProvider = new DataProviderController();
        $this->formElementData();
        $this->unsetElementProperties();
    }

    private function formElementData()
    {
        if (!empty($this->apiUrl)) {
            $this->apiResponse = $this->dataProvider->getApiResult($this->cli, $this->apiUrl);
            if (is_array($this->apiResponse)) {
                $this->apiResponse = reset($this->apiResponse);
                if (strpos($this->displayTextBn, DYNAMIC_TEXT) !== false || strpos($this->displayTextEn, DYNAMIC_TEXT) !== false) {
                    $this->replaceDynamicData();
                } else {
                    $this->getCalculatedData();
                }
            } else {
                $this->hasError = true;
                return;
            }
        }
    }

    private function replaceDynamicData()
    {
        $this->getDynamicValue();
        $this->displayTextEn = str_replace(DYNAMIC_TEXT, $this->calculationResult, $this->displayTextEn);
        $this->displayTextBn = str_replace(DYNAMIC_TEXT, $this->calculationResult, $this->displayTextBn);
        $this->displayTextBn = $this->replaceBengaliDigits($this->displayTextBn);

    }

    private function getDynamicValue()
    {
        $this->comparingData = $this->dataProvider->getComparingData($this->elementId);
        if (is_array($this->comparingData)) {
            $this->checkComparingData();
            if (!$this->comparingResult) {
                $this->hasError = true;
                return;
            }
        }
        $this->getCalculatedData();
        if ($this->calculationResult == null) {
            $this->hasError = true;
        }
    }

    private function getCalculatedData()
    {
        $this->calculations = $this->dataProvider->getElementsCalculations($this->elementId);
        $this->keyList = $this->dataProvider->getApiKeyData($this->elementId);
        $this->calculationResult = null;
        foreach ($this->keyList as $keyData) {
            foreach ($this->calculations as $calculation) {
                $calculation = trim($calculation['calculation']);
                if (strpos($calculation, "<RV>") !== false) {
                    $calculation = str_replace("<RV>", $this->apiResponse[$keyData["response_key"]], $calculation);
                } else {
                    $calculation = str_replace("<" . $keyData['response_key'] . ">", $this->apiResponse[$keyData["response_key"]], $calculation);
                }
                $this->calculationResult = eval("return ($calculation);");
            }
        }
    }

}
