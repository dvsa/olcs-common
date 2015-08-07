<?php
// @codingStandardsIgnoreFile

/**
 * Usage:
 * UploadSingleTemplate.php <path_to_template> <destination_folder>
 * e.g. php -f Scripts/UploadSingleTemplate.php ../olcs-templates/GB/PSVChecklist.rtf templates/GB
 */

require("vendor/autoload.php");
require("TemplateWorker.php");

$baseDir = 'templates';
if (isset($argv[2])) {
    $baseDir = $argv[2];
}

$worker = new TemplateWorker($argv);

$source = $argv[1];
$folder = $argv[2];
$name = basename($source);

$worker->uploadFile($name, $folder, $source);
