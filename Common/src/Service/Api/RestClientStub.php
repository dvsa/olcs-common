<?php

namespace Common\Service\Api;

class RestClientStub {
    private $url;
    
    public function __construct($url)
    {
        $this->url = $url;
    }
    
    public function get(array $params = [])
    {
        return $_SESSION[$this->url];
    }

    
    public function create(array $params = array())
    {
        $_SESSION[$this->url] = empty($_SESSION[$this->url]) ? [] : $_SESSION[$this->url];
        $_SESSION[$this->url] = array_merge($_SESSION[$this->url], array_filter($params));
    }

   
    public function update($path = null, array $params = array(), $version = null)
    {
        
    }  
    
    public function patch($path = null, array $params = array(), $version = null) {
        
    }
}

    