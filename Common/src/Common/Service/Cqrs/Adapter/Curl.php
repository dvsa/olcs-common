<?php

namespace Common\Service\Cqrs\Adapter;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class Curl extends \Zend\Http\Client\Adapter\Curl
{
    /**
     * Send request to the remote server
     *
     * @param  string        $method      HTTP Method
     * @param  \Zend\Uri\Uri $uri         target url
     * @param  float         $httpVersion HTTP version
     * @param  array         $headers     HTTP Headers
     * @param  string        $body        body
     *
     * @return string
     */
    public function write($method, $uri, $httpVersion = 1.1, $headers = [], $body = '')
    {
        $this->response = '';

        return parent::write($method, $uri, $httpVersion, $headers, $body);
    }
}
