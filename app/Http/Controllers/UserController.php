<?php

namespace App\Http\Controllers;

abstract class UserController
{
    protected $cli;
    protected $ip;
    protected $pin;
    protected $tPin;
    protected $authCode;

    public function __construct($cli)
    {
        $this->cli = $cli;
    }

    abstract public function getUserType();

    abstract public function getIvrId();

    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function getAuthCode()
    {
        return $this->authCode;
    }

    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
    }

    public function getIPAddress()
    {
        return $this->ip;
    }

    public function setIPAddress($ip)
    {
        $this->ip = $ip;
    }

    public function getCli()
    {
        return $this->cli;
    }
}
