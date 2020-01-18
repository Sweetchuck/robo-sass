<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Sass\Tests\Unit\Task;

use Codeception\Test\Unit;
use League\Container\Container as LeagueContainer;
use Robo\Collection\CollectionBuilder;
use Robo\Config\Config;
use Robo\Robo;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;
use Sweetchuck\Robo\Sass\Test\Helper\Dummy\DummyTaskBuilder;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\ErrorHandler\BufferingLogger;

abstract class TaskTestBase extends Unit
{
    /**
     * @var \League\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Robo\Config\Config
     */
    protected $config;

    /**
     * @var \Robo\Collection\CollectionBuilder
     */
    protected $builder;

    /**
     * @var \Sweetchuck\Robo\Sass\Test\UnitTester
     */
    protected $tester;

    /**
     * @var \Sweetchuck\Robo\Sass\Task\BaseTask
     */
    protected $task;

    /**
     * @var \Sweetchuck\Robo\Sass\Test\Helper\Dummy\DummyTaskBuilder
     */
    protected $taskBuilder;

    /**
     * @inheritdoc
     */
    public function _before()
    {
        parent::_before();

        Robo::unsetContainer();

        $this->container = new LeagueContainer();
        $application = new SymfonyApplication('Sweetchuck - Robo Sass', '1.0.0');
        $this->config = new Config();
        $input = null;
        $output = new DummyOutput([
            'verbosity' => DummyOutput::VERBOSITY_DEBUG,
        ]);

        $this->container->add('container', $this->container);

        Robo::configureContainer($this->container, $application, $this->config, $input, $output);
        $this->container->share('logger', BufferingLogger::class);

        $this->builder = CollectionBuilder::create($this->container, null);
        $this->taskBuilder = new DummyTaskBuilder();
        $this->taskBuilder->setContainer($this->container);
        $this->taskBuilder->setBuilder($this->builder);

        $this->initTask();
    }

    /**
     * @return $this
     */
    abstract protected function initTask();
}
