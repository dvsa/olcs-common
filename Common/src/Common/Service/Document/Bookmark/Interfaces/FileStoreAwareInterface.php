<?php

namespace Common\Service\Document\Bookmark\Interfaces;

/**
 * File Store Aware Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
interface FileStoreAwareInterface
{
    public function setFileStore(/* FileStoreService */$fileStore);

    public function getFileStore();
}
