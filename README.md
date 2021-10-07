# Robo task wrapper for Sass

[![CircleCI](https://circleci.com/gh/Sweetchuck/robo-sass/tree/1.x.svg?style=svg)](https://circleci.com/gh/Sweetchuck/robo-sass/?branch=1.x)
[![codecov](https://codecov.io/gh/Sweetchuck/robo-sass/branch/1.x/graph/badge.svg?token=HSF16OGPyr)](https://app.codecov.io/gh/Sweetchuck/robo-sass/branch/1.x)

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
            ->in('./scss')
            ->name('/^[^_].*\.(sass|scss)$/')
            ->files();

        return $this
            ->taskSassCompile()
            ->setFiles($files);
    }
}
```
