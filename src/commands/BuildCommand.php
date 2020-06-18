<?php
namespace extas\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class BuildCommand
 *
 * @package extas\commands
 * @author jeyroik <jeyroik@gmail.com>
 */
class BuildCommand extends Command
{
    protected string $packageName = '';
    protected string $userName = '';
    protected string $resourcesPath = __DIR__ . '/../../resources';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('run')
            ->setAliases([])
            ->setDescription('Run package building')
            ->setHelp('Run skeleton package building.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['', '<info>Running skeleton package builder v0.1.0</info>', '']);

        $helper = $this->getHelper('question');
        $question = new Question('Please enter the name of the package: ', 'new');
        $this->packageName = $helper->ask($input, $output, $question);

        $question = new Question('Please enter package owner name: ', 'jeyroik');
        $this->userName = $helper->ask($input, $output, $question);

        $output->writeln([
            '',
            '<info>Starting building package "' . $this->packageName . '" by "' . $this->userName . '"...</info>',
            ''
        ]);

        foreach ($this->getFilesForUpdate() as $filename => $method) {
            $this->$method();
            $output->writeln(['<comment> - ' . $filename . ' updated</comment>']);
        }

        $this->printInstructions($output);

        return 0;
    }

    /**
     * @param OutputInterface $output
     */
    protected function printInstructions(OutputInterface $output): void
    {
        $output->writeln([
            '', '<info>Package building finished.</info>', '',
            '<info>Please, do now:</info>',
            '<info> - See codecov instructions in the CODECOV.md.</info>',
            '<info> - See code climate instructions in the CODECLIMATE.md.</info>',
            '<info> - Paste code-climate link into the README.md.</info>',
            '<info> - Paste release dates in the VERSIONS.md</info>', ''
        ]);
    }

    /**
     * @return \Generator
     */
    protected function getFilesForUpdate()
    {
        foreach ([
            'composer.json' => 'updateComposerJson',
            'extas.json' => 'updateExtasJson',
            'README.md' => 'updateReadMeMd',
            'CODECOV.md' => 'updateCodeCovMd',
            'CODECLIMATE.md' => 'updateCodeClimateMd',
            'tests/' . $this->packageName . '/'. ucfirst($this->packageName) . 'Test.php' => 'createTest'
        ] as $filename => $method) {
            yield $filename => $method;
        }
    }

    /**
     * Update package name + remove builder dep
     */
    protected function updateComposerJson(): void
    {
        $this->updateFileContent(
            $this->resourcesPath . '/composer.json',
            getcwd() . '/composer.json'
        );
    }

    /**
     * Update package name
     */
    protected function updateExtasJson(): void
    {
        $this->updateFileContent(getcwd() . '/extas.json');
    }

    /**
     * Update badges
     */
    protected function updateReadMeMd(): void
    {
        $this->updateFileContent($this->resourcesPath . '/README.md', getcwd() . '/README.md');
    }

    /**
     * Update instructions for code coverage
     */
    protected function updateCodeCovMd(): void
    {
        $this->updateFileContent(getcwd() . '/CODECOV.md');
    }

    /**
     * Update instructions for code climate
     */
    protected function updateCodeClimateMd(): void
    {
        $this->updateFileContent(getcwd() . '/CODECLIMATE.md');
    }

    /**
     * Create extas.json test draft
     */
    protected function createTest(): void
    {
        $path = getcwd() . '/tests/' . $this->packageName;

        mkdir($path, 0755);
        $this->updateFileContent(
            $this->resourcesPath . '/Test.php',
            $path . '/' . ucfirst($this->packageName) . 'Test.php'
        );
    }

    /**
     * @param string $pathToGet
     * @param string $pathToPut
     */
    protected function updateFileContent(string $pathToGet, string $pathToPut = ''): void
    {
        $pathToPut = $pathToPut ?: $pathToGet;

        file_put_contents(
            $pathToPut,
            str_replace(
                ['@package', '@user', '@Package'],
                [$this->packageName, $this->userName, ucfirst($this->packageName)],
                file_get_contents($pathToGet)
            )
        );
    }
}
