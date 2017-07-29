<?php

namespace Sweetchuck\Robo\Sass\Task;

use Robo\Task\BaseTask as RoboBaseTask;
use Robo\TaskInfo;

abstract class BaseTask extends RoboBaseTask
{
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

    // region Option - assetNamePrefix.
    /**
     * @var string
     */
    protected $assetNamePrefix = '';

    public function getAssetNamePrefix(): string
    {
        return $this->assetNamePrefix;
    }

    /**
     * @return $this
     */
    public function setAssetNamePrefix(string $value)
    {
        $this->assetNamePrefix = $value;

        return $this;
    }
    // endregion

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
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            switch ($name) {
                case 'assetNamePrefix':
                    $this->setAssetNamePrefix($value);
                    break;
            }
        }

        return $this;
    }

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
