<?php
namespace Common\Service\Document\Parser;

interface ParserInterface
{
    public function extractTokens($content);

    public function replace($content, $data);
}
