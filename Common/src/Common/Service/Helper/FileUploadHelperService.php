<?php

/**
 * File Upload Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Common\Exception\File\InvalidMimeException;
use Zend\Validator\File\FilesSize;
use Common\Exception\ConfigurationException;

/**
 * File Upload Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileUploadHelperService extends AbstractHelperService
{
    const MAX_FILE_SIZE = '25MB';

    private $form;

    private $selector;

    private $countSelector;

    private $uploadCallback;

    private $deleteCallback;

    private $loadCallback;

    private $request;

    private $element;

    public function getForm()
    {
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    public function getSelector()
    {
        return $this->selector;
    }

    public function setSelector($selector)
    {
        $this->selector = $selector;
        return $this;
    }

    public function getCountSelector()
    {
        return $this->countSelector;
    }

    public function setCountSelector($selector)
    {
        $this->countSelector = $selector;
        return $this;
    }

    public function getUploadCallback()
    {
        return $this->uploadCallback;
    }

    public function setUploadCallback($uploadCallback)
    {
        $this->uploadCallback = $uploadCallback;
        return $this;
    }

    public function getDeleteCallback()
    {
        return $this->deleteCallback;
    }

    public function setDeleteCallback($deleteCallback)
    {
        $this->deleteCallback = $deleteCallback;
        return $this;
    }

    public function getLoadCallback()
    {
        return $this->loadCallback;
    }

    public function setLoadCallback($loadCallback)
    {
        $this->loadCallback = $loadCallback;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function getElement()
    {
        if ($this->element === null) {
            $this->element = $this->findElement($this->getForm(), $this->getSelector());
        }
        return $this->element;
    }

    public function setElement($element)
    {
        $this->element = $element;
        return $this;
    }

    /**
     * Process file uploads/deletions and list population
     *
     * @return boolean
     */
    public function process()
    {
        $processed = false;

        if ($this->getRequest()->isPost() && $this->processFileUploads()) {
            $processed = true;
        }

        $this->populateFileList();

        if ($this->getRequest()->isPost() && $this->processFileDeletions()) {
            $processed = true;
        }

        return $processed;
    }

    /**
     * Populate file list
     *
     * @return boolean
     * @throws \Common\Exception\ConfigurationException
     */
    private function populateFileList()
    {
        $callback = $this->getLoadCallback();

        if ($callback === null) {
            return;
        }

        if (!is_callable($callback)) {
            throw new ConfigurationException('Load data callback is not callable');
        }

        $url = $this->getServiceLocator()->get('Helper\Url');

        $files = call_user_func($callback);

        $element = $this->getElement();

        $element->get('list')->setFiles($files, $url);

        $this->updateCount(count($files));

    }

    protected function updateCount($count)
    {
        $selector = $this->getCountSelector();

        if (!is_null($selector)) {
            $this->findElement($this->getForm(), $selector)->setValue($count);
        }
    }

    protected function decrementCount()
    {
        $selector = $this->getCountSelector();

        if (!is_null($selector)) {
            $element = $this->findElement($this->getForm(), $selector);
            $count = (int)$element->getValue();
            if ($count>0) {
                $element->setValue($count - 1);
            }
        }
    }

    /**
     * Process a file uploads
     *
     * @NOTE we return true if we have processed the files, regardless of state
     *
     * @return boolean
     */
    private function processFileUploads()
    {
        // If we don't have a callable upload callback, we can just return false
        $callback = $this->getUploadCallback();

        if (!is_callable($callback)) {
            return false;
        }

        // Check if the upload button has been pressed
        $postData = $this->findSelectorData((array)$this->getRequest()->getPost(), $this->getSelector());
        $fileData = $this->findSelectorData((array)$this->getRequest()->getFiles(), $this->getSelector());

        /**
         * @TODO: these next two statements *are* temporary; the old MultipleFileUpload element groups
         * all its inputs (including the file itself) under a nested 'file-controls' fieldset. The updated
         * mechanism using form annotations and the MultipleFileUpload fieldset can't do that. However, to
         * preserve BC we simply copy the top-level file data onto the expected 'file-controls' key to avoid
         * disruption and to maintain both
         *
         * At some point we should nuke the MultipleFileUpload _element_ altogether and remove this
         */
        if (isset($postData) && !isset($postData['file-controls'])) {
            $postData['file-controls'] = $postData;
        }
        if (isset($fileData) && !isset($fileData['file-controls'])) {
            $fileData['file-controls'] = $fileData;
        }

        if ($postData === null
            || $fileData === null
            || !isset($postData['file-controls']['upload'])
            || empty($postData['file-controls']['upload'])
            || !isset($fileData['file-controls']['file'])
        ) {
            return false;
        }

        $error = $fileData['file-controls']['file']['error'];

        $validator = new FilesSize(self::MAX_FILE_SIZE);

        if ($error == UPLOAD_ERR_OK && !$validator->isValid($fileData['file-controls']['file']['tmp_name'])) {
            $error = UPLOAD_ERR_INI_SIZE;
        }

        switch ($error) {
            case UPLOAD_ERR_OK:
                try {
                    call_user_func(
                        $callback,
                        $fileData['file-controls']['file']
                    );
                } catch (InvalidMimeException $ex) {
                    $this->invalidMime();
                    return false;
                } catch (\Exception $ex) {
                    $this->failedUpload();
                    return false;
                }
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->getForm()->setMessages($this->formatErrorMessageForForm('File was only partially uploaded'));
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->getForm()->setMessages($this->formatErrorMessageForForm('Please select a file to upload'));
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->getForm()->setMessages($this->formatErrorMessageForForm('The file was too large to upload'));
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                $this->getForm()->setMessages(
                    $this->formatErrorMessageForForm('An unexpected error occurred while uploading the file')
                );
                break;
        }

        return true;
    }

    /**
     * Process a single file deletion
     *
     * @return array
     */
    private function processFileDeletions()
    {
        $callback = $this->getDeleteCallback();

        if (!is_callable($callback)) {
            return false;
        }

        $postData = $this->findSelectorData((array)$this->getRequest()->getPost(), $this->getSelector());

        if ($postData === null
            || !isset($postData['list'])
        ) {
            return false;
        }

        $element = $this->getElement()->get('list');

        foreach ($element->getFieldsets() as $listFieldset) {

            $name = $listFieldset->getName();

            if (isset($postData['list'][$name]['remove'])
                && !empty($postData['list'][$name]['remove'])) {

                $success = call_user_func(
                    $callback,
                    $postData['list'][$name]['id']
                );

                if ($success === true) {
                    $element->remove($name);
                    $this->decrementCount();
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Build the error array for the form
     *
     * @param string $message
     * @return array
     */
    private function formatErrorMessageForForm($message)
    {
        $array = array();
        $reference = &$array;
        $selector = $this->getSelector();

        while (strstr($selector, '->')) {
            list($index, $selector) = explode('->', $selector, 2);

            $reference[$index] = array();

            $reference = &$reference[$index];
        }

        $reference[$selector]['__messages__'] = array($message);

        return $array;
    }

    /**
     * Find the selector index of the given data
     *
     * @param array $data
     * @param string $selector
     * @return array
     */
    private function findSelectorData($data, $selector)
    {
        if (strstr($selector, '->')) {
            list($index, $selector) = explode('->', $selector, 2);

            if (!isset($data[$index])) {
                return null;
            }

            return $this->findSelectorData($data[$index], $selector);
        }

        if (!isset($data[$selector])) {
            return null;
        }

        return $data[$selector];
    }

    /**
     * Find the element by the selector
     *
     * @param \Zend\Form\ElementInterface $form
     * @param string $selector
     * @return \Zend\Form\ElementInterface
     */
    private function findElement($form, $selector)
    {
        if (strstr($selector, '->')) {
            list($element, $selector) = explode('->', $selector, 2);
            return $this->findElement($form->get($element), $selector);
        }

        return $form->get($selector);
    }

    private function invalidMime()
    {
        $this->getForm()->setMessages($this->formatErrorMessageForForm('ERR_MIME'));
    }

    private function failedUpload()
    {
        $this->getForm()->setMessages($this->formatErrorMessageForForm('unknown_error'));
    }
}
