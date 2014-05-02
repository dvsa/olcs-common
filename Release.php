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
    /**
     * Release types
     */
    const TYPE_MINOR = 1;
    const TYPE_MAJOR = 2;

    /**
     * Message types
     */
    const MESSAGE_DEFAULT = "\e[39m";
    const MESSAGE_OK = "\e[34m";
    const MESSAGE_ERROR = "\e[31m";
    const MESSAGE_SUCCESS = "\e[32m";
    const MESSAGE_INFO = "\e[33m";

    /**
     * Release type
     *
     * @var int
     */
    private $type = self::TYPE_MINOR;

    /**
     * Holds the nextRelease
     *
     * @var array
     */
    private $nextRelease = array();

    /**
     * Format any command line arguments (In the future)
     */
    public function __construct()
    {
        $options = getopt('t:');

        if (isset($options['t']) && defined('self::TYPE_' . strtoupper($options['t']))) {

            $this->setType(constant('self::TYPE_' . strtoupper($options['t'])));
        }
    }

    /**
     * Setter for type
     *
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Determine what release type we are wanting
     *
     * @return int
     */
    private function getType()
    {
        return $this->type;
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

        $version = $this->getNextRelease();

        $version = $version[0] . '.' . $version[1];

        foreach ($this->getRepos() as $repo) {

            $repo->setVersion($version);

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

            $repo->createRelease();
        }

        $this->updateReleaseVersion();

        foreach ($this->getRepos() as $repo) {

            $repo->updateComposerJson();
        }

        foreach ($this->getRepos() as $repo) {

            if ($repo->hasUncommittedChanges()) {

                $repo->commitChanges(
                    'Updated composer dependencies and release number: ' . $version
                );
            }

            $repo->publish();
        }
    }

    /**
     * Update release version
     */
    private function updateReleaseVersion()
    {
        $this->output('Updating release number in config');

        if (file_exists(__DIR__ . '/Common/config/release.json')) {
            $version = $this->getNextRelease();

            $release = json_decode(file_get_contents(__DIR__ . '/Common/config/release.json'), true);

            $release['version'] = $version[0] . '.' . $version[1];

            if (!file_put_contents(__DIR__ . '/Common/config/release.json', json_encode($release))) {
                throw new Exception('Unable to write to release.json');
            }
        } else {
            throw new Exception('No release.json found');
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
            new Repo(__DIR__, $this),
            new Repo(__DIR__ . '/../olcs-backend', $this),
            new Repo(__DIR__ . '/../olcs-entities', $this),
            new Repo(__DIR__ . '/../olcs-internal', $this),
            new Repo(__DIR__ . '/../olcs-selfserve', $this)
        );
    }

    /**
     * Get the version number of the next release
     *
     * @return array
     */
    private function getNextRelease()
    {
        if (empty($this->nextRelease)) {
            list($lastMajor, $lastMinor) = $this->getLastTag();

            switch($this->getType()) {
                case self::TYPE_MINOR:
                    $newMajor = $lastMajor;
                    $newMinor = ($lastMinor + 1);
                    break;
                case self::TYPE_MAJOR:
                    $newMajor = ($lastMajor + 1);
                    $newMinor = 0;
                    break;
            }

            $this->nextRelease = array($newMajor, $newMinor);
        }

        return $this->nextRelease;
    }

    /**
     * Get the last version tag
     *
     * @todo implement this when we have a tag
     * @return array
     */
    private function getLastTag()
    {
        $tag = shell_exec('git tag');

        $tag = trim($tag, "\n");

        if (empty($tag)) {
            throw new Exception('No current tag found');
        }

        $tags = explode("\n", $tag);

        if (count($tags) === 1) {

            $lastTag = $tags[0];

        } else {
            $lastTag = array_pop($tags);
        }

        $lastTag = str_replace('v', '', $lastTag);

        return explode('.', $lastTag, 2);
    }
}

/**
 * Repo object
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Repo
{
    /**
     * The release version number
     *
     * @var string
     */
    private $version;

    /**
     * The script runner
     *
     * @var object
     */
    private $runner;

    /**
     * The repo location
     *
     * @var string
     */
    private $location;

    /**
     * The repo name
     *
     * @var string
     */
    private $name;

    /**
     * The git status
     *
     * @var string
     */
    private $status;

    /**
     * Pass in the location and runner
     *
     * @param string $location
     * @param object $runner
     */
    public function __construct($location, $runner)
    {
        $this->runner = $runner;

        $this->location = realpath($location);

        $parts = explode('/', $this->location);

        $this->name = array_pop($parts);

        $this->loadStatus();
    }

    /**
     * Setter for version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        if (empty($version)) {
            throw new Exception('Version number is empty');
        }
        $this->version = 'v' . $version;
    }

    /**
     * Getter for version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
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
     * Set the current status
     */
    private function loadStatus()
    {
        $this->status = shell_exec('cd ' . $this->getLocation() . ' && git status');
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
     * Create release branch
     */
    public function createRelease()
    {
        $this->output('Creating release ' . $this->getVersion());
        shell_exec('cd ' . $this->getLocation() . ' && git flow release start ' . $this->getVersion());
        $this->loadStatus();
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
     * Commit changes
     *
     * @param string $message
     */
    public function commitChanges($message)
    {
        $this->output('Committing changes');

        shell_exec('cd ' . $this->getLocation() . ' && git add . && git commit -m "' . $message . '"');
    }

    /**
     * Publish repo
     */
    public function publish()
    {
        $this->output('Publishing release branch');

        shell_exec('cd ' . $this->getLocation() . ' && git flow release publish ' . $this->getVersion());
    }

    /**
     * Update the dependency versions in composer
     *
     * @param array $version
     */
    public function updateComposerJson()
    {
        $this->output('Looking for composer.json');

        $composerFile = $this->getLocation() . '/composer.json';
        if (file_exists($composerFile)) {

            $this->output('Updating composer.json');

            $composer = json_decode(file_get_contents($composerFile), true);

            if (isset($composer['repositories'])) {

                foreach ($composer['repositories'] as &$dependency) {

                    if (isset($dependency['package'])) {

                        $dependency['package']['version'] = 'release-' . $this->getVersion();
                        $dependency['package']['source']['reference'] = 'origin/release/' . $this->getVersion();

                        $this->output('Updating dependency: ' . $dependency['package']['name']);
                    }
                }
            }

            if (!file_put_contents($composerFile, json_encode($composer))) {

                throw new Exception($this->getName() . ': Could not write to ' . $composerFile);
            }

            $this->output('Composer updated', Runner::MESSAGE_SUCCESS);
        }

        $this->loadStatus();
    }

    /**
     * Output a message
     *
     * @param string $message
     */
    private function output($message, $type = Runner::MESSAGE_OK)
    {
        $this->runner->output($this->getName() . ': ' . $message, $type);
    }
}

$release = new Runner();

$release->run();
