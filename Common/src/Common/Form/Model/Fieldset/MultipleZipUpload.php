<?php

namespace Common\Form\Model\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Attributes({
 *     "class": "file-uploader"
 * })
 */
class MultipleZipUpload
{
    /**
     * @Form\Name("file")
     * @Form\Attributes({
     *   "class": "js-visually-hidden"
     * })
     * @Form\Options({
     *     "value": "Upload .Zip file(s)",
     *     "hint": "Files must be a zip file"
     * })
     * @Form\Type("\Common\Form\Elements\Types\AttachFilesButton")
     */
    public $controls = null;

    /**
     * @Form\Attributes({
     *   "class": "js-upload-list"
     * })
     * @Form\Options({"preview_images": "false"})
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
     * @Form\Attributes({"type":"submit","class":"inline-upload action--primary js-upload", "value": "Submit"})
     * @Form\Options({
     *     "label": "Submit"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $upload = null;
}
