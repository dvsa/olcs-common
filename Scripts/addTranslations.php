<?php

class ProcessTranslations
{
    private $csvFile;

    public function setSourceCsvFile($file)
    {
        $this->csvFile = $file;

        return $this;
    }

    public function exec()
    {
        $path = __DIR__ . '/../Common/config/language/';
        $pathToPartials = __DIR__ . '/../Common/config/language/partials/';
        $cyGbFile = $path . 'cy_GB.php';
        $enGbFile = $path . 'en_GB.php';
        $cyGbRefFile = $path . 'cy_GB_refdata.php';

        $cyGb = include $cyGbFile;
        $enGb = include $enGbFile;
        $cyGbRef = include $cyGbRefFile;

        //  process items
        echo PHP_EOL . 'Process items: ';

        $transl = $this->parseCsv();
        foreach ($transl as $key => $value) {
            $tbt = strtoupper($value['TBT']);

            if (!in_array($tbt, ['YES', 'REF', 'FILE'], true)) {
                echo PHP_EOL . '  - SKIP (TBT !== Yes): ' . $key;
                continue;
            }

            if ($key !== strip_tags($key)) {
                echo PHP_EOL . '  - ERR: Invalid key: "' . $key . '" (key can\'t contain html);';
                continue;
            }

            if ($tbt === 'YES') {
                //  add or set values
                $val = trim($value['English']);
                if (
                    $val !== ''
                    && !isset($enGb[$key])      //  set value only for new translations (do not update exists)
                ) {
                    $enGb[$key] = $val;
                }

                $val = trim($value['Welsh']);
                if ($val !== '') {
                    $cyGb[$key] = $val;
                }

                continue;
            }

            if ($tbt === 'REF') {
                //  add or set values
                $val = trim($value['Welsh']);
                if ($val !== '') {
                    $cyGbRef[$key] = $val;
                }

                continue;
            }

            if ($tbt === 'FILE') {
                $isKeyInGb = isset($enGb[$key]);

                //  add or set values
                $filePath = $pathToPartials . 'en_GB/' . $key . '.phtml';

                $val = trim($value['English']);
                if ($val !== '') {
                    if ($isKeyInGb) {
                        $enGb[$key] = $val;
                    } elseif (!is_file($filePath)) {
                        echo PHP_EOL . '  - TO FILE: ' . $filePath;

                        file_put_contents($filePath, $val);
                    }
                }

                //  add or set values
                $val = trim($value['Welsh']);
                if ($val !== '') {
                    if ($isKeyInGb) {
                        $cyGb[$key] = $val;
                    } else {
                        $filePath = $pathToPartials . 'cy_GB/' . $key . '.phtml';
                        echo PHP_EOL . '  - TO FILE: ' . $filePath;

                        file_put_contents($filePath, $val);
                    }
                }

                continue;
            }
        }

        //  rearange items in CY to match position in EN
        $cyGb = $this->rearrangeCy($enGb, $cyGb);

        //  save to files
        echo PHP_EOL . PHP_EOL . 'Save to files:';
        $this->saveArrayToFile($cyGb, $cyGbFile);
        $this->saveArrayToFile($enGb, $enGbFile);
        $this->saveArrayToFile($cyGbRef, $cyGbRefFile);

        echo PHP_EOL . PHP_EOL . 'done!' . PHP_EOL;
    }

    /**
     * Rearrange elements in CY translations array to have same position like in EN array
     *
     * @param array $enGb English Translations
     * @param array $cyGb Welsh Translations
     *
     * @return array
     */
    private function rearrangeCy(array $enGb, array $cyGb)
    {
        //  rearange items in CY to match position in EN
        echo PHP_EOL . PHP_EOL . 'Rearange items in CY array';

        $rearranged = [];
        foreach ($enGb as $key => $value) {
            if (!isset($cyGb[$key])) {
                continue;
            }

            $rearranged[$key] = $cyGb[$key];
            unset($cyGb[$key]);
        }

        $rearranged += $cyGb;

        return $rearranged;
    }

    /**
     * Save Translations to file
     *
     * @param array  $data Translations Array;
     * @param string $file Path to target file
     *
     * @return void
     */
    private function saveArrayToFile(array $data, $file)
    {
        echo PHP_EOL . "  Save translations to " . $file;
        try {
            $fh = fopen($file, "w");

            fwrite($fh, "<?php\n// @codingStandardsIgnoreFile\nreturn ");

            $result = str_replace(
                [
                    'array (',
                    PHP_EOL . ')',
                    PHP_EOL . "  ",
                ],
                [
                    '[',
                    PHP_EOL . '  // Potentially unused translations' . PHP_EOL . '];' . PHP_EOL,
                    PHP_EOL . "    ",
                ],
                var_export($data, true)
            );

            fwrite($fh, $result);
            fclose($fh);

        } catch (\Exception $e) {
            echo PHP_EOL . 'ERR: Cant store translation file ' . $file . '; ' . $e->getMessage();
        }
    }

    /**
     * Parse CSV file and remove unused elements
     * Required csv columns: message, English, Welsh, TBT.
     *
     * @return array
     */
    private function parseCsv()
    {
        $csv = array_map('str_getcsv', file($this->csvFile));

        $header = $csv[0];
        array_shift($csv); # remove column header

        $result = [];

        array_walk($csv, function (&$a) use ($header, &$result) {
            $tmp = array_combine($header, $a);
            $result[$tmp['message']] = $tmp;
        });

        return $result;
    }
}


/*
 * Call php -f addTranslations.php <path-to-CSV-file>
 */
(new ProcessTranslations)
    ->setSourceCsvFile($argv[1])
    ->exec();
