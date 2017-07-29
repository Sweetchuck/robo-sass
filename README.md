# Robo task wrapper for Sass

[![Build Status](https://travis-ci.org/Sweetchuck/robo-sass.svg?branch=master)](https://travis-ci.org/Sweetchuck/robo-sass)
[![codecov](https://codecov.io/gh/Sweetchuck/robo-sass/branch/master/graph/badge.svg)](https://codecov.io/gh/Sweetchuck/robo-sass)

Compile SASS/SCSS files with [sass PHP extension](https://github.com/jamierumbelow/sassphp)

## Example

```php
<?php

use Sweetchuck\Robo\Sass\SassTaskLoader;
use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Symfony\Component\Finder\Finder;

class RoboFile extends Tasks
{
    use SassTaskLoader;

    public function sassCompile(): TaskInterface
    {
        $files = (new Finder())
            ->in(__DIR__ . '/scss')
            ->name('/^[^_].*\.(sass|scss)$/')
            ->files();

        return $this
            ->taskSassCompile()
            ->setFiles($files);
    }
}
```
