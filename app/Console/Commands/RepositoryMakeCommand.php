<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use App\Traits\SortableImport;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    use SortableImport;

    protected $name = 'make:repo';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repo {name : Repository name you want to use.}';


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    private $repositoryClass;
    private $model;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path('Console/Stubs/repository.stub');
    }


    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    protected function getDefaultModel($rootNamespace)
    {
        return str_replace('Repository', '', $rootNamespace);
    }


    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        if(!$this->argument('name')){
            throw new InvalidArgumentException("Missing required argument model name");
        }

        $stub = parent::replaceClass($stub, $name);

        return str_replace('DummyModel', str_replace('Repository', '', $this->argument('name')), $stub);
    }
}
