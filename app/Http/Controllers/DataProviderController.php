<?php

namespace App\Http\Controllers;

class DataProviderController extends DataRequestController
{
    private $params;
    private $method;

    private function getResponse($isArray = false)
    {
        $requestData = array("method" => $this->method, "params" => $this->params);
        $this->responseData = $this->dataRequest($requestData);
        $this->responseData = json_decode($this->responseData, $isArray);
    }

    public function getUserType($cli)
    {
        $api = GET_USER_TYPE_API;
        $params = json_encode(array($cli, $api));
        $method = CALL_EXTERNAL_API;
        $requestData = array("method" => $method, "params" => $params);
        $this->responseData = json_decode($this->dataRequest($requestData), true);
        if (is_array($this->responseData) && isset($this->responseData[0][USER_TYPE_KEY])) {
            return $this->responseData[0][USER_TYPE_KEY];
        }
        return null;
    }

    public function generatePIN($paramArray)
    {
        $params = json_encode($paramArray); //array($cli, $plan, $ivr_id, $auth_code, $ip)
        $method = PIN_GENERATE_FUNCTION;
        $this->responseData = $this->dataRequest(array("method" => $method, "params" => $params));
        $this->responseData = json_decode($this->responseData);
        if ($this->responseData == 1) {
            return true;
        }
        return false;
    }

    public function sendSms($cli, $text)
    {
        $did = '';
        $params = json_encode(array($cli, $did, $text));
        $method = SEND_SMS_FUNCTION;
        $requestData = array("method" => $method, "params" => $params);
        $this->responseData = json_decode($this->dataRequest($requestData));
        if ($this->responseData->res == "SUCCESS") {
            return true;
        }
        return false;
    }

    public function getUser($cli, $pin)
    {
        $params = json_encode(array($cli, $pin));
        $method = GET_USER_FUNCTION;
        $this->responseData = $this->dataRequest(array("method" => $method, "params" => $params));
        $this->responseData = json_decode($this->responseData, true);
        if ($this->responseData == 0) {
            return null;
        }
        return reset($this->responseData);
    }

    public function getUserFromAuthCode($authCode)
    {
        $params = json_encode(array($authCode));
        $method = GET_USER_FROM_AUTH_CODE_FUNCTION;
        $this->responseData = $this->dataRequest(array("method" => $method, "params" => $params));
        $this->responseData = json_decode($this->responseData, true);
        if ($this->responseData == 0) {
            return null;
        }
        return reset($this->responseData);
    }

    public function getDefaultAction($ivrId)
    {
        $params = json_encode(array($ivrId));
        $method = GET_DEFAULT_ACTION_FUNCTION;
        $requestData = array("method" => $method, "params" => $params);
        $this->responseData = $this->dataRequest($requestData);
        $this->responseData = json_decode($this->responseData, true);
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

    public function storeCustomerFeedback($stopTime, $timeInIvr, $sessionId, $feedback)
    {
        $this->params = json_encode(array($stopTime, $timeInIvr, $sessionId, $feedback));
        $this->method = SAVE_CUSTOMER_FEEDBACK;
        $this->getResponse(true);
        return $this->responseData;
    }
}
