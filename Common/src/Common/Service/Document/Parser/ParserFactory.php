<?php
namespace Common\Service\Document\Parser;

class ParserFactory
{
    public function getParser($mime)
    {
        switch ($mime) {
        case 'application/rtf':
        case 'application/x-rtf':
            return new RtfParser();
        default:
            throw new \RuntimeException('No parser found for mime type: ' . $mime);
        }
    }
}
