<?php

namespace Cheppers\Robo\Sass\Composer;

use Cheppers\GitHooks\Main as GitHooksComposerScripts;
use Composer\Script\Event;

class Scripts
{
    public static function postInstallCmd(Event $event): bool
    {
        GitHooksComposerScripts::deploy($event);

        return true;
    }

    public static function postUpdateCmd(Event $event): bool
    {
        GitHooksComposerScripts::deploy($event);

        return true;
    }
}
