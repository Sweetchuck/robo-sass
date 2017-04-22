# Robo task wrapper for Sass

[![Build Status](https://travis-ci.org/Cheppers/robo-sass.svg?branch=master)](https://travis-ci.org/Cheppers/robo-sass)
[![codecov](https://codecov.io/gh/Cheppers/robo-sass/branch/master/graph/badge.svg)](https://codecov.io/gh/Cheppers/robo-sass)

@todo

## Example

```php
<?php

use Cheppers\Robo\Sass\SassTaskLoader;
use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Symfony\Component\Finder\Finder;

class SassRoboFile extends Tasks
{
    use SassTaskLoader;

    public function compileFiles(): TaskInterface
    {
        $files = (new Finder())
            ->in(__DIR__ . '/scss')
            ->files();

        return $this
            ->taskSassCompile()
            ->setFiles($files);
    }
}
```
