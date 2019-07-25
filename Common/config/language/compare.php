#!/usr/bin/php
<?php
/**
 * This is a simple PHP script to compare the two translation files (en_GB.php and cy_GB.php) to check for missing
 * entries. It outputs these in a jira-table-friendly manner so they can be added to comments etc. It also has the
 * option to output MySQL INSERT statements for adding the entries into a database quickly.
 */

/*
 * The FhAdditionalInfo.api.validation.too_short key in en_GB must be updated to remove the constant value
 * otherwise the array will not get generated and the script will not work.
 */

if ($argc === 1) {
    echo "Please specify an argument.\n";
    echo "Provide the argument [h] for help.\n";

    exit(1);
}

if ($argc > 2) {
    echo "Please only specify one argument to the script.\n";

    exit(1);
}

/*
 * The argument at the second index is used because the 'first' argument is the script file itself when run in the
 * command line.
 *
 * e.g. php [compare.php] [w|e|u]
 */
if ($argv[1] === 'e') {
    $count = 0;
    $english = include('en_GB.php');
    $welsh = include('cy_GB.php');
    $welsh_without_english = array_diff_key($welsh, $english);

    echo sprintf("Current missing English translations (%d):\n", count($welsh_without_english));
    echo "||Message key||English||Welsh||\n";
    foreach ($welsh_without_english as $key => $value) {
        echo "|" . $key . "| |" . $value . "|\n";
    }

    exit;
}

if ($argv[1] === 'w') {
    $english = include('en_GB.php');
    $welsh = include('cy_GB.php');
    $english_without_welsh = array_diff_key($english, $welsh);

    echo sprintf("Current missing Welsh translations (%d):\n", count($english_without_welsh));
    echo "||Message key||English||Welsh||\n";
    foreach ($english_without_welsh as $key => $value) {
        echo "|" . $key . "|" . $value . "| |\n";
    }

    exit;
}

if ($argv[1] === 'u') {
    $english = include('en_GB.php');
    $welsh = include('cy_GB.php');
    echo sprintf("Current untranslated Welsh (%d):\n", count(preg_grep("/(CY - )/", $welsh)));
    echo "||Message key||English||Welsh\n";

    foreach ($welsh as $key => $value) {
        if (strpos($value, 'CY - ') === false) {
            continue;
        } else {
            echo sprintf(
                "|%s|%s|%s|\n",
                $key,
                isset($english[$key]) ? $english[$key] : '',
                $value
            );
        }
    }

    exit;
}

if ($argv[1] === 'us') {
    $english = include('en_GB.php');
    $welsh = include('cy_GB.php');
    echo "Current untranslated Welsh where 'CY - ' prefix is missing:\n";
    echo "||Message key||English||Welsh\n";

    foreach ($welsh as $key => $value) {
        if (isset($english[$key]) && $english[$key] === $value) {
            echo sprintf(
                "|%s|%s|%s|\n",
                $key,
                isset($english[$key]) ? $english[$key] : '',
                $value
            );
        }
    }

    exit;
}

if ($argv[1] === 's') {
    $english = include('en_GB.php');
    $welsh = include('cy_GB.php');
    echo "Insert Statements:\n";

    foreach ($english as $key => $value) {
        if (array_key_exists($key, $welsh) && strpos($welsh[$key], 'CY - ') === false) {
            echo sprintf(
                "INSERT INTO `olcs_be.translation` (`key`, `english`, `welsh`) VALUES (`%s`, `%s`, `%s`);\n",
                $key,
                $english[$key],
                $welsh[$key]
            );
        }
    }

    exit;
}

if ($argv[1] === 'd') {
    $english = include('en_GB.php');
    $welsh = include('cy_GB.php');
    echo "Differing translations:\n";

    foreach ($welsh as $key => $value) {
        if (strpos($value, "CY - ") === false) {
            continue;
        } else {
            if (("CY - " . $english[$key]) !== $value) {
                echo sprintf(
                    "|%s|%s|%s|\n",
                    $key,
                    $english[$key],
                    $value
                );
            }
        }
    }

    exit;
}

if ($argv[1] === 'h') {
    echo "PHP Translation File Comparison Script\n";
    echo "\n";
    echo "Provide an argument in the following format to execute the script:\n";
    echo "php compare.php [e|w|u|s]\n";
    echo "\n";
    echo "e - View missing English Translations\n";
    echo "w - View missing Welsh Translations\n";
    echo "u - View untranslated Welsh (entries that start with the 'CY - ' prefix)\n";
    echo "us - View untranslated Welsh where the 'CY - ' prefix is missing\n";
    echo "s - View the SQL INSERT statements for the translation entries\n";
    echo "d - View the entries that have different translations\n";

    exit;
}

echo "You have not entered a correct parameter.\n";
echo "Provide the parameter [h] for help\n";
exit(1);
