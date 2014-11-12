<?php

namespace Common\Service\Data;

/**
 * Interface CloseableInterface
 * @package Common\Service\Data
 */
interface CloseableInterface
{
    public function canClose($id);
    public function isClosed($id);
    public function canReopen($id);
}
