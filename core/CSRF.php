<?php
class CSRF {
    public static function generate(): string {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }
    public static function validate(?string $token): bool {
        return $token !== null && hash_equals(Session::get('csrf_token') ?? '', $token);
    }
    public static function getField(): string {
        return '<input type="hidden" name="csrf_token" value="' . self::generate() . '">';
    }
}
