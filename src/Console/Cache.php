<?php
/*
 * The file is part of the slim-minimal-website package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>. All Rights Reserved
 *
 * For the full copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

declare(strict_types=1);

namespace Opengeek\Console;

use Opengeek\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

readonly class Cache
{
    public function __construct(private Configuration $configuration)
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
