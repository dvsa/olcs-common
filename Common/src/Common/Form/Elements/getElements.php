<?php
$elements = [];
foreach(glob(__DIR__ . '/*.element.php') as $element) {
    $elements += include $element;
}
return $elements;

