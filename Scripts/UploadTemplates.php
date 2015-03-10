<?php
// @codingStandardsIgnoreFile

require("vendor/autoload.php");

class TemplateWorker
{
    private $client;

    public function __construct()
    {
        $client = new \Dvsa\Jackrabbit\Service\Client();

        $request = new \Zend\Http\Request();
        $request->getHeaders()->addHeaderLine('uuid', 'uD12345');

        $http = new \Zend\Http\Client();
        $http->setRequest($request);

        $client->setHttpClient($http);

        $client->setBaseUri('http://scdv-ap05.sc.npm:8080/hcs');

        $client->setWorkspace('olcs');

        $this->client = $client;
    }

    public function readWorkspace()
    {
        return $this->client->readMeta()['structure']['children'];
    }

    public function deleteFolder($name, $data)
    {
        foreach ($data as $folder) {
            if ($folder['name'] !== basename($name)) {
                continue;
            }
            if ($folder['folder'] === true) {
                echo "Entering folder '" . $name . "'\n";
                foreach ($folder['children'] as $child) {
                    $this->deleteFolder($name . '/' . $child['name'], $folder['children']);
                }
            } else {
                echo "Deleting file: " . $name . "\n";
                $r = $this->client->remove($name, true);
                if ($r->isSuccess()) {
                    echo "OK\n";
                } else {
                    echo "ERROR: " . $r->getStatusCode() . "\n";
                }
            }
        }
    }

    public function uploadFolder($name, $source)
    {
        $handle = opendir($source);

        if (!$handle) {
            echo "Could not open directory: " . $source . "\n";
            return;
        }

        while (false !== ($entry = readdir($handle))) {

            if (substr($entry, 0, 1) !== ".") {
                if (is_dir($source.'/'.$entry)) {
                    $this->uploadFolder('templates/' . $entry, $source . '/' . $entry);
                } else {
                    $data = file_get_contents($source . '/' . $entry);

                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($data);

                    $file = new \Dvsa\Jackrabbit\Data\Object\File();
                    $file->setContent($data);
                    $file->setMimeType($mimeType);

                    $path = $name . '/' . str_replace(" ", "_", $entry);

                    echo "Uploading $path... ";

                    $result = $this->client->write($path, $file);
                    if ($result->isSuccess()) {
                        echo "OK\n";
                    } else {
                        echo "ERROR: " . $result->getStatusCode() . "\n";
                    }
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

echo "Reading remote workspace...\n";
$data = $worker->readWorkspace();

// always clear out tmp; it can get a bit cluttered
$worker->deleteFolder('/tmp', $data);

// then mirror whatever our directory was
$worker->deleteFolder('/' . $baseDir, $data);
$worker->uploadFolder($baseDir, $argv[1]);
