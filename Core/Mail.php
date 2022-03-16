<?php

namespace Obsidian\Core;

class Mail {

  public static function send(string $toAddress, string $subject, string $body) {
    $headers = array(
        'From'         => 'noreply@diary.by',
        'Reply-To'     => 'noreply@diary.by',
        'X-Mailer'     => 'PHP/' . phpversion(),
        'Content-Type' => 'text/html; charset="UTF-8"',
    );
    return mail($toAddress, $subject, $body, $headers);
}

public static function renderTemplate(string $template, array $data) {
    ob_start();
    Template::view($template, $data);
    $output = ob_get_clean();
    return $output;
}

}