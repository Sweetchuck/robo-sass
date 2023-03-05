<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Sass\Tests\Unit;

use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use Sweetchuck\Robo\Sass\Tests\UnitTester;
use Sweetchuck\Robo\Sass\Utils;

class UtilsTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @return array<string, mixed>
     */
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
     * @param array<mixed> $expected
     * @param array<string> $gemPaths
     */
    #[DataProvider('casesIncludePathsFromGemPaths')]
    public function testIncludePathsFromGemPaths(array $expected, array $gemPaths): void
    {
        $this->tester->assertEquals($expected, Utils::includePathsFromGemPaths($gemPaths));
    }

    /**
     * @return array<string, mixed>
     */
    public function casesReplaceFileExtension(): array
    {
        return [
            'empty 1' => ['', '', []],
            'empty 2' => ['', '', ['sass' => 'css']],
            'empty 3' => ['a.scss', 'a.scss', []],
            'simple' => ['a.css', 'a.scss', ['scss' => 'css']],
            'multiple 1' => ['a.css', 'a.scss', ['sass' => 'css', 'scss' => 'css']],
            'multiple 2' => ['a.foo', 'a.scss', ['sass' => 'css', 'scss' => 'foo']],
        ];
    }

    /**
     * @param string $expected
     * @param string $fileName
     * @param array<string, string> $pairs
     */
    #[DataProvider('casesReplaceFileExtension')]
    public function testReplaceFileExtension(string $expected, string $fileName, array $pairs): void
    {
        $this->tester->assertSame($expected, Utils::replaceFileExtension($fileName, $pairs));
    }
}
