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
            /**
             * if we have a specific class to handle this bookmark then life's good,
             * we can just hand off straight to it
             */
            $instance = new $class();
        } else {
            /**
             * otherwise we fall back to a class which will rummage through the data
             * it is later provided looking for a known key representing user chosen
             * paragraphs
             */
            $instance = new TextBlock();
        }

        $instance->setToken($token);

        return $instance;
    }
}
