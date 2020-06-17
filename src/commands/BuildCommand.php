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
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the name of the package', 'new');
        $this->packageName = $helper->ask($input, $output, $question);

        $this->updateComposerJson();
        $output->writeln(['composer.json updated']);

        $this->updateExtasJson();
        $output->writeln(['extas.json updated']);

        $this->updateReadMeMd();
        $output->writeln(['README.md updated']);

        $output->writeln([
            'Please, do now:',
            ' - Remove skeleton-builder dependency in the composer.json.',
            ' - Paste code-climate link into README.md.'
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
