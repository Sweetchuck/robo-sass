<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Sass;

use Robo\Collection\CollectionBuilder;

trait SassTaskLoader
{
    /**
     * @param array<string, mixed> $options
     * @phpstan-param robo-sass-task-compile-options $options
     *
     * @return \Sweetchuck\Robo\Sass\Task\SassCompileFilesTask|\Robo\Collection\CollectionBuilder
     */
    protected function taskSassCompile(array $options = []): CollectionBuilder
    {
        return $this->task(Task\SassCompileFilesTask::class, $options);
    }
}
