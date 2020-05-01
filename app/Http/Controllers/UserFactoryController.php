<?php

namespace App\Http\Controllers;

class UserFactoryController extends Controller
{
    protected $phone;
    protected $ip;

    public function __construct($phone)
    {
        $this->phone = $phone;
        $this->dataProvider = new DataProviderController();
    }

    //new user type will be implemented here
    public function getUser()
    {
        $userType = $this->getUserCategory();
        if ($userType === "0") {
            return new PrepaidUserController($this->phone);
        } else if ($userType === "1") {
            return new PostpaidUserController($this->phone);
        }
        return null;
    }

    private function getUserCategory()
    {
        return $this->dataProvider->getUserType($this->phone);
    }
}
