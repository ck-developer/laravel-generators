<?php

namespace Mwc\Generators\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerCommand extends GeneratorCommand
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $name = 'generate:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new controller';

    /**
     * Meta information for the requested command.
     *
     * @var array
     */
    protected $type = 'controller';

    /**
     * Meta information for the requested command.
     *
     * @var array
     */
    protected $meta;

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller']
        ];
    }

    protected function getOptions()
    {
        return [
            ['office', null, InputOption::VALUE_OPTIONAL, 'The office of the controller', null],
            ['empty', 'e', InputOption::VALUE_NONE, 'Generate a new empty controller'],
        ];
    }

    public function handle()
    {
        $this->makeController();
    }

    protected function handleMeta()
    {

        $this->meta['name'] = str_replace(['controller', 'Controller'], '', $this->argument('name'));

        $this->meta['office'] = $this->option('office');

        $this->meta['options']['empty'] = $this->option('empty');

        if(($office = $this->meta['office']))
        {
            $this->meta['namespace'] = $this->getAppNamespace() . 'Http\\Controllers\\' . ucfirst($office);
        }
        else {
            $this->meta['namespace'] = $this->getAppNamespace() . 'Http\\Controllers';
        }

        $this->meta['class'] = ucfirst($this->meta['name']) . ucfirst($this->type);
    }

    /**
     * Generate the desired migration.
     */
    protected function makeController()
    {
        $this->handleMeta();

        if ($this->filesystem->exists($path = $this->getPath($this->meta['class']))) {
            $confirm = $this->ask('you want to replace your controller ? [y] /', 'N');
            if($confirm == 'y')
            {
                $this->filesystem->delete($path);
            } else {
                return $this->error($this->meta['name'] . ' ' . $this->type . ' is aborded!');
            }
        }

        $this->makeDirectory($path);

        $this->filesystem->put($path, $this->compileControllerStub());

        $this->phpCsFixer->fix($path);

        $this->info('Controller created successfully.');
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        if(($office = $this->meta['office']))
        {
            return app_path() . '/Http/controllers/'. ucfirst($office) .'/' . $name . '.php';
        } else {
            return app_path() . '/Http/controllers/' . $name . '.php';
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
        if (!$this->filesystem->isDirectory(dirname($path))) {
            $this->filesystem->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return view('laravel-generators::controller', $this->meta)->render();
    }
}
