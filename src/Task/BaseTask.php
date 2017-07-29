<?php

namespace Sweetchuck\Robo\Sass\Task;

use Sweetchuck\AssetJar\AssetJarAware;
use Sweetchuck\AssetJar\AssetJarAwareInterface;
use Robo\Task\BaseTask as RoboBaseTask;
use Robo\TaskInfo;

abstract class BaseTask extends RoboBaseTask implements AssetJarAwareInterface
{
    use AssetJarAware;

    /**
     * @var string
     */
    protected $sassClass = \Sass::class;

    /**
     * @var \Sass
     */
    protected $sass = null;

    /**
     * @var string
     */
    protected $taskName = 'Sass';

    /**
     * @var array
     */
    protected $assets = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @return $this
     */
    abstract public function setOptions(array $option);

    public function getTaskName(): string
    {
        return $this->taskName ?: TaskInfo::formatTaskName($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTaskContext($context = null)
    {
        if (!$context) {
            $context = [];
        }

        if (empty($context['name'])) {
            $context['name'] = $this->getTaskName();
        }

        return parent::getTaskContext($context);
    }
}
