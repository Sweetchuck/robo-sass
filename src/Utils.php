<?php

namespace Sweetchuck\Robo\Sass;

class Utils
{
    public static $includePathSuggestions = [
        'stylesheets',
        'sass',
        'scss',
    ];

    /**
     * @param string[] $gemPaths
     *
     * @return string[]
     */
    public static function includePathsFromGemPaths(iterable $gemPaths): array
    {
        $includePaths = [];
        foreach ($gemPaths as $gemPath) {
            $path = static::includePathFromGemPath($gemPath);
            if ($path !== null) {
                $includePaths[] = $path;
            }
        }

        return $includePaths;
    }

    public static function includePathFromGemPath(string $gemPath): ?string
    {
        foreach (static::$includePathSuggestions as $suggestion) {
            if (is_dir("$gemPath/$suggestion")) {
                return "$gemPath/$suggestion";
            }
        }

        return null;
    }

    public static function replaceFileExtension(string $fileName, array $pairs): string
    {
        $patterns = [];
        $replacement = [];
        foreach ($pairs as $old => $new) {
            $patterns[] = '/\.' . preg_quote($old) . '$/';
            $replacement = ".$new";
        }

        return preg_replace($patterns, $replacement, $fileName);
    }
}
