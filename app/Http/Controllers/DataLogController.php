<?php
namespace App\Http\Controllers;

class DataLogController extends DataRequestController
{
    function createCustomerLogData($userData, $ip, $source)
    {
        $logTime = date("Y-m-d H:i:s");
        $customerJourneyData = array($userData['cli'], VIVR_MODULE_TYPE, VIVR_ONLY_MODULE_SUBTYPE, $logTime, $userData['session_id']);
        $customerLogData = array($logTime, $logTime, $userData['cli'], $userData['did'], $userData['ivr_id'], 0, $userData['session_id'], $userData['language'], '', '', '', $source, $ip);
        $customerJourneyResponse = $this->storeCustomerJourney($customerJourneyData);
        $logFromWebVisitResponse = $this->storeVivrLog($customerLogData);
        if ($customerJourneyResponse && $logFromWebVisitResponse) {
            return true;
        }
        return false;
    }

    private function storeCustomerJourney($paramsArray)
    {
        $params = json_encode($paramsArray);
        $method = STORE_CUSTOMER_JOURNEY_FUNCTION;
        $this->responseData = $this->dataRequest(array("method" => $method, "params" => $params));
        $this->responseData = json_decode($this->responseData);
        if ($this->responseData == 1) {
            return true;
        }
        return false;
    }

    private function storeVivrLog($paramsArray)
    {
        $params = json_encode($paramsArray);
        $method = VIVR_LOG_FUNCTION;
        $this->responseData = $this->dataRequest(array("method" => $method, "params" => $params));
        $this->responseData = json_decode($this->responseData);
        if ($this->responseData == 1) {
            return true;
        }
        return false;
    }
}
