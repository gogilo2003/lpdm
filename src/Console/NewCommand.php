<?php

namespace Gogilo\Lpdm\Console;

use Gogilo\Lpdm\Util;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Laravel package')
            ->addArgument('vendor', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Path where the package should be created')
            ->addOption('vue', null, InputOption::VALUE_NONE, 'Setup vue with package')
            ->addOption('git', null, InputOption::VALUE_NONE, 'Create a repository for the new package')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces install even if the directory already exists')
            ->addOption('api', 'a', InputOption::VALUE_NONE, 'Creates package with rest api ready development features');
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


        $output->write(PHP_EOL . '<fg=red>Create a new laravel package</>' . PHP_EOL . PHP_EOL);

        // sleep(1);

        if (!$input->getOption('force')) {
            $this->verifyApplicationDoesntExist(Util::getPackageDirectory($input));
        }

        if ($input->getOption('force') && Util::getPackageDirectory($input) === '.') {
            throw new RuntimeException('Cannot use --force option when using current directory for installation!');
        }

        $commands = [];

        if ($input->getOption('api')) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "src/Http/Controllers/Api") . "\"");
        }

        if (PHP_OS_FAMILY == 'Windows') {
            array_unshift($commands, "type nul \"" . Util::getPackageDirectory($input, 'resources/scss/' . $input->getArgument('name') . '.scss') . "\"");
            array_unshift($commands, "type \"require('./bootstrap');\" \"" . Util::getPackageDirectory($input, 'resources/js/' . $input->getArgument('name') . '.js') . "\"");
            array_unshift($commands, "type nul \"" . Util::getPackageDirectory($input, 'resources/js/bootstrap.js') . "\"");
            array_unshift($commands, "RENAME \"" . Util::getPackageDirectory($input, 'config/config.php') . "\" \"" . Util::getPackageDirectory($input, 'config/' . $input->getArgument('name') . '.php') . "\"");
        } else {
            array_unshift($commands, "touch \"" . Util::getPackageDirectory($input, 'resources/scss/' . $input->getArgument('name') . '.scss') . "\"");
            array_unshift($commands, "touch \"" . Util::getPackageDirectory($input, 'resources/js/' . $input->getArgument('name') . '.js') . "\"");
            array_unshift($commands, "touch \"" . Util::getPackageDirectory($input, 'resources/js/bootstrap.js') . "\"");
            array_unshift($commands, "mv \"" . Util::getPackageDirectory($input, 'config/config.php') . "\" \"" . Util::getPackageDirectory($input, 'config/' . $input->getArgument('name') . '.php') . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "resources/js"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "resources/js") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "resources/css"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "resources/css") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "resources/scss"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "resources/scss") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "resources/views"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "resources/views") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "resources"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "resources") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "src/Console"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "src/Console") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "src/Http/Controllers"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "src/Http/Controllers") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "src/Http/Resources"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "src/Http/Resources") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "src/Http"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "src/Http") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "src/Models"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "src/Models") . "\"");
        }

        if (!file_exists(Util::getPackageDirectory($input, "src"))) {
            array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input, "src") . "\"");
        }

        // if (!file_exists(Util::getPackageDirectory($input))) {
        //     array_unshift($commands, "mkdir \"" . Util::getPackageDirectory($input) . "\"");
        // }

        if (PHP_OS_FAMILY == 'Windows') {
            array_unshift($commands, "xcopy \"" . Util::getSkeletonPath() . "\" \"" . Util::getPackageDirectory($input) . "\\\" /E/H ");
        } else {
            $command = "cp -r \"" . Util::getSkeletonPath() . "\" \"" . Util::getPackageDirectory($input) . "\"";
            array_unshift($commands, $command);
        }

        if (!file_exists(Util::getVendorDirectory($input))) {
            array_unshift($commands, "mkdir \"" . Util::getVendorDirectory($input) . "\"");
        }

        if (Util::getVendorDirectory($input) != '.' && $input->getOption('force')) {
            if (PHP_OS_FAMILY == 'Windows') {
                array_unshift($commands, "rd /s /q \"" . Util::getVendorDirectory($input) . "\"");
            } else {
                array_unshift($commands, "rm -rf \"" . Util::getVendorDirectory($input) . "\"");
            }
        }

        if (($process = $this->runCommands($commands, $input, $output))->isSuccessful()) {
            // Prepare composer.json
            $this->prepareComposer($input);

            //Prepare service provider
            $this->prepareServiceProvider($input, $output);

            //Prepare base controller
            $this->prepareBaseController($input, $output);

            // if ($input->getOption('git') || $input->getOption('github') !== false) {
            //     $this->createRepository($vendorDir, $input, $output);
            // }

            // if ($input->getOption('github') !== false) {
            //     $this->pushToGitHub($name, $vendorDir, $input, $output);
            // }

            $output->writeln(PHP_EOL . '<comment>New package setup, create something amazing</comment>');
        }

        return $process->getExitCode();
        // return 0;
    }

    /**
     * Prepare composer.json
     */
    protected function prepareComposer($input)
    {
        $vendor = $input->getArgument('vendor');
        $package = $input->getArgument('name');
        Util::replaceInFile('{{ vendorName }}', Util::getVendorName($vendor), Util::getComposerPath(Util::getPackageDirectory($input)));
        Util::replaceInFile('{{ vendorNamespace }}', Util::getVendorNamespaceName($vendor), Util::getComposerPath(Util::getPackageDirectory($input)));
        Util::replaceInFile('{{ packageName }}', Util::getPackageName($package), Util::getComposerPath(Util::getPackageDirectory($input)));
        Util::replaceInFile('{{ packageNamespace }}', Util::getPackageNamespaceName($package), Util::getComposerPath(Util::getPackageDirectory($input)));
    }

    /**
     * Prepare Base Controller
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return Integer
     */
    protected function prepareBaseController(InputInterface $input, OutputInterface $output)
    {
        $commands = [];

        $src = Util::getStubFile('base.controller' . ($input->getOption('api') ? '.api' : ''));
        $dest = Util::getPackageDirectory($input, ($input->getOption('api') ? 'src/Http/Controllers/Api/Controller.php' : 'src/Http/Controllers/Controller.php'));

        if (PHP_OS_FAMILY == 'Windows') {
            array_unshift($commands, "copy \"$src\" \"$dest\"");
        } else {
            array_unshift($commands, "cp -r \"$src\" \"$dest\"");
        }

        if (($process = $this->runCommands($commands, $input, $output))->isSuccessful()) {
            Util::replaceInFile('{{ packageNamespace }}', Util::getNamespace($input->getArgument('vendor'), $input->getArgument('name')), $dest);
        }
        return $process->getExitCode();
    }

    /**
     * Prepare Service Provider
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    protected function prepareServiceProvider(InputInterface $input, OutputInterface $output)
    {
        $commands = [];
        $src = Util::getStubFile('provider');
        $dest = Util::getPackageDirectory($input, 'src/' . Util::getPackageNamespaceName($input->getArgument('name')) . 'ServiceProvider.php');

        if (PHP_OS_FAMILY == 'Windows') {
            array_unshift($commands, "copy \"$src\" \"$dest\"");
        } else {
            array_unshift($commands, "cp -r \"$src\" \"$dest\"");
        }

        if (($process = $this->runCommands($commands, $input, $output))->isSuccessful()) {
            Util::replaceInFile('{{ packageNamespaceName }}', Util::getPackageNamespaceName($input->getArgument('name')), $dest);
            Util::replaceInFile('{{ packageName }}', Util::getPackageName($input->getArgument('name')), $dest);
            Util::replaceInFile('{{ packageNamespace }}', Util::getNamespace($input->getArgument('vendor'), $input->getArgument('name')), $dest);
        }

        return $process->getExitCode();
    }

    /**
     * Return the local machine's default Git branch if set or default to `main`.
     *
     * @return string
     */
    protected function defaultBranch()
    {
        $process = new Process(['git', 'config', '--global', 'init.defaultBranch']);

        $process->run();

        $output = trim($process->getOutput());

        return $process->isSuccessful() && $output ? $output : 'main';
    }

    /**
     * Create a Git repository and commit the base Laravel skeleton.
     *
     * @param  string  $vendorDir
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function createRepository(string $vendorDir, InputInterface $input, OutputInterface $output)
    {
        chdir($vendorDir);

        $branch = $input->getOption('branch') ?: $this->defaultBranch();

        $commands = [
            'git init -q',
            'git add .',
            'git commit -q -m "Set up a fresh Laravel app"',
            "git branch -M {$branch}",
        ];

        $this->runCommands($commands, $input, $output);
    }

    /**
     * Commit any changes in the current working directory.
     *
     * @param  string  $message
     * @param  string  $vendorDir
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function commitChanges(string $message, string $vendorDir, InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('git') && $input->getOption('github') === false) {
            return;
        }

        chdir($vendorDir);

        $commands = [
            'git add .',
            "git commit -q -m \"$message\"",
        ];

        $this->runCommands($commands, $input, $output);
    }

    /**
     * Create a GitHub repository and push the git log to it.
     *
     * @param  string  $name
     * @param  string  $vendorDir
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function pushToGitHub(string $name, string $vendorDir, InputInterface $input, OutputInterface $output)
    {
        $process = new Process(['gh', 'auth', 'status']);
        $process->run();

        if (!$process->isSuccessful()) {
            $output->writeln('Warning: make sure the "gh" CLI tool is installed and that you\'re authenticated to GitHub. Skipping...');

            return;
        }

        chdir($vendorDir);

        $name = $input->getOption('organization') ? $input->getOption('organization') . "/$name" : $name;
        $flags = $input->getOption('github') ?: '--private';
        $branch = $input->getOption('branch') ?: $this->defaultBranch();

        $commands = [
            "gh repo create {$name} --source=. {$flags}",
            "git -c credential.helper= -c credential.helper='!gh auth git-credential' push -q -u origin {$branch}",
        ];

        $this->runCommands($commands, $input, $output, ['GIT_TERMINAL_PROMPT' => 0]);
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string  $vendorDir
     * @return void
     */
    protected function verifyApplicationDoesntExist($vendorDir)
    {
        if ((is_dir($vendorDir) || is_file($vendorDir)) && $vendorDir != getcwd()) {
            throw new RuntimeException('Package already exists!');
        }
    }

    /**
     * Get the version that should be downloaded.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @return string
     */
    protected function getVersion(InputInterface $input)
    {
        if ($input->getOption('dev')) {
            return 'dev-master';
        }

        return '';
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        $composerPath = getcwd() . '/composer.phar';

        if (file_exists($composerPath)) {
            return '"' . PHP_BINARY . '" ' . $composerPath;
        }

        return 'composer';
    }

    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  array  $env
     * @return \Symfony\Component\Process\Process
     */
    protected function runCommands($commands, InputInterface $input, OutputInterface $output, array $env = [])
    {
        if (!$output->isDecorated()) {
            $commands = array_map(function ($value) {
                if (substr($value, 0, 5) === 'chmod') {
                    return $value;
                }

                return $value . ' --no-ansi';
            }, $commands);
        }

        if ($input->getOption('quiet')) {
            $commands = array_map(function ($value) {
                if (substr($value, 0, 5) === 'chmod') {
                    return $value;
                }

                return $value . ' --quiet';
            }, $commands);
        }

        $process = Process::fromShellCommandline(implode(' && ', $commands), null, $env, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $output->writeln('Warning: ' . $e->getMessage());
            }
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write('    ' . $line);
        });

        return $process;
    }
}
