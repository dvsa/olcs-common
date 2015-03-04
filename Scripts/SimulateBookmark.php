<?php
/**
 * SimulateBookmark.php
 *
 * Takes a bookmark name and its data parameters and talks to the backend API
 * using the query that the bookmark returns.
 *
 * This tools is useful for checking what response a bookmarks query will give
 * back.
 *
 * Note:
 * Usage of this script will look a little something like this:
 *
 * <pre>
 *  // Dynamic Bookmarks
 *  php ./Scripts/SimulateBookmark.php LicenceType '{"licence": 207}'
 *
 *  // Static Bookmarks
 *  php ./Scripts/SimulateBookmark.php TodaysDate
 * </pre>
 * @author Josh Curtis <josh.curtis@valtech.com>
 */

require_once __DIR__ . '/../vendor/autoload.php';

use \Common\Service\Document\Bookmark\BookmarkFactory;

// Must pass a bookmark to use.
if(!isset($argv[1])) {
    die('Bookmark name required.');
}

$bookmarkName = $argv[1];

// Check the bookmark name passed exists as a class.
$bookmark = "\\Common\\Service\\Document\\Bookmark\\{$bookmarkName}";
if(!class_exists($bookmark)) {
    die($bookmark . ' not found.');
}

// Get and configure the service manager for use later on.
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

// Instantiate a new bookmark instance and check if it's static.
$bookmark = new $bookmark();
if($bookmark->isStatic()) {
    // If it is just render the output.
    echo "Result:\n";
    return var_dump($bookmark->render());
}

// Check that a data argument has been provided.
if(!isset($argv[2])) {
    die('Data array is required for this bookmark.');
}

// Data should be provided as a json object.
$data = json_decode($argv[2], true);
if(!is_array($data)) {
    die('Data given cannot be converted to an array.');
}

// Get and store the bookmark query.
$query = $bookmark->getQuery($data);

// Dump the query out.
echo "Query:\n";
var_dump($query);

// Get the rest helper and make the request to the backend.
$result = $serviceManager->get('Helper\Rest')
    ->makeRestCall('BookmarkSearch', 'GET', [], array($query));

// Dump the result from the backend.
echo "\n\nResult:\n";
var_dump($result);