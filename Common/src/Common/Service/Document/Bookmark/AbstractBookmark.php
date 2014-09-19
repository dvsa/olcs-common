<?php
namespace Common\Service\Document\Bookmark;

abstract class AbstractBookmark
{
    protected $token = null;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function isStatic()
    {
        return static::TYPE === 'static';
    }

    abstract public function format();
}
