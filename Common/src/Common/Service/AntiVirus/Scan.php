<?php

namespace Common\Service\AntiVirus;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Olcs\Logging\Log\Logger;

use Interop\Container\ContainerInterface;

/**
 * AntiVirus Scan a file
 */
class Scan implements FactoryInterface
{
    /**
     * @var string
     */
    private $cliCommand;

    /**
     * @var \Common\Filesystem\Shell
     */
    private $shell;

    /**
     * Scan a file for viruses
     *
     * @param string $file File to scan
     *
     * @return bool true = file is clean, false = file failed the virus scan
     */
    public function isClean($file)
    {
        $this->validatecliCommand();

        if (!is_string($file)) {
            throw new \InvalidArgumentException('file to scan must be a string');
        }
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("Cannot scan '$file' as it does not exist");
        }

        $existingFilePerms = $this->shell->fileperms($file);

        try {
            // change the file's permissions to let scanning to run
            $this->shell->chmod($file, 0660);

            $result = $this->shell->execute($this->getCliCommandFile($file));
        } catch (\Exception $ex) {
            $result = -1;

            Logger::notice(
                sprintf(
                    'Unable to scan the file. File: %s, Error: %s',
                    $file,
                    $ex->getMessage()
                )
            );
        }

        try {
            // revert back the file's permissions
            $this->shell->chmod($file, $existingFilePerms);
        } catch (\Exception $ex) {
            Logger::warn(
                sprintf(
                    'Unable to revert the file permissions. File: %s, Perms: %s, Error: %s',
                    $file,
                    substr(decoct($existingFilePerms), -4),
                    $ex->getMessage()
                )
            );
        }

        return $result === 0;
    }

    /**
     * Is the scanner enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return !empty($this->getCliCommand());
    }

    /**
     * Validate the cli command
     *
     * @throws \Common\Exception\ConfigurationException
     *
     * @return void
     */
    private function validatecliCommand()
    {
        if (empty($this->getCliCommand())) {
            throw new \Common\Exception\ConfigurationException('Scan cliCommand is not set.');
        }
        if (strpos($this->getCliCommand(), '%s') === false) {
            throw new \Common\Exception\ConfigurationException(
                '%s must be in the cliCommand, this is where the file to be scanned is inserted'
            );
        }
    }

    /**
     * Get the cli command to run
     *
     * @param string $file File name to scan
     *
     * @return string
     */
    private function getCliCommandFile($file)
    {
        return sprintf($this->getCliCommand(), $file);
    }

    /**
     * Get Cli Command
     *
     * @return string
     */
    public function getCliCommand()
    {
        return $this->cliCommand;
    }

    /**
     * Set Cli Command
     *
     * @param string $cliCommand Cli Command
     *
     * @return void
     */
    public function setCliCommand($cliCommand)
    {
        $this->cliCommand = $cliCommand;
    }

    /**
     * Set the shell to use
     *
     * @param \Common\Filesystem\Shell $shell Shell
     *
     * @return void
     */
    public function setShell(\Common\Filesystem\Shell $shell)
    {
        $this->shell = $shell;
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (isset($config['antiVirus']['cliCommand'])) {
            $this->cliCommand = $config['antiVirus']['cliCommand'];
        }
        $this->setShell(new \Common\Filesystem\Shell());
        return $this;
    }
}
