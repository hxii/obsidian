<?php

namespace Obsidian\Core;

use Obsidian\Application;

class Auth {

    public static function generateSignature($datesalt = false): string {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $salt = ($datesalt) ? date('HdmY', microtime(1)) : '';
        $signature = sha1( $userAgent . $ip . (Configuration::get('security','pepper') ?? '') . $salt );
        return $signature;
    }

    public static function hashPassword(string $password, bool $hash = true) {
        if (strlen($password) < Configuration::get('security','min_pass_length')) return false;
        $peppered = hash_hmac('sha256', $password, Configuration::get('security','pepper'));
        return ($hash) ? password_hash($peppered, Configuration::get('security','algo')) : $peppered;
    }

    public static function verifyPassword(string $password, string $hash): bool {
        $hashedPassword = self::hashPassword($password, false);
        return password_verify($hashedPassword, $hash);
    }

    public static function isLoggedIn(string $key = 'signature') {
        if ($signature = Application::session()->$key) {
            $check = $this->generateSignature();
            if ($signature === $check) {
                return true;
            }
        }
        return false;
    }

    public static function createNonce() {
        $_SESSION['nonce'] = self::generateSignature(1);
        return $_SESSION['nonce'];
    }

    public static function verifyNonce() {
        if (isset($_SESSION['nonce']) && $check = $_SESSION['nonce']) {
            unset($_SESSION['nonce']);
            $nonce = self::generateSignature(1);
            return $nonce === $check;
        }
        return false;
    }

}