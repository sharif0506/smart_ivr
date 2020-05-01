<?php

namespace App\Http\Controllers;

class PrepaidUserController extends UserController
{
    protected $userType = PREPAID;
    protected $ivrId = PREPAID_IVR;

    public function getUserType()
    {
        return $this->userType;
    }

    public function getIvrId()
    {
        return $this->ivrId;
    }
}
