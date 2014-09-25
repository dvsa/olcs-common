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

        // bizarrely the filter alone won't replace all underscores,
        // so we need a bit of extra muscle
        $className = str_replace("_", "", $filter->filter($token));

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
