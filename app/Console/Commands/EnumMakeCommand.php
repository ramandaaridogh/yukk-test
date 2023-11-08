<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class EnumMakeCommand extends GeneratorCommand
{
    protected $name = 'make:enum';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name : Enum name you want to use.}';


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Enum';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path('Console/Stubs/enum.stub');
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
        return $rootNamespace . '\Enums';
    }
}
