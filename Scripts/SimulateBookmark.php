<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \Common\Service\Document\Bookmark\BookmarkFactory;

if(!isset($argv[1])) {
    die('Bookmark name required.');
}

$bookmarkName = $argv[1];

$bookmark = "\\Common\\Service\\Document\\Bookmark\\{$bookmarkName}";
if(!class_exists($bookmark)) {
    die($bookmark . ' not found.');
}

$serviceManager = new \Zend\ServiceManager\ServiceManager(new \Zend\Mvc\Service\ServiceManagerConfig());
$serviceManager->setService('ApplicationConfig', array(
        'modules' => array(
            'Common'
        ),
        'module_listener_options' => array(
            'module_paths' => array(
                __DIR__ . '/../'
            )
        )
    ));
$serviceManager->get('ModuleManager')->loadModules();
$serviceManager->setAllowOverride(true);

$bookmark = new $bookmark();
if($bookmark->isStatic()) {
    echo "Result:\n";
    return var_dump($bookmark->render());
}

if(!isset($argv[2])) {
    die('Data array is required for this bookmark.');
}

$data = eval("return " . $data . ";");
if(!is_array($data)) {
    die('Data given cannot be converted to an array.');
}

$query = $bookmark->getQuery($data);

echo "Query:\n";
var_dump($query);

$result = $serviceManager->get('Helper\Rest')
    ->makeRestCall('BookmarkSearch', 'GET', [], array($query));

echo "\n\nResult:\n";
var_dump($result);