<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Sass\Tests\Unit\Task;

use Codeception\Attribute\DataProvider;
use Sweetchuck\Robo\Sass\Task\SassCompileFilesTask;
use Symfony\Component\Finder\Finder;

/**
 * @method SassCompileFilesTask createTask()
 */
class SassCompileFilesTaskTest extends TaskTestBase
{

    protected function createTaskInstance(): SassCompileFilesTask
    {
        return new SassCompileFilesTask();
    }

    public function testGetSetStyle(): void
    {
        $task = $this->createTask();
        $task->setStyle('nested');
        $this->tester->assertSame('nested', $task->getStyle());
        $this->tester->assertSame(\Sass::STYLE_NESTED, $task->getStyleNumeric());

        $task->setStyle(2);
        $this->tester->assertSame('compact', $task->getStyle());
        $this->tester->assertSame(\Sass::STYLE_COMPACT, $task->getStyleNumeric());
    }

    public function testSetStyleInvalidNumber(): void
    {
        $task = $this->createTask();
        $task->setStyle(0);
        $task->setStyle(1);
        $task->setStyle(2);
        $task->setStyle(3);

        $this->tester->expectThrowable(
            new \InvalidArgumentException('Invalid style identifier "42"', 1),
            function () use ($task) {
                $task->setStyle(42);
            },
        );
    }

    public function testSetStyleInvalidName(): void
    {
        $task = $this->createTask();
        $task->setStyle('nested');
        $task->setStyle('expanded');
        $task->setStyle('compact');
        $task->setStyle('compressed');

        $this->tester->expectThrowable(
            new \InvalidArgumentException('Invalid style identifier "foo"', 1),
            function () use ($task) {
                $task->setStyle('foo');
            },
        );
    }

    /**
     * @return array<string, mixed>
     */
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
     * @param array<string, mixed> $expected
     * @param array<string, mixed> $options
     */
    #[DataProvider('casesRunSuccess')]
    public function testRunSuccess(array $expected, array $options): void
    {
        $expected += [
            'exitCode' => 0,
            'data' => [],
        ];
        $task = $this->createTask();
        $result = $task
            ->setOptions($options)
            ->run();

        $this->tester->assertSame(
            $expected['exitCode'],
            $result->getExitCode(),
            'Exit code',
        );
        foreach ($expected['data'] as $name => $value) {
            $this->tester->assertSame(
                $value,
                $result[$name],
                "Asset in result[$name]",
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
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
     * @param array<string, mixed> $expected
     * @param array<string, mixed> $options
     */
    #[DataProvider('casesRunFail')]
    public function testRunFail(array $expected, array $options): void
    {
        $task = $this->createTask();
        $result = $task
            ->setOptions($options)
            ->run();

        $this->tester->assertSame(
            $expected['exitCode'],
            $result->getExitCode(),
        );
    }

    /**
     * @return array<string, mixed>
     */
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
        $task = $this->createTask();
        $task->setOptions($options);

        $this->tester->assertSame(['a' => true, 'b' => true], $task->getIncludePaths());

        $task->removeIncludePath('a');
        $this->tester->assertSame(['b' => true], $task->getIncludePaths());

        $task->addIncludePath('c');
        $this->tester->assertSame(['b' => true, 'c' => true], $task->getIncludePaths());
    }
}
