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
    }

    protected function updateComposerJson()
    {
        $file = json_decode(file_get_contents(getcwd() . '/composer.json'), true);
        $file['name'] = 'jeyroik/extas-' . $this->packageName;
        file_put_contents(getcwd() . '/composer.json', json_encode($file, JSON_PRETTY_PRINT));
    }

    protected function updateExtasJson()
    {
        $file = json_decode(file_get_contents(getcwd() . '/extas.json'), true);
        $file['name'] = 'extas/' . $this->packageName;
        file_put_contents(getcwd() . '/extas.json', json_encode($file, JSON_PRETTY_PRINT));
    }

    protected function updateReadMeMd()
    {
        // not implemented yet
    }
}
