<?php

namespace Common\Validator;

/**
 * This is a simple validator to check the number of files upload through our ajax uploader,
 * It is not very configurable (as doesn't need to be)
 *
 * It can be added to any form element, as it checks file uploads from the context rather than the value
 *
 * @package Common\Validator
 */
class FileUploadCount extends \Zend\Validator\AbstractValidator
{
    const TOO_FEW = 'fileCountTooFew';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::TOO_FEW => "Too few files uploaded",
    );

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'min' => 0
    );

    /**
     * Set the min number of file uploads required
     *
     * @param int $min
     * @throws Exception\InvalidArgumentException
     */
    public function setMin($min)
    {
        if (!is_numeric($min)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $this->options['min'] = $min;
    }

    /**
     * Get the min number of file uploads required
     *
     * @return int
     */
    public function getMin()
    {
        return (int) $this->options['min'];
    }

    /**
     * is the value valid
     *
     * @param  mixed $value
     * @param  array $context
     *
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        if ($this->getNumberOfFilesUploaded($context) >= $this->getMin()) {
            return true;
        }
        $this->error(self::TOO_FEW);

        return false;
    }

    /**
     * Get the number of files uploaded
     *
     * @param array $context
     *
     * @return int
     */
    private function getNumberOfFilesUploaded($context)
    {
        if (isset($context['files']['list']) && is_array($context['files']['list'])) {
            return count($context['files']['list']);
        }

        return 0;
    }
}
