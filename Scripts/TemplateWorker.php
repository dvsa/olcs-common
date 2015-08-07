<?php

/**
 * @codingStandardsIgnoreFile
 * @codeCoverageIgnore
 */
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
        $http->setOptions(['timeout' => 120]);

        $client->setHttpClient($http);

        // @todo DOCUMENT STORE Set this to the new endpoint
        $client->setBaseUri('http://scdv-ap05.sc.npm:8080/hcs');
        //$client->setBaseUri('http://fs01.olcs.mgt.mtpdvsa:8080/hfs/');

        $client->setWorkspace('olcs');

        $this->client = $client;
    }

    /**
     * @todo DOCUMENT STORE Remove this method
     */
    public function readWorkspace()
    {
        return $this->client->readMeta()['structure']['children'];
    }

    /**
     * @todo DOCUMENT STORE Remove this method
     */
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

            if (substr($entry, 0, 1) === ".") {
                continue;
            }

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

        closedir($handle);
    }

    public function uploadFile($name, $folder, $source)
    {
        $data = file_get_contents($source);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($data);

        $file = new \Dvsa\Jackrabbit\Data\Object\File();
        $file->setContent($data);
        $file->setMimeType($mimeType);

        $path = $folder . '/' . str_replace(" ", "_", $name);

        echo "Uploading $path... ";

        $result = $this->client->write($path, $file);
        if ($result->isSuccess()) {
            echo "OK\n";
        } else {
            echo "ERROR: " . $result->getStatusCode() . "\n";
        }
    }
}
