<?php

namespace App\Http\Controllers;

class PostpaidUserController extends UserController
{
    protected $userType = POSTPAID;
    protected $ivrId = POSTPAID_IVR;

    public function getUserType()
    {
        return $this->userType;
    }

    public function getIvrId()
    {
        return $this->ivrId;
    }
}
