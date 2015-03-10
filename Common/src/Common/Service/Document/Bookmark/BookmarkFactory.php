<?php

namespace Common\Service\Document\Bookmark;

use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Bookmark factory class
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BookmarkFactory
{
    public function locate($token)
    {
        $filter = new UnderscoreToCamelCase();

        // 1) SOMETHING__Like_This -> something__like_this
        $className = strtolower($token);
        // 2) something__like_this -> Something_LikeThis
        $className = $filter->filter($className);
        // 3) Something_LikeThis -> SomethingLikeThis
        $className = str_replace("_", "", $className);

        $instance = $this->getInstance($className);

        $instance->setToken($token);

        return $instance;
    }

    private function getInstance($className)
    {
        $class = __NAMESPACE__ . '\\' . $className;

        if (class_exists($class)) {
            /**
             * if we have a specific class to handle this bookmark then life's good,
             * we can just hand off straight to it
             */
            return new $class();
        }

        $alias = __NAMESPACE__ . '\Alias\\' . $className;

        if (class_exists($alias)) {
            /**
             * An alias is simply a bookmark which extends another without modifying its
             * behaviour in any way. They only exist because the bookmarks we're importing
             * have wildly differing names across different templates and trying to consolidate
             * them all at source is almost impossible
             */
            return new $alias();
        }

        /**
         * otherwise we fall back to a class which will rummage through the data
         * it is later provided looking for a known key representing user chosen
         * paragraphs
         */
        return new TextBlock();
    }
}
