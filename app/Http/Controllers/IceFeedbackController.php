<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IceFeedbackController extends Controller
{
    private $cacheKey;
    private $feedback;
    private $sessionId;
    private $timeInIvr;

    public function __construct()
    {
        $this->dataLogger = new DataLogController();
        $this->responseManager = new ResponseDataController();
        $this->cache = new CacheController();
        $this->response = response();
    }

    public function storeIceFeedback(Request $request)
    {
        try {
            $this->validate($request, [
                'key' => 'required|digits:26',
                'feedback' => ['required', Rule::in(['Y', 'N'])]
            ]);
        } catch (ValidationException $e) {
            return $this->responseManager->iceFeedbackFailedResponse($this->response);
        }

        $cacheKey = $request->input('key');
        $feedback = $request->input('feedback');
        $sessionId = $this->cache->getCacheData('sessionId' . $cacheKey);
        $startTime = $this->cache->getCacheData('startTime' . $cacheKey);
        $stopTime = date("Y-m-d H:i:s");
        $timeInIvr = time() - $startTime;

        if ($this->dataLogger->storeCustomerFeedback($stopTime, $timeInIvr, $sessionId, $feedback)) {
            return $this->responseManager->iceFeedbackSuccessResponse($this->response);
        }
        return $this->responseManager->iceFeedbackFailedResponse($this->response);
    }
}
