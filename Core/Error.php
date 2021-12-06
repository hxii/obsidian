<?php

namespace Obsidian\Core;

class Error {

    public static function show($error) {
        if (Configuration::get('logging','level') > 1) {
            $trace = debug_backtrace();
            echo <<<EOL
            <div style="background: orangered; color: white; padding: 1rem;">
            <p><strong>Obsidian Error</strong></p>
            <p><pre style="background: #ffeeef; color: black; padding: 1rem;">$error<hr>
            {$trace[1]['file']}:{$trace[1]['line']}<br>
            {$trace[2]['file']}:{$trace[2]['line']}
            </pre></p>
            </div>
            EOL;
            die();
        }
    }

}