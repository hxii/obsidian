<?php

namespace Obsidian\Core;

use Obsidian\Application;

class Notifications {

    public static function create(string $level, string $message) {
        // if (Application::session()->id()) {
        //     Application::session()->notifications[] = [
        //         'level' => $level,
        //         'message' => $message,
        //     ];
        // }
        if (Session::id()) {
            array_push(
                Session::get('notifications'),
                ['level' => $level, 'message' => $message]
            );
        }
    }

    public static function show() {
        $html = '';
        if (Session::id() && Session::get('notifications')) {
            foreach (Session::get('notifications') as $k=>$notification) {
                $html .= <<<EOL
                <div class="notification {$notification['level']}">
                {$notification['message']}
                </div>
                EOL;
                unset(Session::get('notifications')[$k]);
            }
        }
        return $html;
    }

}