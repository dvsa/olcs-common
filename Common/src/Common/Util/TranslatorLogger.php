<?php

namespace Common\Util;

use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\Translator as ZendTranslator;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Log all the translation used by a Request
 */
class TranslatorLogger
{
    const CACHE_KEY = 'TranslatorLogger_messagesWritten';

    /**
     * @var File
     */
    private $logFilePointer;

    /**
     * @var array
     */
    private $messagesWritten;

    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;


    /**
     * TranslatorLogger constructor.
     *
     * @param string                            $logFileName Log file name
     * @param \Zend\Http\PhpEnvironment\Request $request     Request
     */
    public function __construct($logFileName, \Zend\Http\PhpEnvironment\Request $request)
    {
        $this->logFilePointer = fopen($logFileName, 'a');
        $this->request = $request;

        if (apcu_exists(self::CACHE_KEY)) {
            $this->messagesWritten = apcu_fetch(self::CACHE_KEY);
        }
        //fputcsv($this->logFilePointer, ['URL', 'Message', 'English', 'Welsh']);
    }

    /**
     *  Destructor to clear the file pointer
     */
    public function __destruct()
    {
        apc_store(self::CACHE_KEY, $this->messagesWritten, 60 * 60 * 72);
        fclose($this->logFilePointer);
    }

    /**
     * Log translation
     *
     * @param string $message Message to be translated
     * @param string $english English version of the translation
     * @param string $welsh   Welsh version of the translation
     *
     * @return void
     */
    public function logTranslations($message, $translator)
    {
        if (in_array($message, $this->messagesWritten)) {
            return;
        }

        $this->messagesWritten[] = $message;

        fputcsv(
            $this->logFilePointer,
            [$this->request->getRequestUri(), $message, $translator->translate($message)]
        );
    }
}
