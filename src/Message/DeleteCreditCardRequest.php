<?php

namespace Omnipay\PaySimple\Message;

class DeleteCreditCardRequest extends AbstractRequest
{

    public function getAccountId()
    {
        return $this->getParameter('AccountId');
    }

    public function setAccountId($value)
    {
        return $this->setParameter('AccountId', $value);
    }

    public function getData()
    {
        return array();
    }

    public function getHttpMethod()
    {
        return 'DELETE';
    }

    public function getEndpoint()
    {
        $endpoint = $this->getTestMode() ? $this->sandboxEndpoint : $this->productionEndpoint;
        return  $endpoint . '/v4/account/creditcard/' . $this->getAccountId();
    }
}
