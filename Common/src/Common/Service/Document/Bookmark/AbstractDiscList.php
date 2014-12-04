<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Abstract Disc list bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractDiscList extends DynamicBookmark
{
    /**
     * We have to split some fields if they exceed this length
     */
    const MAX_LINE_LENGTH = 23;

    /**
     * No disc content? No problem
     */
    const PLACEHOLDER = 'XXXXXXXXXX';

    /**
     * Discs per page - any shortfall will be voided with placeholders
     */
    const PER_PAGE = 6;

    /**
     * Bookmark variable prefix
     */
    const BOOKMARK_PREFIX = 'DISC';

    /**
     * Let the parser know we've already formatted our content by the
     * time it has been rendered
     */
    const PREFORMATTED = true;

    protected $discBundle = [];

    protected $service;

    public function getQuery(array $data)
    {
        $query = [];

        foreach ($data as $id) {
            $query[] = [
                'service' => $this->service,
                'data' => [
                    'id' => $id
                ],
                'bundle' => $this->discBundle
            ];
        }

        return $query;
    }

    /**
     * Split a string into N array parts based on a predefined
     * constant max line length
     */
    protected function splitString($str)
    {
        return str_split($str, static::MAX_LINE_LENGTH);
    }

    /**
     * Return either PREFIX1_ or PREFIX2_ based on a given index
     */
    protected function getPrefix($index)
    {
        $prefix = ($index % static::PER_ROW) + 1;
        return static::BOOKMARK_PREFIX . $prefix . '_';
    }

    protected function renderSnippets($snippets)
    {
        $snippet = $this->getSnippet();
        $parser  = $this->getParser();

        // at last, we can loop through each group and run a sub
        // replacement on its tokens
        $str = '';
        foreach ($snippets as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }
}
