<?php

namespace Common\Service\Document\Bookmark\Base;

/**
 * Dynamic bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class DynamicBookmark extends AbstractBookmark
{
    const TYPE = 'dynamic';

    protected $data = [];

    public function setData(array $data)
    {
        $this->data = $data;
    }

    abstract public function getQuery(array $data);
}
