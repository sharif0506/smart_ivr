<?php

namespace App\Http\Controllers;

class ErrorElementController extends ElementController
{
    public function __construct($language)
    {
        $this->language = $language;
    }

    public function getApiErrorMsg()
    {
        $this->elementType = ELEMENT_TYPE_PARAGRAPH;
        $this->displayTextEn = "Sorry, the service is not available at this moment. Please try again later. Thanks";
        $this->displayTextBn = "দুঃখিত, এই মুহূর্তে সার্ভিসটি দেয়া সম্ভব হচ্ছে না। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন। ধন্যবাদ।";
        $this->isVisible = VISIBLE;
        $this->elementOrder = 1;

        $this->unsetElementProperties();
        return $this;
    }

    public function getErrorRequestMsg()
    {
        $this->elementType = ELEMENT_TYPE_PARAGRAPH;
        $this->displayTextEn = "Sorry, your request is not valid. Please try again later. Thanks";
        $this->displayTextBn = "দুঃখিত, আপনার অনুরোধ সঠিক নয়। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন। ধন্যবাদ।";
        $this->isVisible = VISIBLE;
        $this->elementOrder = 1;

        $this->unsetElementProperties();
        return $this;
    }
}
