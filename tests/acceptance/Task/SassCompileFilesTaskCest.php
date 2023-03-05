<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Sass\Tests\Acceptance\Task;

use Sweetchuck\Robo\Sass\Tests\AcceptanceTester;
use Sweetchuck\Robo\Sass\Tests\Helper\RoboFiles\SassRoboFile;
use Symfony\Component\Filesystem\Filesystem;

class SassCompileFilesTaskCest
{
    /**
     * @var array<string>
     */
    protected array $tmpDirs = [];

    protected Filesystem $fs;

    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    public function __destruct()
    {
        $this->fs->remove($this->tmpDirs);
    }

    public function runCompileFilesSuccess(AcceptanceTester $I): void
    {
        $tmpDir = $this->createTmpDir();

        $id = 'compile:files:success';
        $I->runRoboTask(
            $id,
            SassRoboFile::class,
            'compile:files',
            codecept_data_dir('project-01/scss'),
            '01.scss',
            "--cssPath=$tmpDir",
            "--mapPath=$tmpDir",
        );
        $I->assertSame(0, $I->getRoboTaskExitCode($id));
        $I->assertSame('', $I->getRoboTaskStdOutput($id));
        $I->assertSame(" [Sass::compile] Compile files\n", $I->getRoboTaskStdError($id));
        $I->openFile("$tmpDir/01.css");
        $I->seeInThisFile(implode("\n", [
            '/* My Comment. */',
            '.foo {',
            '  color: #ffffff;',
            '}',
            '',
            '.foo .bar {',
            '  font-size: 3.3333px;',
            '}',
            '',
            '/*# sourceMappingURL=',
        ]));

        $map = json_decode(
            (string) file_get_contents("$tmpDir/01.css.map"),
            true,
        );
        $I->assertSame(
            [
                'version',
                'file',
                'sources',
                'names',
                'mappings',
            ],
            array_keys($map),
        );
    }

    public function runCompileFilesFail(AcceptanceTester $I): void
    {
        $tmpDir = $this->createTmpDir();

        $id = 'compile:files:fail';
        $I->runRoboTask(
            $id,
            SassRoboFile::class,
            'compile:files',
            codecept_data_dir('project-01/scss'),
            '02.scss',
            "--cssPath=$tmpDir",
            "--mapPath=$tmpDir",
        );
        $I->assertSame(1, $I->getRoboTaskExitCode($id));
        $I->assertSame('', $I->getRoboTaskStdOutput($id));
        $I->assertStringContainsString(" [Sass::compile] Compile files\n", $I->getRoboTaskStdError($id));
        $I->assertStringContainsString(" [Sass::compile] Compile files\n", $I->getRoboTaskStdError($id));
        $I->assertStringContainsString(
            'Error: Invalid CSS after "  color: #ffffff;": expected "}", was ""',
            $I->getRoboTaskStdError($id),
        );
    }

    protected function createTmpDir(): string
    {
        $name = $this->fs->tempnam(sys_get_temp_dir(), 'robo-sass-test-');
        $this->fs->remove($name);
        $this->fs->mkdir($name, 0777 - umask());
        $this->tmpDirs[] = $name;

        return $name;
    }
}
