<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

class BuildVendorCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('build:vendor')
            ->setDescription('remove unnecessary files from vendor')
            ->addArgument('folder', InputArgument::REQUIRED, 'path of vendor folder');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $folder = $input->getArgument('folder');
        var_dump($folder);
        $this->cleanDevelopVendorFiles($folder, $output);
    }

    protected function ignoreVendorFiles()
    {
        return array(
            'appveyor.yml',
            'CONTRIBUTING.md',
            'CONTRIBUTORS.md',
            'phpunit',
            'CHANGELOG.md',
            'composer.json',
            'phpunit.xml.dist',
            'Gemfile',
            'README.md',
            'UPGRADE.md',
            'VERSION',
            'CHANGES',
            'CHANGELOG',
            'changelog.txt',
            'README',
            'README_zh_CN.md',
            '.travis.yml',
        );
    }

    /**
     * remove test file and document
     *
     * @param $folder
     * @param $output
     *
     * @return bool
     */
    protected function cleanDevelopVendorFiles($folder, $output)
    {
        if (!file_exists($folder)) {
            return false;
        }
        $finder = new Finder();
        $filesystem = new Filesystem();

        $finder->in($folder)->depth('<= 3')->ignoreUnreadableDirs(true)->ignoreDotFiles(false);

        foreach ($finder as $folder) {
            if (in_array($folder->getFilename(), array('tests', 'Tests', 'test', 'testing'))) {
                $output->writeln('    - remove  folder : '.$folder->getRelativePath().'/'.$folder->getFilename());
                $filesystem->remove($folder->getRealPath());
            }

            if (!$folder->isFile()) {
                continue;
            }

            if (in_array($folder->getFilename(), $this->ignoreVendorFiles()) || strrpos($folder->getFilename(), '.') === 0 || strrpos($folder->getFilename(), '.md') !== false) {
                $output->writeln('    - remove  File : '.$folder->getRelativePath().'/'.$folder->getFilename());
                $filesystem->remove($folder->getRealPath());
            }
        }
    }
}
