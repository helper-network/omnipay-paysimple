<?php

namespace Omnipay\PaySimple\Message;

class RefundRequest extends AbstractRequest
{
    
    public function getData()
    {
        return array();
    }

    public function getHttpMethod()
    {
        return 'PUT';
    }

    public function getEndpoint()
    {
        $endpoint = $this->getTestMode() ? $this->sandboxEndpoint : $this->productionEndpoint;
        return  $endpoint . '/v4/payment/' .  $this->getTransactionId() . '/reverse';
    }
}
