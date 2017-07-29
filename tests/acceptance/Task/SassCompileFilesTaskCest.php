<?php

namespace Sweetchuck\Robo\Sass\Tests\Acceptance\Task;

use Sweetchuck\Robo\Sass\Test\AcceptanceTester;
use Symfony\Component\Filesystem\Filesystem;

class SassCompileFilesTaskCest
{
    /**
     * @var string[]
     */
    protected $tmpDirs = [];

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs = null;

    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    public function __destruct()
    {
        $this->fs->remove($this->tmpDirs);
    }

    public function runCompileFilesSuccess(AcceptanceTester $I)
    {
        $tmpDir = $this->createTmpDir();

        $id = 'compile:files:success';
        $I->runRoboTask(
            $id,
            \SassRoboFile::class,
            'compile:files',
            '01.scss',
            "--cssPath=$tmpDir",
            "--mapPath=$tmpDir"
        );
        $I->assertEquals(0, $I->getRoboTaskExitCode($id));
        $I->assertEquals('', $I->getRoboTaskStdOutput($id));
        $I->assertEquals(" [Sass::compile] Compile files\n", $I->getRoboTaskStdError($id));
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
            '/*# sourceMappingURL='
        ]));

        $map = json_decode(file_get_contents("$tmpDir/01.css.map"), true);
        $I->assertEquals(
            [
                'version',
                'file',
                'sources',
                'sourcesContent',
                'names',
                'mappings',
            ],
            array_keys($map)
        );
    }

    public function runCompileFilesFail(AcceptanceTester $I)
    {
        $tmpDir = $this->createTmpDir();

        $id = 'compile:files:fail';
        $I->runRoboTask(
            $id,
            \SassRoboFile::class,
            'compile:files',
            '02.scss',
            "--cssPath=$tmpDir",
            "--mapPath=$tmpDir"
        );
        $I->assertEquals(1, $I->getRoboTaskExitCode($id));
        $I->assertEquals('', $I->getRoboTaskStdOutput($id));
        $I->assertContains(" [Sass::compile] Compile files\n", $I->getRoboTaskStdError($id));
        $I->assertContains(
            'Error: Invalid CSS after "  color: #ffffff;": expected "}", was ""',
            $I->getRoboTaskStdError($id)
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
