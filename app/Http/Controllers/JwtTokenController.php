<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;

class JwtTokenController extends Controller
{
    private $jwtToken;

    public function getJwtAuthToken($user)
    {
        $this->createJwtAuthToken($user);
        return $this->jwtToken;
    }

    private function createJwtAuthToken($user)
    {
        $plan = $user->getUserType();
        $cli = $user->getCli();
        $key = env('JWT_SECRET');
        $createTime = time();
        $notBeforeTime = time() + 5;
        $expireTime = time() + LOGIN_DURATION;
        $payload = array(
            "iss" => VIVR_TOKEN_ISSUER,
            "aud" => VIVR_TOKEN_ISSUER,
            "iat" => $createTime,
            "nbf" => $notBeforeTime,
            "exp" => $expireTime,
            "cli" => $cli,
            "plan" => $plan,
//            "ivr" => $user->getIvrId(),
            "uid" => md5($cli . $plan . $createTime)
        );
        $this->jwtToken = JWT::encode($payload, $key);
    }

}
