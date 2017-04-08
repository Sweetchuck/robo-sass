<?php

use Cheppers\Robo\Sass\SassTaskLoader;
use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Symfony\Component\Finder\Finder;

// @codingStandardsIgnoreStart
class SassRoboFile extends Tasks
// @codingStandardsIgnoreEnd
{
    use SassTaskLoader;

    public function compileFiles(
        string $name,
        array $options = [
            'style' => 'expanded',
            'comments' => false,
            'cssPath' => '',
            'mapPath' => '',
            'embed' => false,
            'indent' => 4,
            'precision' => 4,
            'includePaths' => [],
        ]
    ): TaskInterface {
        $files = (new Finder())
            ->in(__DIR__ . '/scss')
            ->files()
            ->name($name);

        return $this
            ->taskSassCompile($options)
            ->setFiles($files);
    }
}
