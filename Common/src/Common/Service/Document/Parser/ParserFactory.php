<?php

namespace Common\Service\Document\Parser;

/**
 * Parser factory class
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
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
