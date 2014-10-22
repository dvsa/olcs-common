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

        $class = __NAMESPACE__ . '\\' . $className;

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
