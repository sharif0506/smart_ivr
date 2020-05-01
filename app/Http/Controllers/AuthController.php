<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $user;
    private $userFactory;
    private $jwtTokenObject;
    private $cacheKey;

    public function __construct()
    {
        $this->dataProvider = new DataProviderController();
        $this->dataLogger = new DataLogController();
        $this->responseManager = new ResponseDataController();
        $this->cache = new CacheController();
        $this->response = response();
    }

    private function setUser($cli)
    {
        $this->userFactory = new UserFactoryController($cli);
        $this->user = $this->userFactory->getUser();
    }

    public function loginWithPhone(Request $request)
    {
        $this->validate($request, [
            'cli' => 'required|numeric|digits:10'
        ]);
        $cli = $request->input('cli');
        $ip = $request->ip();
        $this->setUser($cli);
        if ($this->user != null) {
            $this->user->setIPAddress($ip);
            if ($this->generatePin($cli)) {
                return $this->responseManager->successOtpResponse($this->response);
            }
        }
        return $this->responseManager->failedOtpResponse($this->response);
    }

    private function generatePin($cli)
    {
        $authCode = rand(100000, 999999);
        $paramsOfPin = array(
            $cli,
            $this->user->getUserType(),
            $this->user->getIvrId(),
            $authCode,
            $this->user->getIPAddress()
        );
        if ($this->dataProvider->generatePIN($paramsOfPin)) {
            $text = PIN_SMS_TEXT . $authCode;
            if ($this->dataProvider->sendSms($cli, $text)) return true;
        }
        return false;
    }

    public function loginWithPin(Request $request)
    {
        $this->validate($request, [
            'cli' => 'required|numeric|digits:10',
            'pin' => 'required|numeric|digits:6'
        ]);
        $cli = $request->input('cli');
        $pin = $request->input('pin');
        $userData = $this->dataProvider->getUser($cli, $pin);
        if ($userData != null) {
            return $this->generateTokenData($request, $userData, WEB_SOURCE);
        }
        return $this->responseManager->failedLoginResponse($this->response);
    }

    public function loginWithAuthCode(Request $request)
    {
        $this->validate($request, [
            'authCode' => 'required|alpha_num|size:12'
        ]);
        $authCode = $request->input('authCode');
        $userData = $this->dataProvider->getUserFromAuthCode($authCode);
        if ($userData != null) {
            return $this->generateTokenData($request, $userData, IVR_SOURCE);
        }
        return $this->responseManager->failedLoginResponse($this->response);
    }

    private function generateTokenData($request, $userData, $source)
    {
        $this->jwtTokenObject = new JwtTokenController();
        $cli = $userData['cli'];
        $this->setUser($cli);
        if ($this->user != null) {
            $userData['session_id'] = $cli . time();
            $isLogged = $this->dataLogger->createCustomerLogData($userData, $request->ip(), $source);
            if ($isLogged) {
                $token = $this->jwtTokenObject->getJwtAuthToken($this->user);
                $this->cacheKey = rand(100000, 999999) . $userData['session_id'];
                if ($token != null) {
                    $this->setInitialCacheData($userData);
                    return $this->responseManager->successLoginResponse($this->response, $token, $this->cacheKey);
                }
            }
        }
        return $this->responseManager->errorLoginResponse($this->response);
    }

    public function setInitialCacheData($userData)
    {
        $key = $this->cacheKey;

        $this->cache->setCacheData("ivrId" . $key, $userData['ivr_id']);
        $this->cache->setCacheData("cli" . $key, $userData['cli']);
        $this->cache->setCacheData("language" . $key, $userData['language']);
        $this->cache->setCacheData("sessionId" . $key, $userData['session_id']);
        $this->cache->setCacheData("sound" . $key, 'ON');
        $this->cache->setCacheData("did" . $key, $userData['did']);
        $this->cache->setCacheData("startTime" . $key, time());
        $this->cache->setCacheData("action" . $key, DEFAULT_ACTION);
        $this->cache->setCacheData("firstGreeting" . $key, true);
    }

    public function test(Request $request){
        $key = $request->input('key');
        die("test");
    }

}
