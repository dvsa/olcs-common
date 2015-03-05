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
 *  // Dynamic Bookmarks (note the data should be a json object).
 *  php ./Scripts/SimulateBookmark.php LicenceType '{"licence": 207}'
 *
 *  // Static Bookmarks
 *  php ./Scripts/SimulateBookmark.php TodaysDate
 * </pre>
 *
 * @author Josh Curtis <josh.curtis@valtech.com>
 */

require_once __DIR__ . '/../vendor/autoload.php';

use \Common\Service\Document\Bookmark\BookmarkFactory;

// Must pass a bookmark to use.
if (!isset($argv[1])) {
    die("\033[41;37mBookmark name required.\033[0m");
}

$bookmarkName = $argv[1];

// Check the bookmark name passed exists as a class.
$bookmark = "\\Common\\Service\\Document\\Bookmark\\{$bookmarkName}";
if (!class_exists($bookmark)) {
    die("\033[41;37m" . $bookmark . " not found.\033[0m");
}

// Get and configure the service manager for use later on.
$serviceManager = new \Zend\ServiceManager\ServiceManager(new \Zend\Mvc\Service\ServiceManagerConfig());
$serviceManager->setService(
    'ApplicationConfig', array(
        'modules' => array(
            'Common'
        ),
        'module_listener_options' => array(
            'module_paths' => array(
                __DIR__ . '/../'
            )
        )
    )
);
$serviceManager->get('ModuleManager')->loadModules();
$serviceManager->setAllowOverride(true);

// Instantiate a new bookmark instance and check if it's static.
$bookmark = new $bookmark();
if ($bookmark->isStatic()) {
    // If it is just render the output.
    echo "\033[0;32mOutput:\033[0m\n";
    return var_dump($bookmark->render());
}

// Check that a data argument has been provided.
if (!isset($argv[2])) {
    die("\033[41;37mData array is required for this bookmark.\033[0m");
}

// Data should be provided as a json object.
$data = json_decode($argv[2], true);
if (!is_array($data)) {
    die("\033[41;37mData given cannot be converted to an array.\033[0m");
}

// Get and store the bookmark query.
$query = $bookmark->getQuery($data);

// Dump the query out.
echo "\033[0;32mQuery:\033[0m\n";

var_dump($query);

// Get the rest helper and make the request to the backend.
$result = $serviceManager->get('Helper\Rest')
    ->makeRestCall('BookmarkSearch', 'GET', [], array($bookmarkName => $query));

// Dump the query result from the backend.
echo "\n\n\033[0;32mResult:\033[0m\n";
print_r($result);

// Set the bookmark data.
$bookmark->setData($result[$bookmarkName]);

// Dump the bookmark output.
echo "\n\n\033[0;32mOutput (Simulated, dont trust me):\033[0m\n";
var_dump($bookmark->render());