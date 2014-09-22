<?php
namespace Common\Service\Document\Bookmark\Base;

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
