<?php
namespace Common\Service\Document\Bookmark\Base;

/**
 * Abstract bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
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

    abstract public function render();
}
