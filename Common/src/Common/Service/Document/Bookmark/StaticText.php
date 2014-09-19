<?php
namespace Common\Service\Document\Bookmark;

class StaticText extends AbstractBookmark
{
    public function getQuery($data)
    {
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

    public function format($data)
    {
        if (!isset($data[$this->token])) {
            return null;
        }
        $details = $data[$this->token];

        $result = "";
        foreach ($details as $paragraph) {
            $result .= $paragraph['paraText'];
        }
        return $result;
    }
}
