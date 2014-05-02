<?php

/**
 * Release
 *
 * Handle's the release process
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Runner
{
    const TYPE_MINOR = 1;
    const TYPE_MAJOR = 2;

    const MESSAGE_DEFAULT = "\e[39m";
    const MESSAGE_OK = "\e[34m";
    const MESSAGE_ERROR = "\e[31m";
    const MESSAGE_SUCCESS = "\e[32m";
    const MESSAGE_INFO = "\e[33m";

    public function __construct()
    {

    }

    /**
     * Run the script
     */
    public function run()
    {
        try {
            $this->runCommand();
        } catch (Exception $ex) {
            $this->output($ex->getMessage(), self::MESSAGE_ERROR);
        }
    }

    /**
     * Run the command
     *
     * @throws Exception
     */
    private function runCommand()
    {
        $this->output('Checking repositories', self::MESSAGE_INFO);
        foreach ($this->getRepos() as $repo) {

            $checkAgain = false;

            $repo->fetchOrigin();

            if (!$repo->isOnDevelop()) {

                $checkAgain = true;
                $repo->checkoutDevelop();
            }

            if ($repo->hasUncommittedChanges()) {
                throw new Exception($repo->getName() . ': Has uncommitted changes');
            }

            $repo->pullDevelop();

            if ($checkAgain && !$repo->isOnDevelop()) {
                throw new Exception($repo->getName() . ': Is not on develop, please corrent this before continuing');
            }
        }

        $this->output('Creating release branches', self::MESSAGE_INFO);
        foreach ($this->getRepos() as $repo) {

            $repo->createRelease($this->getNextRelease());
        }
    }

    /**
     * Output message
     *
     * @param string $message
     */
    public function output($message, $type = self::MESSAGE_OK)
    {
        echo $type . $message . "\n" . self::MESSAGE_DEFAULT;
    }

    /**
     * Get a list of repos
     *
     * @return array
     */
    private function getRepos()
    {
        return array(
            //new Repo(__DIR__, $this),
            new Repo(__DIR__ . '/../olcs-backend', $this),
            new Repo(__DIR__ . '/../olcs-entities', $this),
            new Repo(__DIR__ . '/../olcs-internal', $this),
            new Repo(__DIR__ . '/../olcs-selfserve', $this),
            new Repo(__DIR__ . '/../olcs-frontend-styleguide', $this)
        );
    }

    /**
     * Get the version number of the next release
     *
     * @return array
     */
    private function getNextRelease()
    {
        list($lastMajor, $lastMinor) = $this->getLastTag();

        switch($this->getReleaseType()) {
            case self::TYPE_MINOR:
                $newMajor = $lastMajor;
                $newMinor = ($lastMinor + 1);
                break;
            case self::TYPE_MAJOR:
                $newMajor = $lastMajor;
                $newMinor = ($lastMinor + 1);
                break;
        }

        return array($newMajor, $newMinor);
    }

    /**
     * Get the last version tag
     *
     * @todo implement this when we have a tag
     * @return array
     */
    private function getLastTag()
    {
        return array(0, 1);
    }

    /**
     * Determine what release type we are wanting
     *
     * @todo implement this
     * @return int
     */
    private function getReleaseType()
    {
        return self::TYPE_MINOR;
    }
}

/**
 * Repo object
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Repo
{
    private $runner;

    private $location;

    private $name;

    private $status;

    public function __construct($location, $runner)
    {
        $this->runner = $runner;

        $this->location = realpath($location);

        $parts = explode('/', $this->location);

        $this->name = array_pop($parts);

        $this->loadStatus();
    }

    /**
     * Create release branch
     *
     * @param array $release
     */
    public function createRelease($release)
    {
        $releaseName = $release[0] . '.' . $release[1];

        $this->output('Creating release ' . $releaseName);
        //shell_exec('cd ' . $this->getLocation() . ' && git flow release start ' . $releaseName);
        //$this->loadStatus();
    }

    /**
     * Set the current status
     */
    private function loadStatus()
    {
        $this->status = shell_exec('cd ' . $this->getLocation() . ' && git status');
    }

    /**
     * Get the location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get the name of the repo
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the status of the repo
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Fetch origin
     */
    public function fetchOrigin()
    {
        $this->output('Fetching origin');
        shell_exec('cd ' . $this->getLocation() . ' && git fetch -p origin');
        $this->loadStatus();
    }

    /**
     * Check if a repo is on develop
     *
     * @return boolean
     */
    public function isOnDevelop()
    {
        $this->output('Checking if repo is on develop');

        if (preg_match('/^On branch ([a-z]+)/', $this->getStatus(), $matches)) {

            return ($matches[1] === 'develop');
        }

        return false;
    }

    /**
     * Checkout the latest develop
     */
    public function checkoutDevelop()
    {
        $this->output('Checking if we can check out develop');

        if (strstr($this->getStatus(), 'nothing to commit')) {

            $this->output('Checking out develop');

            shell_exec('cd ' . $this->getLocation() . ' && git checkout develop');
            $this->loadStatus();

        } else {

            throw new Exception($this->getName() . ' has uncommitted changes, please correct this and re-run the script');
        }
    }

    /**
     * Pull the latest develop
     */
    public function pullDevelop()
    {
        if (!strstr($this->getStatus(), 'Your branch is up-to-date with \'origin/develop\'')) {
            $this->output('Pulling latest develop');
            shell_exec('cd ' . $this->getLocation() . ' && git pull origin develop');
            $this->loadStatus();
        } else {
            $this->output('Up to date', Runner::MESSAGE_SUCCESS);
        }
    }

    /**
     * Check if we have uncommited changes
     *
     * @return boolean
     */
    public function hasUncommittedChanges()
    {
        return !(strstr($this->getStatus(), 'nothing to commit'));
    }

    /**
     * Output a message
     *
     * @param string $message
     */
    public function output($message, $type = Runner::MESSAGE_OK)
    {
        $this->runner->output($this->getName() . ': ' . $message, $type);
    }
}

$release = new Runner();

$release->run();

