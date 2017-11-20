<?php

namespace Common\Service\Cqrs;


/**
 * Recover HttpClientExceptionTrait Trait
 *
 */
trait RecoverHttpClientExceptionTrait
{
    /** @var bool */
    protected $recoverHttpClientException = false;

    /**
     * Set RecoverHttpClientException
     *
     * @param bool $value value
     *
     * @return void
     */
    public function setRecoverHttpClientException($value)
    {
        $this->recoverHttpClientException = $value;
    }

    /**
     * get RecoverHttpClientException
     *
     * @return bool
     */
    public function getRecoverHttpClientException()
    {
        return $this->recoverHttpClientException;
    }
}
