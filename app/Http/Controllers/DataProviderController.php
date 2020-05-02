<?php

namespace App\Http\Controllers;

class DataProviderController extends DataRequestController
{
    public function getUserType($cli)
    {
        $api = GET_USER_TYPE_API;
        $this->params = json_encode(array($cli, $api));
        $this->method = CALL_EXTERNAL_API;
        $this->getResponse(true);
        if (is_array($this->responseData) && isset($this->responseData[0][USER_TYPE_KEY])) {
            return $this->responseData[0][USER_TYPE_KEY];
        }
        return null;
    }

    public function generatePIN($paramArray)
    {
        $this->params = json_encode($paramArray); //array($cli, $plan, $ivr_id, $auth_code, $ip)
        $this->method = PIN_GENERATE_FUNCTION;
        $this->getResponse();
        if ($this->responseData == 1) {
            return true;
        }
        return false;
    }

    public function sendSms($cli, $text)
    {
        $did = '';
        $this->params = json_encode(array($cli, $did, $text));
        $this->method = SEND_SMS_FUNCTION;
        $this->getResponse();
        if ($this->responseData->res == "SUCCESS") {
            return true;
        }
        return false;
    }

    public function getUser($cli, $pin)
    {
        $this->params = json_encode(array($cli, $pin));
        $this->method = GET_USER_FUNCTION;
        $this->getResponse(true);
        if ($this->responseData == 0) {
            return null;
        }
        return reset($this->responseData);
    }

    public function getUserFromAuthCode($authCode)
    {
        $this->params = json_encode(array($authCode));
        $this->method = GET_USER_FROM_AUTH_CODE_FUNCTION;
        $this->getResponse(true);
        if ($this->responseData == 0) {
            return null;
        }
        return reset($this->responseData);
    }

    public function getDefaultAction($ivrId)
    {
        $this->params = json_encode(array($ivrId));
        $this->method = GET_DEFAULT_ACTION_FUNCTION;
        $this->getResponse(true);
        if ($this->responseData == 0) {
            return null;
        }
        return reset($this->responseData);
    }

    public function getFunctionName($action)
    {
        $this->params = json_encode(array($action));
        $this->method = GET_FUNCTION_NAME_FROM_ACTION;
        $this->getResponse();
        if ($this->responseData == 0) {
            return null;
        }
        return reset($this->responseData)->function;
    }

    public function getDefaultPageId($ivrId)
    {
        $this->params = json_encode(array($ivrId));
        $this->method = GET_DEFAULT_PAGE_ID;
        $this->getResponse();
        if (!empty($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

    public function getPageIdFromButton($buttonId)
    {
        $this->params = json_encode(array($buttonId));
        $this->method = GET_PAGE_ID_FROM_BUTTON;
        $this->getResponse();
        if (!empty($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

    public function getNavigationPage($navigationType)
    {
        $this->params = json_encode(array($navigationType));
        $this->method = GET_NAVIGATION_PAGE;
        $this->getResponse(true);
        if (is_array($this->responseData)) {
            return reset($this->responseData);
        }
        return null;
    }

    public function getPageData($pageId)
    {
        $this->params = json_encode(array($pageId));
        $this->method = GET_PAGE_DATA_FROM_PAGE_ID;
        $this->getResponse(true);
        if (is_array($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

    public function getPageElements($pageId)
    {
        $this->params = json_encode(array($pageId));
        $this->method = GET_PAGE_ELEMENTS_FROM_PAGE_ID;
        $this->getResponse(true);
        if (is_array($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

    public function getApiResult($cli, $apiUrl, $inputName = null, $inputValue = null)
    {
        $this->params = json_encode(array($cli, $apiUrl, $inputName, $inputValue));
        $this->method = EXTERNAL_API_CALL;
        $this->getResponse(true);
        if (is_array($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

    public function getComparingData($elementId)
    {
        $this->params = json_encode(array($elementId));
        $this->method = GET_COMPARING_DATA;
        $this->getResponse(true);
        if (is_array($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

    public function getApiKeyData($elementId)
    {
        $this->params = json_encode(array($elementId));
        $this->method = GET_API_KEY_DATA;
        $this->getResponse(true);
        if (is_array($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

    public function getElementsCalculations($elementId)
    {
        $this->params = json_encode(array($elementId));
        $this->method = GET_API_CALCULATION_DATA;
        $this->getResponse(true);
        if (is_array($this->responseData)) {
            return $this->responseData;
        }
        return null;
    }

}
