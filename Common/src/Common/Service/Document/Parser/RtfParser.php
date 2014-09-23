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

        $search  = [];
        $replace = [];

        for ($i = 0; $i < count($matches[0]); $i++) {
            $literal = $matches[0][$i];
            $token   = $matches[1][$i];

            // bear in mind the later str_replace will replace *all*
            // bookmarks of this name; probably what we want of course,
            // but worth being clear about
            if (isset($data[$token])) {
                $search[]  = $literal;
                $replace[] = $this->format($data[$token]);
            }
        }

        $content = str_replace($search, $replace, $content);

        return $content;
    }

    private function getMatches($content)
    {
        preg_match_all(
            "#{\\\.\\\bkmkstart\s([^}]+)}\s*{\\\.\\\bkmkend\s[^}]+}#",
            $content,
            $matches
        );
        return $matches;
    }

    private function format($data)
    {
        return str_replace("\n", "\par ", $data);
    }
}
