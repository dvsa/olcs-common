<?php
// @codingStandardsIgnoreFile

/**
 * Class to process welsh translations from csv file
 */
class ProcessTranslations
{
    private $csvFile;

    /**
     * Set path to csv file
     *
     * @param string $file Path to csv file
     *
     * @return $this
     */
    public function setSourceCsvFile($file)
    {
        $this->csvFile = $file;

        return $this;
    }

    /**
     * Process parse csv file and add/update new translations
     */
    public function exec(): void
    {
        //  snapshot
        $pathSnapshot = __DIR__ . '/../../olcs-backend/module/Snapshot/config/language/';
        $cyGbFileSnapshot = $pathSnapshot . 'cy_GB.php';
        $enGbFileSnapshot = $pathSnapshot . 'en_GB.php';

        $cyGb['snapshot'] = include $cyGbFileSnapshot;
        $enGb['snapshot'] = include $enGbFileSnapshot;

        //  common
        $path = __DIR__ . '/../Common/config/language/';
        $pathToPartials = __DIR__ . '/../Common/config/language/partials/';
        $cyGbFile = $path . 'cy_GB.php';
        $enGbFile = $path . 'en_GB.php';
        $cyGbRefFile = $path . 'cy_GB_refdata.php';

        $cyGb['common'] = include $cyGbFile;
        $enGb['common'] = include $enGbFile;
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
                //  define in which translation file located this message
                $inModule = null;

                foreach ($enGb as $module => $body) {
                    if (isset($body[$key])) {
                        $inModule = $module;
                        break;
                    }
                }

                $inModule = ($inModule ?: 'common');

                //  add or set values
                $val = trim($value['English']);
                if (
                    $val !== ''
                    && !isset($enGb[$inModule][$key])      //  set value only for new translations (do not update exists)
                ) {
                    $enGb[$inModule][$key] = $val;
                }

                $val = trim($value['Welsh']);
                if ($val !== '') {
                    $cyGb[$inModule][$key] = $val;
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
        }

        //  rearange items in CY to match position in EN
        echo PHP_EOL . PHP_EOL . 'Rearange items in CY array';
        foreach (['common', 'snapshot'] as $module) {
            echo PHP_EOL . ' - ' . $module;
            $cyGb[$module] = $this->rearrangeCy($enGb[$module], $cyGb[$module]);
        }

        //  save to files
        echo PHP_EOL . PHP_EOL . 'Save to files:';
        echo PHP_EOL . ' - common:';
        $this->saveArrayToFile($cyGb['common'], $cyGbFile);
        $this->saveArrayToFile($enGb['common'], $enGbFile);
        $this->saveArrayToFile($cyGbRef, $cyGbRefFile);

        echo PHP_EOL . PHP_EOL . ' - API/snapshot:';
        $this->saveArrayToFile($cyGb['snapshot'], $cyGbFileSnapshot);
        $this->saveArrayToFile($enGb['snapshot'], $enGbFileSnapshot);

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
        $rearranged = [];
        foreach (array_keys($enGb) as $key) {
            if (!isset($cyGb[$key])) {
                continue;
            }

            $rearranged[$key] = $cyGb[$key];
            unset($cyGb[$key]);
        }

        return $rearranged + $cyGb;
    }

    /**
     * Save Translations to file
     *
     * @param array  $data Translations Array;
     * @param string $file Path to target file
     */
    private function saveArrayToFile(array $data, $file): void
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
        } catch (\Exception $exception) {
            echo PHP_EOL . 'ERR: Cant store translation file ' . $file . '; ' . $exception->getMessage();
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

        //  create array with key as message key
        $result = [];

        array_walk(
            $csv,
            static function (&$a) use ($header, &$result) {
                try {
                    $tmp = array_combine($header, $a);
                    $result[$tmp['message']] = $tmp;
                } catch (\Exception $exception) {
                    echo PHP_EOL . 'something wrong with key: ' . var_export($a, 1);
                }
            }
        );

        return $result;
    }
}


/*
 * Call php -f addTranslations.php <path-to-CSV-file>
 */
(new ProcessTranslations)
    ->setSourceCsvFile($argv[1])
    ->exec();
