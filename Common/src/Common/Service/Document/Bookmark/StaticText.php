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
        $result = "";
        foreach ($data as $paragraph) {
            $result .= $paragraph['paraText'];
        }
        return $result;
    }
}
