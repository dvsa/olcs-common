<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({
 *     "class": "file-uploader"
 * })
 */
class MultipleFileUpload
{
    /**
     * @Form\Name("file")
     * @Form\Attributes({
     *   "class": "js-visually-hidden"
     * })
     * @Form\Options({
     *     "value": "Attach file(s)",
     *     "hint": "PDF, DOC, JPG, PNG or GIF"
     * })
     * @Form\Type("\Common\Form\Elements\Types\AttachFilesButton")
     */
    public $controls = null;

    /**
     * @Form\Attributes({
     *   "class": "js-upload-list"
     * })
     * @Form\Options({"preview_images": "true"})
     * @Form\Type("\Common\Form\Elements\Types\FileUploadList")
     */
    public $list = null;

    /**
     * @Form\Name("__messages__")
     * @Form\Attributes({})
     * @Form\Options({})
     * @Form\Type("Hidden")
     */
    public $messages = null;

    /**
     * @Form\Attributes({"type":"submit","class":"inline-upload js-upload", "value": "Upload"})
     * @Form\Options({
     *     "label": "Upload"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $upload = null;
}
