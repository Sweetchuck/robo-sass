<?php

namespace Cheppers\Robo\Sass\Tests\Unit\Task;

use Cheppers\AssetJar\AssetJar;
use Cheppers\Robo\Sass\Task\SassCompileFilesTask;
use Cheppers\Robo\Sass\Test\Helper\Dummy\Output as DummyOutput;
use Codeception\Test\Unit;
use Robo\Robo;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class SassCompileFilesTaskTest extends Unit
{
    /**
     * @var \Cheppers\Robo\Sass\Test\UnitTester
     */
    protected $tester;

    public function casesRunSuccess(): array
    {
        $in = codecept_data_dir('project-01/scss');
        $files = (new Finder())
            ->in($in)
            ->files()
            ->name('01.scss');

        return [
            'base' => [
                [
                    'data' => [
                        'files' => [
                            "$in/01.scss" => [
                                'css' => implode("\n", [
                                    '/* My Comment. */',
                                    '/* line 5, tests/_data/project-01/scss/01.scss */',
                                    '.foo {',
                                    '  color: #ffffff;',
                                    '}',
                                    '',
                                    '/* line 8, tests/_data/project-01/scss/01.scss */',
                                    '.foo .bar {',
                                    '  font-size: 3.33333px;',
                                    '}',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ],
                ],
                [
                    'files' => clone $files,
                ],
            ],
            'style:nested:2' => [
                [
                    'data' => [
                        'files' => [
                            "$in/01.scss" => [
                                'css' => implode("\n", [
                                    '/* My Comment. */',
                                    '.foo {',
                                    '  color: #ffffff; }',
                                    '  .foo .bar {',
                                    '    font-size: 3.33333px; }',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ],
                ],
                [
                    'files' => clone $files,
                    'comments' => false,
                    'style' => 'nested',
                    'indent' => 2,
                ],
            ],
            'style:expanded' => [
                [
                    'data' => [
                        'files' => [
                            "$in/01.scss" => [
                                'css' => implode("\n", [
                                    '/* My Comment. */',
                                    '.foo {',
                                    '  color: #ffffff;',
                                    '}',
                                    '',
                                    '.foo .bar {',
                                    '  font-size: 3.33333px;',
                                    '}',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ],
                ],
                [
                    'files' => clone $files,
                    'comments' => false,
                    'style' => 'expanded',
                ],
            ],
            'style:compact' => [
                [
                    'files' => [
                        "$in/01.scss" => [
                            'css' => implode("\n", [
                                '/* My Comment. */',
                                '.foo { color: #ffffff; }',
                                '',
                                '.foo .bar { font-size: 3.33333px; }',
                                '',
                            ]),
                            'map' => '',
                        ],
                    ],
                ],
                [
                    'files' => clone $files,
                    'comments' => false,
                    'style' => 'compact',
                ],
            ],
            'style:compressed' => [
                [
                    'data' => array(
                        'files' => [
                            "$in/01.scss" => [
                                'css' => implode("\n", [
                                    '.foo{color:#fff}.foo .bar{font-size:3.33333px}',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ),
                ],
                [
                    'files' => clone $files,
                    'comments' => false,
                    'style' => 'compressed',
                ],
            ],
            'comments:false' => [
                [
                    'data' => array(
                        'files' => [
                            "$in/01.scss" => [
                                'css' => implode("\n", [
                                    '/* My Comment. */',
                                    '.foo {',
                                    '  color: #ffffff;',
                                    '}',
                                    '',
                                    '.foo .bar {',
                                    '  font-size: 3.33333px;',
                                    '}',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ),
                ],
                [
                    'files' => clone $files,
                    'comments' => false,
                ],
            ],
            'precision:2' => [
                [
                    'data' => array(
                        'files' => [
                            "$in/01.scss" => [
                                'css' => implode("\n", [
                                    '/* My Comment. */',
                                    '.foo {',
                                    '  color: #ffffff;',
                                    '}',
                                    '',
                                    '.foo .bar {',
                                    '  font-size: 3.33px;',
                                    '}',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ),
                ],
                [
                    'files' => clone $files,
                    'comments' => false,
                    'precision' => 2,
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesRunSuccess
     */
    public function testRunSuccess(array $expected, array $options)
    {
        $expected += [
            'exitCode' => 0,
            'data' => [],
        ];

        $options += [
            'assetJar' => new AssetJar(),
            'assetJarMapping' => ['files' => ['files']],
        ];

        $config = [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            'colors' => false,
        ];
        $stdOutput = new DummyOutput($config);
        $containerBackup = Robo::hasContainer() ? Robo::getContainer() : null;
        $container = Robo::createDefaultContainer(null, $stdOutput);
        $container->add('output', $stdOutput, false);
        Robo::setContainer($container);

        $task = new SassCompileFilesTask($options);
        $result = $task->run();

        $this->tester->assertEquals(
            $expected['exitCode'],
            $result->getExitCode(),
            'Exit code'
        );
        foreach ($expected['data'] as $name => $value) {
            $this->tester->assertEquals(
                $value,
                $result[$name],
                "Asset in result[$name]"
            );
        }

        if ($containerBackup) {
            Robo::setContainer($containerBackup);
        } else {
            Robo::unsetContainer();
        }
    }

    public function casesRunFail(): array
    {
        $in = codecept_data_dir('project-01/scss');
        $files = (new Finder())
            ->in($in)
            ->files()
            ->name('02.scss');

        return [
            'basic' => [
                [
                    'exitCode' => 1,
                ],
                [
                    'files' => clone $files,
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesRunFail
     */
    public function testRunFail(array $expected, array $options)
    {
        $config = [
            'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            'colors' => false,
        ];
        $stdOutput = new DummyOutput($config);
        $containerBackup = Robo::hasContainer() ? Robo::getContainer() : null;
        $container = Robo::createDefaultContainer(null, $stdOutput);
        $container->add('output', $stdOutput, false);
        Robo::setContainer($container);

        $options += [
            'assetJar' => new AssetJar(),
            'assetJarMapping' => ['files' => ['files']],
        ];
        $task = new SassCompileFilesTask($options);
        $result = $task->run();

        $this->tester->assertEquals($expected['exitCode'], $result->getExitCode());

        if ($containerBackup) {
            Robo::setContainer($containerBackup);
        } else {
            Robo::unsetContainer();
        }
    }
}