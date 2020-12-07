<?php

namespace Common\Form\Model\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Attributes({
 *     "class": "file-uploader"
 * })
 */
class MultipleFileUpload
{
    /**
     * @Form\Required(false)
     * @Form\Type("Hidden")
     * @Form\Validator({"name": "Laminas\Validator\NotEmpty", "options": {"null"}})
     * @Form\Validator({"name": "Common\Validator\FileUploadCountV2", "options": {"min": 1}})
     */
    public $fileCount = null;

    /**
     * @Form\Name("file")
     * @Form\Attributes({
     *   "class": "js-visually-hidden"
     * })
     * @Form\Options({
     *     "value": "common.file-upload.browse.title",
     *     "hint": "common.file-upload.browse.hint"
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
     * @Form\Attributes({
     *     "type":"submit",
     *     "class":"inline-upload action--primary js-upload",
     *     "value": "Upload",
     * })
     * @Form\Options({
     *     "label": "Upload"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $upload = null;
}
