<?php

namespace Common\Service\Document\Bookmark;

use Zend\Filter\Word\UnderscoreToCamelCase;

class BookmarkFactory
{
    public function locate($token)
    {
        $filter = new UnderscoreToCamelCase();
        $class = '\\Common\\Service\\Document\\Bookmark\\' . $filter->filter($token);

        if (class_exists($class)) {
            $instance = new $class();
        } else {
            $instance = new StaticText();
        }

        $instance->setToken($token);

        return $instance;
    }
}
