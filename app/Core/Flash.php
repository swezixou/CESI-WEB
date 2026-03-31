<?php
namespace App\Core;

/**
 * Flash – Messages de notification éphémères (stockés en session).
 */
class Flash
{
    /** Ajoute un message flash */
    public static function set(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    /** Récupère et efface les messages flash */
    public static function get(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }

    /** Raccourcis */
    public static function success(string $msg): void { self::set('success', $msg); }
    public static function error(string $msg): void   { self::set('error',   $msg); }
    public static function info(string $msg): void    { self::set('info',    $msg); }
    public static function warning(string $msg): void { self::set('warning', $msg); }
}
