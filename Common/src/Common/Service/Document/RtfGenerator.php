<?php
namespace Common\Service\Document;

class RtfGenerator implements GeneratorInterface
{
    public function generate($content, $bookmarks)
    {
        preg_match_all(
            "#{\\\.\\\bkmkstart\s([^}]+)}{\\\.\\\bkmkend\s[^}]+}#",
            $content,
            $matches
        );

        for ($i = 0; $i < count($matches[0]); $i++) {
            $literal = $matches[0][$i];
            $token   = $matches[1][$i];
            if (isset($bookmarks[$token])) {
                $content = str_replace($literal, $bookmarks[$token], $content);
            }
        }
        return $content;
    }
}
