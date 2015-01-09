<?php

/**
 * ensure the vrm matches the required criteria
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * ensure the vrm matches the required criteria
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */
class Vrm extends AbstractValidator
{
    /**
     * Holds the templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'foo' => 'error.vehicle.vrm-exists-on-licence',
        'bar' => 'error.vehicle.vrm-exists-on-application'
    );

    protected $exceptions = [
        '11',
        '1CZS',
        '1G',
        '1S',
        '1RAQ',
        '1V',
        'AH0',
        'BH0521',
        'BL0131',
        'EB02',
        'G0',
        'G1',
        'HS0',
        'KHW004',
        'KI1',
        'LM0',
        'OS0500',
        'OS0579',
        'QLD1',
        'QTR1',
        'QUE1',
        'RG0',
        'S0',
        'S1',
        'SY0',
        'V0',
        'V1',
        'VS0',
        'ZG',
        'ZV'
    ];

    protected $characterMap = [
        '1' => '1-9',
        '9' => '0-9',
        'A' => 'A-HJ-PR-Y',     // no I, Q, Z
        'B' => 'A-HJ-NPR-TV-Y', // no I, O, Q U, Z
        'C' => 'A-Z0-9',
        'D' => 'A-Z',
        'E' => 'A-PR-Z',        // no Q
        'Q' => 'Q',
        'Z' => 'Z'
    ];

    protected $validFormats = [
        'EE199',
        'EE1999',
        'EEE1',
        'EEE19',
        'EEE199',
        'EEE1999',
        '1EEE',
        '19EEE',
        '199EE',
        '199EEE',
        '1999EE',
        'A1999',
        'AA19',
        'AA1',
        'A199',
        'A19',
        'A1',
        '1A',
        '1AA',
        '19A',
        '19AA',
        '199A',
        '1999A',
        'B1AAA',
        'B19AAA',
        'B199AAA',
        'AAA1B',
        'AAA19B',
        'AAA199B',
        'DD99DD',
        '99DD99',
        'DD99DDD',
        '999999Z',
        'Q1AAA',
        'Q19AAA',
        'Q199AAA'
    ];

    protected $patterns = [];

    protected function getPatterns($input)
    {
        if (empty($this->patterns)) {
            $patterns = [];
            foreach ($this->validFormats as $format) {
                $key = strlen($format);
                if (!isset($patterns[$key])) {
                    $patterns[$key] = [];
                }
                $patterns[$key][] = $this->buildRegex($format);
            }
            $this->patterns = $patterns;
        }

        $length = strlen($input);
        return $this->patterns[$length];
    }

    protected function buildRegex($formatter)
    {
        $pattern = '';
        foreach (str_split($formatter) as $char) {
            $pattern .= '[' . $this->characterMap[$char] . ']';
        }
        return '/' . $pattern . '/';
    }

    /**
     * Check if VRM is valid
     *
     * @param string $value
     */
    public function isValid($value)
    {
        // @TODO strlen >= 2 && <= 7

        if (isset($this->exceptions[$value])) {
            return true;
        }

        $patterns = $this->getPatterns($value);
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        //$this->error('not-unique-' . $this->type);

        return false;
    }
}
