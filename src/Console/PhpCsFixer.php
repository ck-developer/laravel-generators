<?php

namespace Mwc\Generators\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;

class PhpCsFixer
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The working path to regenerate from.
     *
     * @var string
     */
    protected $workingPath;

    /**
     * Create a new Composer manager instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  string $workingPath
     */
    public function __construct(Filesystem $files, $workingPath = null)
    {
        $this->files = $files;
        $this->workingPath = $workingPath;
    }

    /**
     * Fix php files with Php cs fixer.
     *
     * @param  string  $path
     * @return void
     */
    public function fix($path)
    {

        $process = $this->getProcess();

        $process->setCommandLine(trim($this->findPhpCsFixer().' fix '. $path . ' --level=psr2'));

        $process->run();
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findPhpCsFixer()
    {
        if (!$this->files->exists($this->workingPath.'/php-cs-fixer.phar')) {
            return 'php vendor/bin/php-cs-fixer';
        }

        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));

        if (defined('HHVM_VERSION')) {
            $binary .= ' --php';
        }

        return "{$binary} ";
    }

    /**
     * Get a new Symfony process instance.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess()
    {
        return (new Process('', $this->workingPath))->setTimeout(null);
    }

    /**
     * Set the working path used by the class.
     *
     * @param  string  $path
     * @return $this
     */
    public function setWorkingPath($path)
    {
        $this->workingPath = realpath($path);

        return $this;
    }
}