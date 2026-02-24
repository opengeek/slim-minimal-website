<?php

namespace Opengeek\Console;

use Opengeek\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Cache
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function clear(OutputInterface $output): int
    {
        $locations = $this->configuration->get('caches');

        $locations = array_filter($locations);

        $finder = new Finder();
        foreach ($finder->files()->in($locations) as $file) {
            $output->writeln('Deleting file ' . $file->getRealPath());
            unlink($file->getRealPath());
        }

        $directories = iterator_to_array($finder->directories()->in($locations)->sort(static function (\SplFileInfo $a, \SplFileInfo $b) {
            $depth = substr_count($a->getRealPath(), '/') - substr_count($b->getRealPath(), '/');
            return ($depth === 0)? strcmp($a->getRealPath(), $b->getRealPath()) : -$depth;
        }));
        foreach ($directories as $directory) {
            $output->writeln('Deleting directory ' . $directory->getRealPath());
            rmdir($directory->getRealPath());
        }

        return Command::SUCCESS;
    }
}
