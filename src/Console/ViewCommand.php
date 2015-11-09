<?php

namespace Mwc\Generators\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Filesystem\Filesystem;

class ViewCommand extends Command
{

    use AppNamespaceDetectorTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $name = 'generate:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new view';

    /**
     * Meta information for the requested command.
     *
     * @var array
     */
    protected $type = 'view';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Meta information for the requested command.
     *
     * @var array
     */
    protected $meta;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller']
        ];
    }

    protected function getOptions()
    {
        return [
            ['office', null, InputOption::VALUE_REQUIRED, 'The office of the controller', null],
            ['action', null, InputOption::VALUE_OPTIONAL, 'The office of the controller', null],
        ];
    }

    public function handle()
    {
        $this->makeController();
    }

    protected function handleMeta()
    {

        $this->meta['name'] = $this->argument('name');

        $this->meta['office'] = $this->option('office');

        $this->meta['action'] = $this->option('action');

    }

    /**
     * Generate the desired view.
     */
    protected function makeView()
    {
        $this->handleMeta();

        if ($this->files->exists($path = $this->getPath($this->meta['class']))) {
            return $this->error($this->meta['name'] . ' ' . $this->type . ' already exists!');
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->compileStub());

        $this->info('View created successfully.');
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        if(($office = $this->meta['office']) && ($action = $this->meta['action']))
        {
            return base_path() . '/resources/views/{$office}/{$name}.{$action}.blade.php';
        } else {
            return base_path() . '/resources/views/' . $name . '.blade.php';
        }
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileStub()
    {
        return view('view', $this->meta)->render();
    }
}
