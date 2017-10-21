<?php

use Sweetchuck\Robo\Sass\SassTaskLoader;
use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Symfony\Component\Finder\Finder;

class SassRoboFile extends Tasks
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
        // There is bug with the default values if the data type is numeric.
        // Default values are overwritten with NULL values.
        foreach (['indent' => 4, 'precision' => 4] as $key => $value) {
            if (!isset($options[$key])) {
                $options[$key] = $value;
            }
        }

        $files = (new Finder())
            ->in(__DIR__ . '/scss')
            ->files()
            ->name($name);

        return $this
            ->taskSassCompile($options)
            ->setFiles($files);
    }
}
