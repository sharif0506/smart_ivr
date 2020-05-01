<?php

namespace App\Http\Controllers;

class ResponseDataController
{
    private $errorCode;
    private $message;

    private function responseJsonData($response, $httpStatusCode, $data = null)
    {
        return $response->json([
            'errorCode' => $this->errorCode,
            'message' => $this->message,
            'data' => $data
        ], $httpStatusCode);
    }

    public function successOtpResponse($response)
    {
        $this->errorCode = 1000;
        $this->message = 'OTP Sent Successfully.';
        return $this->responseJsonData($response, 200);
    }

    public function failedOtpResponse($response)
    {
        $this->errorCode = 1001;
        $this->message = 'Failed to Send OTP.';
        return $this->responseJsonData($response, 400);
    }

    public function successLoginResponse($response, $token, $cacheKey)
    {
        $this->errorCode = 1002;
        $this->message = 'Logged In Successfully.';
        $data = array(
            'token' => $token,
            'key' => $cacheKey
        );
        return $this->responseJsonData($response, 200, $data);
    }

    public function failedLoginResponse($response)
    {
        $this->errorCode = 1003;
        $this->message = 'Unauthorized. User Not Found.';
        return $this->responseJsonData($response, 401);
    }

    public function errorLoginResponse($response)
    {
        $this->errorCode = 1004;
        $this->message = 'Login Failed.';
        return $this->responseJsonData($response, 401);
    }

    public function pageDataResponse($response, $pageData)
    {
        $this->errorCode = 1005;
        $this->message = 'Page Data Returned Successfully';
        $data = array(
            'pageData' => $pageData
        );
        return $this->responseJsonData($response, 200, $pageData);
    }

    public function iceFeedbackSuccessResponse($response)
    {
        $this->errorCode = 1010;
        $this->message = 'Feedback Stored Successfully';
        return $this->responseJsonData($response, 200);
    }

    public function iceFeedbackFailedResponse($response)
    {
        $this->errorCode = 1011;
        $this->message = 'Feedback Storing Failed';
        return $this->responseJsonData($response, 200);
    }
}
