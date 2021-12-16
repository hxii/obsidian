<?php

namespace Obsidian\Core;

use Obsidian\Application;

class Notifications {

    public static function create(string $level, string $message) {
        if (Session::id()) {
            $notifications = Session::get('notifications') ?: Session::set('notifications', []);
            array_push(
                $notifications,
                ['level' => $level, 'message' => $message]
            );
            Session::set('notifications', $notifications);
        }
    }

    public static function show() {
        $html = '';
        if (Session::id() && $notifications = Session::get('notifications')) {
            foreach ($notifications as $k=>$notification) {
                $html .= <<<EOL
                <div class="notification {$notification['level']}">
                {$notification['message']}
                </div>
                EOL;
                unset($notifications[$k]);
            }
            Session::set('notifications');
        }
        return $html;
    }

}