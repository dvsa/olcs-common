<?php

namespace Common\View\Helper;

class UniqidGenerator
{
    protected $id;

    public function __construct(string $prefix = '',  bool $more_entropy = false)
    {
        $this->id = uniqid($prefix, $more_entropy);
    }

    public function getId()
    {
        return $this->id;
    }

    public function regenerateId(string $prefix = '',  bool $more_entropy = false)
    {
        $this->id = uniqid($prefix, $more_entropy);
        return $this->id;
    }
}
