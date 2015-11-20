<?php

namespace Delegator\Magegen\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends AbstractCommand
{
    protected $_hookName = 'build';

    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Builds a standalone package for Magento Connect')
            ->addOption('--package-template-xml-file', null, InputOption::VALUE_REQUIRED, 'Specifies the path to the extension package.template.xml file.', 'package.template.xml')
            ->addOption('--output-directory', null, InputOption::VALUE_REQUIRED, 'Specifies the directory where the built extension file should be placed.', getcwd())
            ->addOption('--modman-file', null, InputOption::VALUE_REQUIRED, 'Specifies the path to the extension modman file.', 'modman');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->stepHook('pre');

        //Load Magento core
        $mageFile = realpath(getcwd() . '/../../app/Mage.php');
        if (!@include_once($mageFile)) {
            throw new \Exception ('Can\'t find Mage.php. Are you running this from your `.modman` directory?');
        } else {
            require_once($mageFile);
        }

        //Boilerplate
        umask(0);
        \Mage::app();

        $output->write('Generating XML... ');
        // Generate a package XML file from our template and modman file
        $packageXmlFile = $this->_generatePackageXmlFile(
            $input->getOption('package-template-xml-file'),
            $input->getOption('modman-file')
        );
        $packageXmlFile = getcwd() . '/' . $packageXmlFile;
        $output->writeln('<info>Done.</info>');

        //Build package
        $output->write('Building package... ');
        chdir('../../');
        $package = new \Mage_Connect_Package($packageXmlFile);
        $package->save($input->getOption('output-directory'));

        $output->writeln('<info>Done.</info>');

        $this->stepHook('post');
    }

    private function _generatePackageXmlFile($packageFilename, $modmanFilename)
    {
        $xml = simplexml_load_file($packageFilename);
        $xml->date = date('Y-m-d');
        $xml->time = date('H:i:s');

        $target = $xml->contents->addChild('target');
        $target->addAttribute('name', 'mageweb');

        // Parse modman file and add directories/files to modman array
        $modmanFP = fopen($modmanFilename, 'r');
        $modman = $this->_parseModmanFile($modmanFP);

        // Step through the modman array, find all of the files the need to be
        // listed in the package.xml
        $files = array();
        foreach ($modman as $line) {
            $target = trim($line[0]);
            $importPath = trim($line['importPath']);

            if (is_file($target)) {
                $directory = preg_replace('/[^\/]+$/', '', $target);
            } else {
                $directory = preg_replace('/\*/', '', $target);
            }

            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $filename) {
                // Skip files that end with a "."
                // put the rest in the $files array
                if (!preg_match('/\.$/', $filename)) {

                    // If this is from an imported modman file
                    $path = $filename->getPath();
                    if ($importPath != '') {
                        $path = str_replace($importPath, '', $path);
                    }

                    $files[$path][] = $filename->getFileName();
                }
            }
        }

        // Step through the files array. Find the correct node for the file.
        // Add it to the XML with an md5sum
        foreach ($files as $dir => $filenames) {
            $node = $this->_findOrCreateNode($dir, $xml);

            foreach ($filenames as $filename) {
                $fileNode = $node->addChild('file');
                $fileNode->addAttribute('name', $filename);
                $fileNode->addAttribute('hash', md5_file('../../' . $dir . '/' . $filename));
            }
        }

        $packageXml = fopen('package.xml', 'w');

        fwrite($packageXml, $xml->asXML());
        fclose($packageXml);
        return 'package.xml';
    }

    private function _parseModmanFile($fileHandle, $prefix = '')
    {
        $modman = array();

        while ($line = fgets($fileHandle)) {
            // Check for comments, or blank lines
            if (preg_match('/^[#|\n]/', $line)) {
                continue;
            }

            $parts = preg_split('/ /', $line);

            // Check for '@import' lines
            if (preg_match('/^@import/', $line)) {
                $importedModmanFile = preg_replace('/\n/', '', $parts[1]);
                $importedModman = fopen($importedModmanFile . '/modman', 'r');
                $modman = array_merge(
                    $modman,
                    $this->_parseModmanFile($importedModman, $importedModmanFile . '/')
                );
                continue;
            }

            $modman[] = array(
                0 => $prefix . $parts[0],
                'importPath' => $prefix
            );
        }

        return $modman;
    }

    private function _findOrCreateNode($dir, &$xml)
    {
        // Split the $dir into each directory name.
        // Step through the directories, creating new nodes as needed.
        $dirs = preg_split('/\//', $dir);
        $currentNode = $xml->contents->target;
        foreach ($dirs as $directory) {
            $nodes = $currentNode->xpath('dir[@name="' . $directory . '"]');
            if ($nodes) {
                $currentNode = $nodes[0];
            } else {
                $currentNode = $currentNode->addChild('dir');
                $currentNode->addAttribute('name', $directory);
            }
        }

        return $currentNode;
    }

}
