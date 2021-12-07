<?php

namespace Obsidian\Core;

use Obsidian\Application;

class Error {

    public static function show($error) {
        if (Configuration::get('logging','level') > 1) {
            $trace = debug_backtrace();
            $version = OBSIDIAN_VERSION;
            echo <<<EOL
            <div style="background: orangered; color: white; padding: 1rem;">
            <p><strong>Obsidian Error</strong></p>
            <p><pre style="background: #ffeeef; color: black; padding: 1rem;">$error<hr>
            {$trace[0]['file']}:{$trace[0]['line']}<br>
            {$trace[1]['file']}:{$trace[1]['line']}<br>
            {$trace[2]['file']}:{$trace[2]['line']}
            </pre></p>
            <small>Obsidian {$version}</small>
            </div>
            EOL;
            die();
        }
    }

}