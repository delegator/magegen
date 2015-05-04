<?php

namespace Delegator\Magegen\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends AbstractCommand
{
    protected $_hookName = 'init';

    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Creates a package.template.xml file. Update this file with your extension\'s information.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->stepHook('pre');

        $xmlTemplate = <<<EOX
<?xml version="1.0"?>
<package>
    <name></name>
    <version></version>
    <stability></stability>
    <license uri=""></license>
    <channel></channel>
    <extends/>
    <summary></summary>
    <description></description>
    <notes></notes>
    <authors>
        <author>
            <name></name>
            <user></user>
            <email></email>
        </author>
    </authors>
    <date />
    <time />
    <contents />
    <compatible/>
    <dependencies>
        <required>
            <php>
                <min>5.2.0</min>
                <max>6.0.0</max>
            </php>
        </required>
    </dependencies>
</package>
EOX;
        $output->write('Generating XML... ');
        $xmlFile = fopen('package.template.xml', 'w');
        fwrite($xmlFile, $xmlTemplate);
        fclose($xmlFile);
        $output->writeln('<info>Done.</info>');

        $this->stepHook('post');
    }
}
