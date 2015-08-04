<?php
// @codingStandardsIgnoreFile

require("vendor/autoload.php");
require("TemplateWorker.php");

$baseDir = 'templates';
if (isset($argv[2])) {
    $baseDir = $argv[2];
}

$worker = new TemplateWorker($argv);

echo "Reading remote workspace...\n";
// @todo DOCUMENT STORE Remove this code block as the new service doesn't allow us to get metadata of folders
// or recursively remove files. It does allow us to update existing files, the only problem will be that any templates
// we have REMOVED from the template repo, won't be removed from the document store automatically
{
    $data = $worker->readWorkspace();
    // always clear out tmp; it can get a bit cluttered
    $worker->deleteFolder('/tmp', $data);
    // then mirror whatever our directory was
    $worker->deleteFolder('/' . $baseDir, $data);
}

$worker->uploadFolder($baseDir, $argv[1]);
