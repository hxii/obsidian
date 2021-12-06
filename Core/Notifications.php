<?php

namespace Obsidian\Core;

use Obsidian\Application;

class Notifications {

    public static function create(string $level, string $message) {
        if (Application::session()->id()) {
            Application::session()->notifications[] = [
                'level' => $level,
                'message' => $message,
            ];
        }
    }

    public static function show() {
        $html = '';
        if (Application::session()->id() && isset(Application::session()->notifications)) {
            foreach (Application::session()->notifications as $k=>$notification) {
                $html .= <<<EOL
                <div class="notification {$notification['level']}">
                {$notification['message']}
                </div>
                EOL;
                unset(Application::session()->notifications[$k]);
            }
        }
        return $html;
    }

}