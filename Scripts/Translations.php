<?php
// @codingStandardsIgnoreFile

$translationLocation = __DIR__ . '/../Common/config/language/';

$translations = include($translationLocation . 'en_GB.php');

$directories = array(
    realpath(__DIR__ . '/../Common/config/list-data/'),
    realpath(__DIR__ . '/../Common/src/'),
    realpath(__DIR__ . '/../test/'),
    realpath(__DIR__ . '/../Common/view/'),
    realpath(__DIR__ . '/../../olcs-internal/module/'),
    realpath(__DIR__ . '/../../olcs-internal/test/'),
    realpath(__DIR__ . '/../../olcs-selfserve/module/'),
    realpath(__DIR__ . '/../../olcs-selfserve/test/')
);

$unusedArray = $foundArray = array();

foreach ($translations as $key => $value) {

    $found = true;
    /** Ignore the grep for now just to speed things up
    $found = false;

    foreach ($directories as $directory) {
        $response = shell_exec('grep -r "' . $key . '" ' . $directory);

        if (!empty($response)) {
            $found = true;
            break;
        }
    }
     */

    $value = preg_replace('/(\s+)/', ' ', $value);

    $value = str_replace("'", "\'", $value);

    if ($found) {
        $foundArray[$key] = $value;
    } else {
        $unusedArray[$key] = $value;
    }
}

ksort($foundArray);
ksort($unusedArray);

$cyGbContent = $enGbContent = '<?php

return array(';

function wrapLine($string) {

    $newString = '';

    $remainingString = $string;

    if (strlen($remainingString) <= 120) {
        $newString = $remainingString;
    }

    while (strlen($remainingString) > 120) {

        $offset = 120 - strlen($remainingString);

        $splitSpaceOffset = strrpos($remainingString, ' ', $offset);

        $lines = substr_replace($remainingString, "\n", $splitSpaceOffset, 1);

        list($trimedLine, $remainingString) = explode("\n", $lines);

        $newString .= $trimedLine . "\n";

        $remainingString = "        " . $remainingString;

        if (strlen($remainingString) < 120) {
            $newString .= $remainingString;
        }
    }

    return $newString;
}

foreach ($foundArray as $key => $value) {

    $gbLine = wrapLine("    '" . $key . "' => '" . $value . "',");
    $cyLine = wrapLine("    '" . $key . "' => 'W " . $value . "',");

    $enGbContent .= "\n" . $gbLine;
    $cyGbContent .= "\n" . $cyLine;
}

$enGbContent .= "\n    // Potentially unused (Not found with grep)";
$cyGbContent .= "\n    // Potentially unused (Not found with grep)";

foreach ($unusedArray as $key => $value) {

    $gbLine = wrapLine("    '" . $key . "' => '" . $value . "',");
    $cyLine = wrapLine("    '" . $key . "' => 'W " . $value . "',");

    $enGbContent .= "\n" . $gbLine;
    $cyGbContent .= "\n" . $cyLine;
}

$enGbContent .= "\n);\n";
$cyGbContent .= "\n);\n";

file_put_contents($translationLocation . 'en_GB.php', $enGbContent);
file_put_contents($translationLocation . 'cy_GB.php', $cyGbContent);
