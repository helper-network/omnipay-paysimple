<?php

namespace Omnipay\PaySimple\Message;

class Response implements \Omnipay\Common\Message\ResponseInterface
{
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        $this->request;
    }
    
    public function isSuccessful()
    {
        $failureData = false;
        $message = $this->loadMessage();
        if (is_array($message) && isset($message['Meta']['Errors'])) {
            $failureData = true;
        }

        return ($this->response->getStatusCode() >= 200 && $this->response->getStatusCode() <= 299 && !$failureData);
    }
    
    public function isRedirect()
    {
        return false;
    }

    public function isCancelled()
    {
        return false;
    }

    public function getMessage()
    {
    	$messageString = [];
    	$messageData = $this->loadMessage();
		if (is_array($messageData) && isset($messageData['Meta']['Errors'])) {
			$code = $messageData['Meta']['Errors']['ErrorCode'];
			$messageString .= "Error Code: $code\n";
			foreach ($messageData['Meta']['Errors']['ErrorMessages'] as $message) {
				$field = $message['Field'];
				$message = $message['Message'];
				$messageString.= "Field: $field. Message: $message";
			}
		} else {
			return $messageData;
		}

		return $messageString;
    }
    
    public function getCode()
    {
        return $this->response->getStatusCode();
    }

    public function getTransactionReference()
    {
        
        $this->response->getBody()->rewind();
        $json = json_decode($this->response->getBody()->getContents(), true);

        return $json['Response']['Id'];
    }

    public function getData()
    {
        return $this->request->getData();
    }

    private function loadMessage()
	{
		$this->response->getBody()->rewind();
		return json_decode($this->response->getBody()->getContents(), true);
	}
}
