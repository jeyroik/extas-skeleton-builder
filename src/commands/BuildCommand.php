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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['', '<info>Running skeleton package builder v0.1.0</info>', '']);

        $helper = $this->getHelper('question');
        $question = new Question('Please enter the name of the package: ', 'new');
        $this->packageName = $helper->ask($input, $output, $question);

        $output->writeln(['', '<info>Starting building package "' . $this->packageName . '"...</info>', '']);

        $this->updateComposerJson();
        $output->writeln(['<comment> - composer.json updated</comment>']);

        $this->updateExtasJson();
        $output->writeln(['<comment> - extas.json updated</comment>']);

        $this->updateReadMeMd();
        $output->writeln(['<comment> - README.md updated</comment>']);

        $output->writeln([
            '', '<info>Package building finished.</info>', '',
            '<info>Please, do now:</info>',
            '<info> - Remove skeleton-builder dependency in the composer.json.</info>',
            '<info> - Paste code-climate link into README.md.</info>', ''
        ]);

        return 0;
    }

    protected function updateComposerJson()
    {
        $path = getcwd() . '/composer.json';
        file_put_contents(
            $path,
            str_replace(
                'jeyroik/extas-skeleton',
                'jeyroik/extas-' . $this->packageName,
                file_get_contents($path)
            )
        );
    }

    protected function updateExtasJson()
    {
        $path = getcwd() . '/extas.json';
        file_put_contents(
            $path,
            str_replace(
                '@package',
                'extas/' . $this->packageName,
                file_get_contents($path)
            )
        );
    }

    protected function updateReadMeMd()
    {
        file_put_contents(
            getcwd() . '/README.md',
            str_replace(
                '@package',
                $this->packageName,
                file_get_contents(__DIR__ . '/../../resources/README.md')
            )
        );
    }
}
