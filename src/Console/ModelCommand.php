<?php

namespace Mwc\Generators\Console;

use Ck\Generators\Parsers\FieldsParser;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Filesystem\Filesystem;

class ModelCommand extends GeneratorCommand
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $name = 'generate:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new model';

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the controller']
        ];
    }

    protected function getOptions()
    {
        return [
            ['table', null, InputOption::VALUE_OPTIONAL, 'The table of model', null],
            ['fields', null, InputOption::VALUE_OPTIONAL, 'The fields of model', null]
        ];
    }

    public function handle()
    {
        $this->makeModel();
    }

    protected function handleData()
    {
        $inputs = [];

        $inputs['class'] = ucfirst($this->argument('name'));

        if(!$this->option('table'))
        {
            $default = strtolower($this->argument('name')) . 's';

            $inputs['table'] = $this->askWithCompletion('The table of model ?', [$default], $default);
        }

        if(!$this->option('fields'))
        {
            while(true) {
                $fieldName = $this->ask('New field name (press <q> to stop adding fields)');

                if($fieldName == "q")
                {
                    break;
                }

                $defaultType = 'string';

                if (substr($fieldName, -3) == '_at') {
                    $defaultType = 'timestamp';
                } elseif (substr($fieldName, -3) == '_id') {
                    $defaultType = 'integer';
                } elseif (substr($fieldName, 0, 3) == 'is_') {
                    $defaultType = 'integer';
                } elseif (substr($fieldName, 0, 4) == 'has_') {
                    $defaultType = 'boolean';
                }

                $fieldType = $this->anticipate('Field type ', ['datetime', 'date', 'timestamp', 'integer', 'integer', 'json'], $defaultType);

                $data = ['name' => $fieldName, 'type' => $fieldType];

                if($fieldType == 'string')
                {
                    $data['length'] = $this->ask('Field length', 255, 255);
                }

                $inputs['fields'][] = $data;
            }
        }

        $this->data = $inputs;

    }

    /**
     * Generate the desired model.
     */
    protected function makeModel()
    {
        $this->handleData();

        if ($this->filesystem->exists($path = $this->getPath($this->data['class']))) {
            $confirm = $this->ask('you want to replace your controller ? [y] /', 'N');
            if($confirm == 'y')
            {
                $this->filesystem->delete($path);
            } else {
                return $this->error($this->data['name'] . ' ' . $this->type . ' is aborded!');
            }
        }

        $this->makeDirectory($path);

        $this->filesystem->put($path, $this->compileModelView());

        $this->phpCsFixer->fix($path);

        $this->info('Model created successfully.');
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return app_path() . '/Models/' . $name . '.php';
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
    protected function compileModelView()
    {
        return view('laravel-generators::model', $this->data)->render();
    }
}
