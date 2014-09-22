<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

class TextBlock extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        // TextBlocks are used as fallbacks when there isn't a more
        // specific bookmark for a given token. As such there's a good
        // chance we don't actually have any data in our `bookmarks`
        // array to satisfy this text block at all, so we need to
        // be defensive
        if (!isset($data['bookmarks'][$this->token])) {
            return null;
        }
        $paragraphs = $data['bookmarks'][$this->token];
        $queries = [];
        foreach ($paragraphs as $paragraphId) {
            $query = [
                'service' => 'DocParagraph',
                'data' => [
                    'id' => $paragraphId
                ],
                'bundle' => [
                    'properties' => ['paraText']
                ]
            ];
            $queries[] = $query;
        }

        return $queries;
    }

    public function format()
    {
        $result = "";
        foreach ($this->data as $paragraph) {
            $result .= $paragraph['paraText'] . "\n";
        }
        return substr($result, 0, -2);
    }
}
