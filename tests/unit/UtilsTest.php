<?php

namespace Sweetchuck\Robo\Sass\Test;

use Sweetchuck\Robo\Sass\Utils;

class UtilsTest extends \Codeception\Test\Unit
{
    /**
     * @var \Sweetchuck\Robo\Sass\Test\UnitTester
     */
    protected $tester;

    public function casesIncludePathsFromGemPaths(): array
    {
        $gemSetDir = rtrim(codecept_data_dir(), DIRECTORY_SEPARATOR);
        return [
            'basic' => [
                [
                    "$gemSetDir/gem-01/sass",
                    "$gemSetDir/gem-02/stylesheets",
                ],
                [
                    "$gemSetDir/gem-01",
                    "$gemSetDir/gem-02",
                    "$gemSetDir/gem-03",
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesIncludePathsFromGemPaths
     */
    public function testIncludePathsFromGemPaths(array $expected, array $gemPaths): void
    {
        $this->tester->assertEquals($expected, Utils::includePathsFromGemPaths($gemPaths));
    }
}
