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
     * @Form\Name("file-controls")
     * @Form\Attributes({
     *   "class": "js-visually-hidden"
     * })
     * @Form\Options({
     *     "value": "Attach file",
     *     "hint": "20MB maximum file size. PDF, DOC, JPG, PNG or GIF"
     * })
     * @Form\Type("\Common\Form\Elements\Types\FileUploadButton")
     */
    public $controls = null;

    /**
     * @Form\Attributes({})
     * @Form\Options({})
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
     * @Form\Attributes({"type":"submit","class":"inline-upload js-visually-hidden"})
     * @Form\Options({
     *     "label": "Upload"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $upload = null;
}
