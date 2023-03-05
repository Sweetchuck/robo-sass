# Robo task wrapper for Sass

[![CircleCI](https://circleci.com/gh/Sweetchuck/robo-sass/tree/3.x.svg?style=svg)](https://circleci.com/gh/Sweetchuck/robo-sass/?branch=3.x)
[![codecov](https://codecov.io/gh/Sweetchuck/robo-sass/branch/3.x/graph/badge.svg?token=escM0wp66c)](https://app.codecov.io/gh/Sweetchuck/robo-sass/branch/3.x)

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
