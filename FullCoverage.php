<?php
// @codingStandardsIgnoreFile

$testDirectories = array(
    'common' => realpath(__DIR__ . '/test'),
    'backend' => realpath(__DIR__ . '/../olcs-backend/test'),
    'entities' => realpath(__DIR__ . '/../olcs-entities/test'),
    'internal' => realpath(__DIR__ . '/../olcs-internal/test'),
    'selfserve' => realpath(__DIR__ . '/../olcs-selfserve/test'),
    // This one outputs a seralized array, the others don't
    //'postcode' => realpath(__DIR__ . '/../olcs-postcode/test')
);

$commonDir = realpath(__DIR__);

$coverageDir = realpath(__DIR__ . '/coverage/');

$files = array();

foreach ($testDirectories as $key => $dir) {
    $fileName = $coverageDir . '/' . $key . '.cov';

    $output = shell_exec('cd ' . $dir . ' && ' . $commonDir . '/vendor/bin/phpunit -c ' . $dir . '/phpunit-full.xml --coverage-php ' . $fileName);

    print_r($output);

    $files[] = $fileName;
}

$output = shell_exec('vendor/bin/phpcov merge --html coverage/html/ coverage/');

print_r($output);
