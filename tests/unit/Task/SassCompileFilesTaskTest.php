<?php

namespace Sweetchuck\Robo\Sass\Tests\Unit\Task;

use InvalidArgumentException;
use Sass;
use Sweetchuck\Robo\Sass\Task\SassCompileFilesTask;
use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyOutput;
use Codeception\Util\Stub;
use Robo\Robo;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class SassCompileFilesTaskTest extends TaskTestBase
{

    /**
     * @var \Sweetchuck\Robo\Sass\Task\SassCompileFilesTask
     */
    protected $task;

    protected function initTask()
    {
        $this->task = $this->taskBuilder->taskSassCompile();
    }

    public function testGetSetStyle(): void
    {
        $this->task->setStyle('nested');
        $this->tester->assertEquals('nested', $this->task->getStyle());
        $this->tester->assertEquals(Sass::STYLE_NESTED, $this->task->getStyleNumeric());

        $this->task->setStyle(2);
        $this->tester->assertEquals('compact', $this->task->getStyle());
        $this->tester->assertEquals(Sass::STYLE_COMPACT, $this->task->getStyleNumeric());
    }

    public function testSetStyleInvalidNumber(): void
    {
        $this->task->setStyle(0);
        $this->task->setStyle(1);
        $this->task->setStyle(2);
        $this->task->setStyle(3);

        $this->tester->expectThrowable(
            new InvalidArgumentException('Invalid style identifier "42"', 1),
            function () {
                $this->task->setStyle(42);
            }
        );
    }

    public function testSetStyleInvalidName(): void
    {
        $this->task->setStyle('nested');
        $this->task->setStyle('expanded');
        $this->task->setStyle('compact');
        $this->task->setStyle('compressed');

        $this->tester->expectThrowable(
            new InvalidArgumentException('Invalid style identifier "foo"', 1),
            function () {
                $this->task->setStyle('foo');
            }
        );
    }

    public function casesRunSuccess(): array
    {
        $gemSetDir = rtrim(codecept_data_dir(), DIRECTORY_SEPARATOR);
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
            'includePaths' => [
                [
                    'data' => [
                        'files' => [
                            "$in/03.scss" => [
                                'css' => implode("\n", [
                                    '/* line 6, tests/_data/project-01/scss/03.scss */',
                                    '.foo {',
                                    '  width: 42px;',
                                    '  height: 84px;',
                                    '  display: none;',
                                    '}',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ],
                ],
                [
                    'gemPaths' => [
                        "$gemSetDir/gem-01",
                        "$gemSetDir/gem-02",
                        "$gemSetDir/gem-03",
                    ],
                    'files' => (new Finder())
                        ->in($in)
                        ->files()
                        ->name('03.scss'),
                    'includePaths' => [
                        "$in/../../lib-01",
                    ],
                ],
            ],
            'assetNamePrefix' => [
                [
                    'data' => [
                        'my_anp:files' => [
                            "$in/03.scss" => [
                                'css' => implode("\n", [
                                    '/* line 6, tests/_data/project-01/scss/03.scss */',
                                    '.foo {',
                                    '  width: 42px;',
                                    '  height: 84px;',
                                    '  display: none;',
                                    '}',
                                    '',
                                ]),
                                'map' => '',
                            ],
                        ],
                    ],
                ],
                [
                    'gemPaths' => [
                        "$gemSetDir/gem-01",
                        "$gemSetDir/gem-02",
                        "$gemSetDir/gem-03",
                    ],
                    'files' => (new Finder())
                        ->in($in)
                        ->files()
                        ->name('03.scss'),
                    'includePaths' => [
                        "$in/../../lib-01",
                    ],
                    'assetNamePrefix' => 'my_anp:',
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

        $result = $this
            ->task
            ->setOptions($options)
            ->run();

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
        $result = $this
            ->task
            ->setOptions($options)
            ->run();

        $this->tester->assertEquals(
            $expected['exitCode'],
            $result->getExitCode()
        );
    }

    public function casesCssFileName(): array
    {
        return [
            'scss' => ['a.css', 'a.scss'],
            'sass' => ['a.css', 'a.sass'],
            'txt' => ['a.txt.css', 'a.txt'],
        ];
    }

    public function testGetSetIncludePaths(): void
    {
        $options = ['includePaths' => ['a', 'b']];
        $this->task->setOptions($options);

        $this->tester->assertEquals(['a' => true, 'b' => true], $this->task->getIncludePaths());

        $this->task->removeIncludePath('a');
        $this->tester->assertEquals(['b' => true], $this->task->getIncludePaths());

        $this->task->addIncludePath('c');
        $this->tester->assertEquals(['b' => true, 'c' => true], $this->task->getIncludePaths());
    }
}
