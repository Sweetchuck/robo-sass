<?php

namespace Cheppers\Robo\Sass\Task;

use Robo\Result;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class SassCompileFilesTask extends BaseTask
{
    /**
     * {@inheritdoc}
     */
    protected $taskName = 'Sass::compile';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs = null;

    /**
     * @var \SassException
     */
    protected $sassException = null;

    // region Options.
    // region Option - style.
    /**
     * @var string
     */
    protected $style = 'expanded';

    public function getStyle(): string
    {
        return $this->style;
    }

    public function getStyleNumeric(): int
    {
        return $this->validStyles()[$this->style];
    }

    /**
     * @return $this
     */
    public function setStyle(string $value)
    {
        $styles = $this->validStyles();
        if (isset($styles[$value])) {
            $this->style = $value;
        } elseif (($style = array_search($value, $styles))) {
            $this->style = $style;
        } else {
            throw new \InvalidArgumentException('@todo');
        }

        return $this;
    }
    // endregion

    // region Option - includePath.
    /**
     * @var string
     */
    protected $includePath = '';

    public function getIncludePath(): string
    {
        return $this->includePath;
    }

    /**
     * @return $this
     */
    public function setIncludePath(string $value)
    {
        $this->includePath = $value;

        return $this;
    }
    // endregion

    // region Option - precision.
    /**
     * @var int
     */
    protected $precision = 5;

    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * @return $this
     */
    public function setPrecision(int $value)
    {
        $this->precision = $value;

        return $this;
    }
    // endregion

    // region Option - comments.
    /**
     * @var bool
     */
    protected $comments = true;

    public function getComments(): bool
    {
        return $this->comments;
    }

    /**
     * @return $this
     */
    public function setComments(bool $value)
    {
        $this->comments = $value;

        return $this;
    }
    // endregion

    // region Option - indent.
    /**
     * @var int
     */
    protected $indent = 2;

    public function getIndent(): int
    {
        return $this->indent;
    }

    /**
     * @return $this
     */
    public function setIndent(int $value)
    {
        $this->indent = $value;

        return $this;
    }
    // endregion

    // region Option - embed.
    /**
     * @var bool
     */
    protected $embed = false;

    public function getEmbed(): bool
    {
        return $this->embed;
    }

    /**
     * @return $this
     */
    public function setEmbed(bool $value)
    {
        $this->embed = $value;

        return $this;
    }
    // endregion

    // region Option - files.
    /**
     * @var \Symfony\Component\Finder\Finder|\Symfony\Component\Finder\SplFileInfo[]
     */
    protected $files = [];

    /**
     * @return \Symfony\Component\Finder\Finder|\Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param \Symfony\Component\Finder\Finder|\Symfony\Component\Finder\SplFileInfo[] $value
     *
     * @return $this
     */
    public function setFiles($value)
    {
        $this->files = $value;

        return $this;
    }
    // endregion

    // region Option - cssPath.
    /**
     * @var string
     */
    protected $cssPath = '';

    public function getCssPath(): string
    {
        return $this->cssPath;
    }

    /**
     * @return $this
     */
    public function setCssPath(string $value)
    {
        $this->cssPath = $value;

        return $this;
    }
    // endregion

    // region Option - mapPath.
    /**
     * @var string
     */
    protected $mapPath = '';

    public function getMapPath(): string
    {
        return $this->mapPath;
    }

    /**
     * @return $this
     */
    public function setMapPath(string $value)
    {
        $this->mapPath = $value;

        return $this;
    }
    // endregion
    // endregion

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            switch ($name) {
                case 'assetJar':
                    $this->setAssetJar($value);
                    break;

                case 'assetJarMapping':
                    $this->setAssetJarMapping($value);
                    break;

                case 'style':
                    $this->setStyle($value);
                    break;

                case 'includePath':
                    $this->setIncludePath($value);
                    break;

                case 'precision':
                    $this->setPrecision($value);
                    break;

                case 'comments':
                    $this->setComments($value);
                    break;

                case 'indent':
                    $this->setIndent($value);
                    break;

                case 'embed':
                    $this->setEmbed($value);
                    break;

                case 'files':
                    $this->setFiles($value);
                    break;

                case 'cssPath':
                    $this->setCssPath($value);
                    break;

                case 'mapPath':
                    $this->setMapPath($value);
                    break;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this
            ->runHeader()
            ->runDoIt()
            ->runReleaseAssets()
            ->runReturn();
    }

    /**
     * @return $this
     */
    protected function runHeader()
    {
        $this->printTaskInfo('Compile files');

        return $this;
    }

    /**
     * @return $this
     */
    protected function runDoIt()
    {
        $this->sassException = null;
        $this->assets = ['files' => []];
        $this->fs = new Filesystem();
        $this->sass = new $this->sassClass();

        $this->sass->setStyle($this->getStyleNumeric());
        $this->sass->setIncludePath($this->getIncludePath());
        $this->sass->setPrecision($this->getPrecision());
        $this->sass->setComments($this->getComments());
        $this->sass->setIndent($this->getIndent());
        $this->sass->setEmbed($this->getEmbed());

        $cssPath = $this->getCssPath();
        $mapPath = $this->getMapPath();

        foreach ($this->getFiles() as $file) {
            $relativePathnameSass = $file->getRelativePathname();
            $relativePathnameCss = $this->cssFileName($relativePathnameSass);
            if ($mapPath) {
                $this->sass->setMapPath("$mapPath/$relativePathnameSass");
            }

            try {
                $compiled = $this->sass->compileFile($file->getPathname());
            } catch (\SassException $e) {
                if (!$this->sassException) {
                    $this->sassException = $e;
                }

                // @todo Break or continue?
                continue;
            }

            if (!is_array($compiled)) {
                $compiled = [
                    'css' => $compiled,
                    'map' => '',
                ];
            } else {
                $compiled['css'] = $compiled[0];
                $compiled['map'] = $compiled[1];
                unset($compiled[0], $compiled[1]);
            }

            $this->assets['files'][$file->getPathname()] = $compiled;

            if ($cssPath) {
                $fileName = "$cssPath/$relativePathnameCss";
                $this->fs->mkdir(Path::getDirectory($fileName));
                file_put_contents($fileName, $compiled['css']);
            }

            if ($mapPath && $compiled['map']) {
                $fileName = "$mapPath/$relativePathnameCss.map";
                $this->fs->mkdir(Path::getDirectory($fileName));
                file_put_contents($fileName, $compiled['map']);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function runReleaseAssets()
    {
        if ($this->hasAssetJar()) {
            $assetJar = $this->getAssetJar();
            foreach ($this->assets as $name => $value) {
                $map = $this->getAssetJarMap($name);
                if ($map) {
                    $assetJar->setValue($map, $value);
                }
            }
        }

        return $this;
    }

    protected function runReturn(): Result
    {
        if ($this->sassException) {
            return Result::fromException($this, $this->sassException, $this->assets);
        }

        return Result::success($this, '', $this->assets);
    }

    protected function cssFileName(string $sassFileName): string
    {
        $extension = pathinfo($sassFileName, PATHINFO_EXTENSION);
        if ($extension === 'sass' || $extension === 'scss') {
            return substr($sassFileName, 0, -4) . 'css';
        }

        return "$sassFileName.css";
    }

    /**
     * @return int[]
     */
    public function validStyles(): array
    {
        return [
            'nested' => \Sass::STYLE_NESTED,
            'expanded' => \Sass::STYLE_EXPANDED,
            'compact' => \Sass::STYLE_COMPACT,
            'compressed' => \Sass::STYLE_COMPRESSED,
        ];
    }
}
