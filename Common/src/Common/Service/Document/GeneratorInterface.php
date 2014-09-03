<?php
namespace Common\Service\Document;

interface GeneratorInterface
{
    public function generate($content, $bookmarks);
}
