<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Sass;

use Robo\Collection\CollectionBuilder;

trait SassTaskLoader
{
    /**
     * @return \Sweetchuck\Robo\Sass\Task\SassCompileFilesTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskSassCompile(array $options = []): CollectionBuilder
    {
        return $this->task(Task\SassCompileFilesTask::class, $options);
    }
}
