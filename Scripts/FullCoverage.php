<?php
// @codingStandardsIgnoreFile

include_once(__DIR__ . '/../vendor/autoload.php');

$testDirectories = array(
    'common' => realpath(__DIR__ . '/../test'),
    'backend' => realpath(__DIR__ . '/../../olcs-backend/test'),
    'internal' => realpath(__DIR__ . '/../../olcs-internal/test'),
    'selfserve' => realpath(__DIR__ . '/../../olcs-selfserve/test')
);

$commonDir = realpath(__DIR__ . '/../');

$coverageDir = realpath(__DIR__ . '/../coverage/');

$coverageObjects = array();

foreach ($testDirectories as $key => $dir) {
    $fileName = $coverageDir . '/' . $key . '.cov';

    echo shell_exec('cd ' . $dir . ' && ' . $commonDir . '/vendor/bin/phpunit -c ' . $dir . '/phpunit-full.xml --coverage-php ' . $fileName);

    $coverageObjects[] = include($fileName);
}

// @IMPORTANT I have had to implement my own method of merging the reports, as this method fails to display files
// that are completely uncovered

// echo shell_exec($commonDir . '/vendor/bin/phpcov merge --html ' . $commonDir . '/coverage/html/ ' . $commonDir . '/coverage/');

$mainReport = new PHP_CodeCoverage();
$mainFilter = $mainReport->filter();

foreach ($coverageObjects as $object) {
    $filter = $object->filter();

    $blackList = $filter->getBlacklistedFiles();
    $whiteList = $filter->getWhitelistedFiles();

    $mainBlackList = $mainFilter->getBlacklistedFiles();
    $mainWhiteList = $mainFilter->getWhitelistedFiles();

    $mainFilter->setBlacklistedFiles(array_merge($mainBlackList, $blackList));
    $mainFilter->setWhitelistedFiles(array_merge($mainWhiteList, $whiteList));
}

foreach ($coverageObjects as $object) {
    $mainReport->merge($object);
}

$writer = new PHP_CodeCoverage_Report_HTML();
$writer->process($mainReport, __DIR__ . '/../coverage/html/');
