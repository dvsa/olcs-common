<?php
// @codingStandardsIgnoreFile

require("vendor/autoload.php");

class TemplateWorker
{
    private $client;

    public function __construct()
    {
        $client = new \Dvsa\Olcs\DocumentShare\Service\Client();

        $request = new \Zend\Http\Request();
        $request->getHeaders()->addHeaderLine('uuid', 'uD12345');

        $http = new \Zend\Http\Client();
        $http->setRequest($request);
        $http->setOptions(['timeout' => 120]);

        $client->setHttpClient($http);

        $client->setBaseUri('http://fs01.olcs.mgt.mtpdvsa:8080/hfs/');

        $client->setWorkspace('olcs');

        $this->client = $client;
    }

    public function uploadFolder($name, $source)
    {
        $handle = opendir($source);

        if (!$handle) {
            echo "Could not open directory: " . $source . "\n";
            return;
        }

        while (false !== ($entry = readdir($handle))) {

            if (substr($entry, 0, 1) === ".") {
                continue;
            }

            if (is_dir($source.'/'.$entry)) {
                $this->uploadFolder('templates/' . $entry, $source . '/' . $entry);
            } else {
                $data = file_get_contents($source . '/' . $entry);

                $file = new \Dvsa\Olcs\DocumentShare\Data\Object\File();
                $file->setContent($data);

                $path = $name . '/' . str_replace(" ", "_", $entry);

                echo "Removing $path... ";

                $result = $this->client->remove($path);

                if ($result->isSuccess()) {
                    echo "OK\n";
                } else {
                    echo "ERROR: " . $result->getStatusCode() . "\n";
                }

                echo "Uploading $path... ";

                $result = $this->client->write($path, $file);

                if ($result->isSuccess()) {
                    echo "OK\n";
                } else {
                    echo "ERROR: " . $result->getStatusCode() . "\n";
                }
            }
        }

        closedir($handle);
    }
}

$baseDir = 'templates';
if (isset($argv[2])) {
    $baseDir = $argv[2];
}

$worker = new TemplateWorker($argv);

$worker->uploadFolder($baseDir, $argv[1]);
