<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageDataController extends Controller
{
    private $pageId;
    private $previousPageId;
    private $buttonId;
    private $buttonValue;
    private $ivrId;
    private $cli;
    private $did;
    private $userInput;
    private $language;
    private $soundStatus;
    private $action;
    private $sessionId;
    private $cacheKey;
    private $startTime;
    private $stopTime;
    private $timeInIvr;
    private $previousPage;
    private $errorElement;
    private $elementFactory;
    private $pageElementsData;
    private $pageContentData;
    private $defaultPageID;
    private $ip;
    private $dynamicAudio;
    private $isReloaded;

    public function __construct()
    {
        $this->dataProvider = new DataProviderController();
        $this->dataLogger = new DataLogController();
        $this->responseManager = new ResponseDataController();
        $this->response = response();
        $this->cache = new CacheController();
        $this->pageElementsData = array();
        $this->dynamicAudio = array();
    }

    public function getPageData(Request $request)
    {
        $this->validatePageDataRequest($request);
        $this->setAttributesFromCache();
        if ($this->isNavigationPage()) {
            $this->pageId = $this->getPageIdFromNavigationButton();
        } else {
            if (empty($this->buttonId)) {
                if (!empty($this->previousPageId)) {
                    $this->pageId = $this->previousPageId;
                }
            } else {
                $this->pageId = $this->dataProvider->getPageIdFromButton($this->buttonId);
            }
        }
        if (empty($this->pageId)) {
            $this->pageId = $this->defaultPageID;
        }
        return $this->getPageElements();
    }

    private function isNavigationPage()
    {
        return in_array($this->buttonValue, array(PREVIOUS_BUTTON_VALUE, HOME_BUTTON_VALUE));
    }

    private function validatePageDataRequest($request)
    {
        $this->validate($request, [
            'buttonId' => 'numeric',
            'buttonValue' => 'alpha_num',
            'action' => 'alpha',
            'previousPage' => 'numeric',
            'userInput' => 'numeric',
            'sound' => 'numeric',
            'language' => 'alpha ',
            'key' => 'required|numeric|digits:26'
        ]);
        $this->buttonId = $request->input('buttonId');
        $this->buttonValue = $request->input('buttonValue');
        $this->action = $request->input('action');
        $this->previousPageId = $request->input('previousPage');
        $this->userInput = $request->input('userInput');
        $this->soundStatus = $request->input('soundStatus');
        $this->language = $request->input('sound');
        $this->cacheKey = $request->input('key');
        $this->ip = $request->ip();
    }

    private function setAttributesFromCache()
    {
        //need to set conditions if input data is set on this class variables
        $key = $this->cacheKey;
        $this->ivrId = $this->cache->getCacheData("ivrId" . $key);
        $this->cli = $this->cache->getCacheData("cli" . $key);
        $this->sessionId = $this->cache->getCacheData("sessionId" . $key);
        $this->did = $this->cache->getCacheData("did" . $key);
        $this->startTime = $this->cache->getCacheData("startTime" . $key);
        if ($this->action == null) $this->action = $this->cache->getCacheData("action" . $key);
        if ($this->language == null) $this->language = $this->cache->getCacheData("language" . $key);
        if ($this->soundStatus == null) $this->soundStatus = $this->cache->getCacheData("sound" . $key);
        $this->defaultPageID = $this->dataProvider->getDefaultPageId($this->ivrId);
    }

    private function getPageIdFromNavigationButton()
    {
        if ($this->buttonValue == HOME_BUTTON_VALUE) {
            return $this->defaultPageID;
        } elseif ($this->buttonValue == PREVIOUS_BUTTON_VALUE) {
            return $this->buttonId;
        }
        return null;
    }

    private function getPageElements()
    {
        if (!empty($this->pageId)) {
            if($this->updateVivrJourneyLog()){
                $pageData = $this->dataProvider->getPageData($this->pageId);
                $pageElements = $this->dataProvider->getPageElements($this->pageId);
                foreach ($pageElements as $pageElement) {
                    $element = $this->getElementFromFactory($pageElement);
                    if ($element->hasError) {
                        return $this->getErrorElement();
                    }
                    if (!empty($element->dynamicAudioFiles)) {
                        $this->dynamicAudio = array_merge($this->dynamicAudio, $element->dynamicAudioFiles);
                    }
                    unset($element->hasError);
                    array_push($this->pageElementsData, $element);
                }
                $this->setPageDataResponse($pageData);
                return $this->responseManager->pageDataResponse($this->response, $this->pageContentData);
            }
        }
        return $this->responseManager->pageDataResponse($this->response, $this->getErrorElement());
    }

    private function getElementFromFactory($pageElement)
    {
        $this->elementFactory = new ElementFactoryController($pageElement, $this->cli, $this->language);
        return $this->elementFactory->getElement();
    }

    private function getErrorElement()
    {
        $this->errorElement = new ErrorElementController($this->language);
        return $this->errorElement->getErrorRequestMsg();
    }

    private function updateVivrJourneyLog()
    {
        $this->stopTime = date("Y-m-d H:i:s");
        $this->timeInIvr = time() - $this->startTime;
        $logParams = array();
        $logParams['logTime'] = $this->stopTime;
        $logParams['fromPage'] = $this->pageId;
        $logParams['toPage'] = $this->previousPageId;
        $logParams['sessionId'] = $this->sessionId;
        $logParams['ivrId'] = $this->ivrId;
        $logParams['dtmf'] = $this->buttonValue;
        $logParams['timeInIvr'] = $this->timeInIvr;
        $logParams['statusFlag'] = '';
        $logParams['ip'] = $this->ip;
        $isVivrLogUpdated = $this->dataLogger->updateVivrLog($this->stopTime, $this->timeInIvr, $this->sessionId);
        $isLoggedCustomerJourney = $this->dataLogger->logCustomerJourney($logParams);
        if ($isVivrLogUpdated && $isLoggedCustomerJourney) {
            return true;
        }

        return false;
    }

    private function setPageDataResponse($pageData)
    {
        $pageData = $pageData[$this->pageId];
        $pageDataResponse = new \stdClass();
        $pageDataResponse->currentPage = $this->pageId;
        $pageDataResponse->action = $pageData['task'];
        $pageDataResponse->audioFiles = $this->getAudioFiles($pageData);
        $pageDataResponse->language = $this->language;
        $pageDataResponse->pageHeading = $this->getPageHeading($pageData);
        $pageDataResponse->soundStatus = $this->soundStatus;
        $pageDataResponse->pageContent = $this->pageElementsData;
        $pageDataResponse->previousPage = $this->getNavigationPage($pageData, PREVIOUS_PAGE);
        $pageDataResponse->homePage = $this->getNavigationPage($pageData, HOME_PAGE);

        $this->pageContentData = $pageDataResponse;
    }

    private function getPageHeading($pageData)
    {
        $pageHeading = array();
        $pageHeading[ENGLISH] = $pageData['page_heading_en'];
        $pageHeading[BENGALI] = $pageData['page_heading_ban'];
        return $pageHeading;
    }

    private function getAudioFiles($pageData)
    {
        $audioFiles = array();
        $greetingsAudio = $this->getGreetingsAudio();
        if ($greetingsAudio != null) {
            $audioFiles[ENGLISH][] = $greetingsAudio[ENGLISH];
            $audioFiles[BENGALI][] = $greetingsAudio[BENGALI];
        }
        $audioFromDb = null;
        $audioFromDb[ENGLISH] = $this->getDynamicAudioFiles($pageData['audio_file_en'], ENGLISH);
        $audioFromDb[BENGALI] = $this->getDynamicAudioFiles($pageData['audio_file_ban'], BENGALI);

        foreach ($audioFromDb[ENGLISH] as $audioEn) {
            $audioFiles[ENGLISH][] = AUDIO_FILE_PATH . $audioEn;
        }
        foreach ($audioFromDb[BENGALI] as $audioBn) {
            $audioFiles[BENGALI][] = AUDIO_FILE_PATH . $audioBn;
        }
        return $audioFiles;
    }

    private function getDynamicAudioFiles($audioFiles, $language, $audioType = 'wav')
    {
        $audioFromDb[$language] = explode(",", $audioFiles);
        if (!empty($this->dynamicAudio)) {
            $tempAudio = array_shift($audioFromDb[$language]);
            $dynamicList = null;
            foreach ($this->dynamicAudio as $audio) {
                $dynamicList [] = $language . "/" . $audio . "." . $audioType;
            }
            array_unshift($dynamicList, $tempAudio);
            $audioFromDb[$language] = array_merge($dynamicList, $audioFromDb[$language]);
        }
        return $audioFromDb[$language];
    }

    private function getGreetingsAudio()
    {
        $greetingsAudio = null;
        if ($this->cache->getCacheData('firstGreeting' . $this->cacheKey)) {
            $greetingsAudio[ENGLISH] = AUDIO_FILE_PATH . GREETINGS_AUDIO_EN;
            $greetingsAudio[BENGALI] = AUDIO_FILE_PATH . GREETINGS_AUDIO_BN;
            $this->cache->updateCacheData('firstGreeting' . $this->cacheKey, false);
        }
        return $greetingsAudio;
    }

    function getNavigationPage($pageData, $navigationType)
    {
        if ($navigationType == PREVIOUS_PAGE) {
            if ($pageData['has_previous_menu'] != YES) {
                return null;
            }
        } elseif ($navigationType == HOME_PAGE) {
            if ($pageData['has_main_menu'] != YES) {
                return null;
            }
        }
        $navigationPageElement = $this->dataProvider->getNavigationPage($navigationType);
        if (is_array($navigationPageElement)) {
            $navigationPageElement = $this->getElementFromFactory($navigationPageElement);
        }
        $navigationPageElement->elementId = $pageData['parent_page_id'];
        unset($navigationPageElement->elementOrder);
        unset($navigationPageElement->hasError);
        return $navigationPageElement;
    }

}
