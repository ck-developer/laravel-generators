<?php

namespace Mwc\Generators\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\AppNamespaceDetectorTrait;

class GeneratorCommand extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The PHP CS Fixer command.
     *
     * @var PhpCsFixer
     */
    protected $phpCsFixer;

    /**
     * The data of command.
     *
     * @var array
     */
    protected $data;

    public function __construct(Filesystem $filesystem, PhpCsFixer $phpCsFixer)
    {
        $this->filesystem = $filesystem;
        $this->phpCsFixer = $phpCsFixer;

        parent::__construct();
    }
}