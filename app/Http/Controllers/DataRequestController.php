<?php

namespace App\Http\Controllers;

class DataRequestController
{
    protected $responseData;
    protected $params;
    protected $method;

    protected function getResponse($isArray = false)
    {
        $requestData = array("method" => $this->method, "params" => $this->params);
        $this->responseData = $this->dataRequest($requestData);
        $this->responseData = json_decode($this->responseData, $isArray);
    }

    private function dataRequest($postData = [])
    {
        $url = DATA_PROVIDER_URL;
        $postRequest = true;
        $requestHeaders = [];
        $headers = ["cache-control: no-cache"];
        $headers = array_merge($headers, $requestHeaders);

        $carlHandler = curl_init();
        curl_setopt($carlHandler, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($carlHandler, CURLOPT_TIMEOUT, 15);
        curl_setopt($carlHandler, CURLOPT_URL, $url);
        curl_setopt($carlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($carlHandler, CURLOPT_HTTPHEADER, $headers);

        if ($postRequest) {
            curl_setopt($carlHandler, CURLOPT_POST, true);
            curl_setopt($carlHandler, CURLOPT_POSTFIELDS, $postData);
        }

        curl_setopt($carlHandler, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($carlHandler, CURLOPT_SSL_VERIFYPEER, false);

        $response = trim(curl_exec($carlHandler));
        $err = curl_error($carlHandler);
        $this->responseData = $response;
        return !empty($err) ? null : $this->responseData;
    }
}
