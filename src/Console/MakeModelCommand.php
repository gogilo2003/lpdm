<?php

namespace Gogilo\Lpdm\Console;

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

class MakeModelCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('make:model')
            ->setDescription('Make model')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('path', 'p', InputOption::VALUE_NONE, 'Specify the path to the place the new model otherwise assumed to be current path')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces overite if the model exists');
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

        $output->write(PHP_EOL . '<fg=red>Make Model</>' . PHP_EOL . PHP_EOL);

        sleep(1);


        return 0;
    }
}
