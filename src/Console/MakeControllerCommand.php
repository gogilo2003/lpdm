<?php

namespace Gogilo\Lpdm\Console;

use Gogilo\Lpdm\Util;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class MakeControllerCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        /*
        --api              Exclude the create and edit methods from the controller.
        --type=TYPE        Manually specify the controller stub file to use.
        --force            Create the class even if the controller already exists
        -i, --invokable        Generate a single method, invokable controller class.
        -m, --model[=MODEL]    Generate a resource controller for the given model.
        -p, --parent[=PARENT]  Generate a nested resource controller class.
        -r, --resource         Generate a resource controller class.
        -R, --requests         Generate FormRequest classes for store and update.
        --test             Generate an accompanying PHPUnit test for the Controller
        --pest             Generate an accompanying Pest test for the Controller
        */
        $this
            ->setName('make:controller')
            ->setDescription('Make controller')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('path', NULL, InputOption::VALUE_OPTIONAL, 'Specify the path to the package root otherwise assumed to be current path.')
            ->addOption('api', NULL, InputOption::VALUE_NONE, 'Exclude the create and edit methods from the controller.')
            // ->addOption('type', NULL, InputOption::VALUE_OPTIONAL, 'Manually specify the controller stub file to use.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces overite if the controller exists.')
            ->addOption('invokable', 'i', InputOption::VALUE_NONE, 'Generate a single method, invokable controller class.')
            ->addOption('model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.')
            ->addOption('parent', 'p', InputOption::VALUE_OPTIONAL, 'Generate a nested resource controller class.')
            ->addOption('resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller class.')
            ->addOption('requests', 'R', InputOption::VALUE_NONE, 'Generate a resource controller class.')
            ->addOption('test', NULL, InputOption::VALUE_NONE, 'Generate an accompanying PHPUnit test for the Controller.')
            ->addOption('pest', NULL, InputOption::VALUE_NONE, 'Generate an accompanying Pest test for the Controller.');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->write(PHP_EOL . '<fg=red>Make Controller</>' . PHP_EOL . PHP_EOL);

        sleep(1);

        $arrName = explode('/', $input->getArgument('name'));
        $controllerName = $arrName[count($arrName) - 1];

        $arrNamespace = $arrName;
        array_pop($arrNamespace);
        foreach ($arrNamespace as $key => $value) {
            $arrNamespace[$key] = ucfirst($value);
        }
        $namespace = '\\' . implode('\\', $arrNamespace);

        // $output->writeln($name);
        $controllerNamespace = Util::getBaseNamespace($input, $namespace);
        $packageNamespace = Util::getBaseNamespace($input);

        $output->writeln($this->prepareController($controllerName, $namespace, $packageNamespace, $input->getOption('path')));

        return 0;
    }

    protected function prepareController($name, $namespace, $packageNamespace, $path = null)
    {
        $stub = file_get_contents(Util::getStubFile('controller'));
        $dest = Util::getControllerPath($path) . str_replace('\\', '/', $namespace);
        // print("ControllerPath: " . $controllerPath . "\n");
        if (!file_exists($dest))
            mkdir($dest, 0777, true);

        $dest .=  '/' . $name . '.php';
        file_put_contents($dest, $stub);

        Util::replaceInFile('{{ namespace }}', $packageNamespace . $namespace, $dest);
        Util::replaceInFile('{{ packageNamespace }}', $packageNamespace, $dest);
        Util::replaceInFile('{{ class }}', $name, $dest);

        return $dest;
    }
}
