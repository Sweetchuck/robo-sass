<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Sass\Test\Helper\RoboFiles;

use Sweetchuck\Robo\Sass\SassTaskLoader;
use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Symfony\Component\Finder\Finder;

class SassRoboFile extends Tasks
{
    use SassTaskLoader;

    public function compileFiles(
        string $directory,
        string $name,
        array $options = [
            'style' => 'expanded',
            'comments' => false,
            'cssPath' => '',
            'mapPath' => '',
            'embed' => false,
            'indent' => '4',
            'precision' => '4',
            'includePaths' => [],
        ]
    ): TaskInterface {
        $files = (new Finder())
            ->in($directory)
            ->files()
            ->name($name);

        return $this
            ->taskSassCompile($options)
            ->setFiles($files);
    }
}
