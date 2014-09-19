<?php
namespace Common\Service\Document\Parser;

class RtfParser implements ParserInterface
{
    public function extractTokens($content)
    {
        $matches = $this->getMatches($content);
        $tokens = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $tokens[] = $matches[1][$i];
        }
        return $tokens;
    }

    public function replace($content, $data)
    {
        $matches = $this->getMatches($content);

        for ($i = 0; $i < count($matches[0]); $i++) {
            $literal = $matches[0][$i];
            $token   = $matches[1][$i];
            if (isset($data[$token])) {
                $content = str_replace($literal, $data[$token], $content);
            }
        }
        return $content;
    }

    private function getMatches($content)
    {
        preg_match_all(
            "#{\\\.\\\bkmkstart\s([^}]+)}{\\\.\\\bkmkend\s[^}]+}#",
            $content,
            $matches
        );
        return $matches;
    }
}
