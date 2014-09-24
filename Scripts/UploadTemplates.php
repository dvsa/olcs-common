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
        if ($handle = opendir($source)) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry !== "." && $entry !== "..") {
                    $file = new \Dvsa\Jackrabbit\Data\Object\File();
                    $file->setContent(
                        file_get_contents($source . '/' . $entry)
                    );
                    $file->setMimeType('application/rtf');

                    $path = $name . '/' . str_replace(" ", "_", $entry);

                    echo "Uploading $path\n";

                    $r = $this->client->write($path, $file);
                    if ($r->isSuccess()) {
                        echo "OK\n";
                    } else {
                        echo "ERROR: " . $r->getStatusCode() . "\n";
                    }
                }
            }

            closedir($handle);
        }
    }
}

$worker = new TemplateWorker($argv);

$data = $worker->readWorkspace();

$worker->deleteFolder('/tmp', $data);
$worker->deleteFolder('/templates', $data);

$worker->uploadFolder('templates', $argv[1]);
