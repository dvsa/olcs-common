<?php
namespace Common\Service\Document\Bookmark;

abstract class AbstractBookmark
{
    protected $token = null;

    public function setToken($token)
    {
        $this->token = $token;
    }

    abstract public function getQuery($data);

    abstract public function format($data);
}
