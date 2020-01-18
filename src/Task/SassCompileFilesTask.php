<?php

namespace Sweetchuck\Robo\Sass\Task;

use InvalidArgumentException;
use Sass;
use SassException;
use Sweetchuck\Robo\Sass\Utils;
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
    // region Option - gemPaths.
    /**
     * @var array
     */
    protected $gemPaths = [];

    public function getGemPaths(): array
    {
        return $this->gemPaths;
    }

    /**
     * @return $this
     */
    public function setGemPaths(array $value)
    {
        $this->gemPaths = $value;

        return $this;
    }
    // endregion

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
        if (is_numeric($value)) {
            $value = (int) $value;
        }

        $styles = $this->validStyles();
        if (isset($styles[$value])) {
            $this->style = $value;
        } elseif ($style = array_search($value, $styles, true)) {
            $this->style = $style;
        } else {
            throw new InvalidArgumentException(sprintf('Invalid style identifier "%s"', $value), 1);
        }

        return $this;
    }
    // endregion

    // region Option - includePaths.
    /**
     * @var array
     */
    protected $includePaths = [];

    public function getIncludePaths(): array
    {
        return $this->includePaths;
    }

    /**
     * @return $this
     */
    public function setIncludePaths(array $paths)
    {
        if (gettype(reset($paths)) === 'boolean') {
            $this->includePaths = $paths;
        } else {
            $this->includePaths = array_fill_keys($paths, true);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addIncludePath(string $path)
    {
        $this->includePaths[$path] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function removeIncludePath(string $path)
    {
        unset($this->includePaths[$path]);

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
        parent::setOptions($options);

        if (array_key_exists('gemPaths', $options)) {
            $this->setGemPaths($options['gemPaths']);
        }

        if (array_key_exists('style', $options)) {
            $this->setStyle($options['style']);
        }

        if (array_key_exists('includePaths', $options)) {
            $this->setIncludePaths($options['includePaths']);
        }

        if (array_key_exists('precision', $options)) {
            $this->setPrecision($options['precision']);
        }

        if (array_key_exists('comments', $options)) {
            $this->setComments($options['comments']);
        }

        if (array_key_exists('indent', $options)) {
            $this->setIndent($options['indent']);
        }

        if (array_key_exists('embed', $options)) {
            $this->setEmbed($options['embed']);
        }

        if (array_key_exists('files', $options)) {
            $this->setFiles($options['files']);
        }

        if (array_key_exists('cssPath', $options)) {
            $this->setCssPath($options['cssPath']);
        }

        if (array_key_exists('mapPath', $options)) {
            $this->setMapPath($options['mapPath']);
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
        $this->sass->setPrecision($this->getPrecision());
        $this->sass->setComments($this->getComments());
        $this->sass->setIndent($this->getIndent());
        $this->sass->setEmbed($this->getEmbed());

        $includePaths = $this->getIncludePaths();
        $gemPaths = $this->getGemPaths();
        if (is_iterable($gemPaths)) {
            $includePaths += array_fill_keys(Utils::includePathsFromGemPaths($gemPaths), true);
        }

        $includePaths = array_keys($includePaths, true, true);
        $this->sass->setIncludePath(implode(PATH_SEPARATOR, $includePaths));

        $cssPath = $this->getCssPath();
        $mapPath = $this->getMapPath();

        $extensionPairs = [
            'scss' => 'css',
            'sass' => 'css',
        ];
        foreach ($this->getFiles() as $file) {
            $relativePathnameSass = $file->getRelativePathname();
            $relativePathnameCss = Utils::replaceFileExtension($relativePathnameSass, $extensionPairs);
            if ($relativePathnameSass === $relativePathnameCss) {
                $relativePathnameCss = '.css';
            }

            if ($mapPath) {
                $this->sass->setMapPath("$mapPath/$relativePathnameSass");
            }

            try {
                $compiled = $this->sass->compileFile($file->getPathname());
            } catch (SassException $e) {
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

            $cssFileName = $cssPath ? "$cssPath/$relativePathnameCss" : '';
            $mapFileName = $mapPath && $compiled['map'] ? "$mapPath/$relativePathnameCss.map" : '';

            if ($cssFileName && $mapFileName) {
                $compiled['css'] .= sprintf(
                    "\n/*# sourceMappingURL=%s */\n",
                    Path::makeRelative($mapFileName, Path::getDirectory($cssFileName))
                );
            }

            if ($cssFileName) {
                $this->fs->mkdir(Path::getDirectory($cssFileName));
                $this->fs->dumpFile($cssFileName, $compiled['css']);
            }

            if ($mapFileName) {
                $this->fs->mkdir(Path::getDirectory($mapFileName));
                $this->fs->dumpFile($mapFileName, $compiled['map']);
            }
        }

        return $this;
    }

    protected function runReturn(): Result
    {
        $assetNamePrefix = $this->getAssetNamePrefix();
        if ($assetNamePrefix === '') {
            $data = $this->assets;
        } else {
            $data = [];
            foreach ($this->assets as $key => $value) {
                $data["{$assetNamePrefix}{$key}"] = $value;
            }
        }

        if ($this->sassException) {
            return Result::fromException($this, $this->sassException, $data);
        }

        return Result::success($this, '', $data);
    }

    /**
     * @return int[]
     */
    public function validStyles(): array
    {
        return [
            'nested' => Sass::STYLE_NESTED,
            'expanded' => Sass::STYLE_EXPANDED,
            'compact' => Sass::STYLE_COMPACT,
            'compressed' => Sass::STYLE_COMPRESSED,
        ];
    }
}
