<?php
namespace App\Core;

/**
 * Moteur de template PHP – gère les layouts et les includes.
 * Conforme à STx 7 (moteur de template côté Backend).
 */
class View
{
    private array  $data     = [];
    private string $content  = '';

    /**
     * Rend un template dans un layout.
     *
     * @param string $template   Ex: 'offers/index'
     * @param array  $data       Variables injectées dans la vue
     * @param string $layout     Nom du layout (app/Views/layouts/{layout}.php)
     */
    public function render(string $template, array $data = [], string $layout = 'main'): void
    {
        $this->data = $data;

        // 1. Capture le rendu du template enfant
        $templatePath = VIEW_PATH . '/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Vue introuvable : $templatePath");
        }

        ob_start();
        extract($this->data, EXTR_SKIP);
        include $templatePath;
        $this->content = ob_get_clean();

        // 2. Injecte dans le layout
        $layoutPath = VIEW_PATH . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout introuvable : $layoutPath");
        }

        extract($this->data, EXTR_SKIP);
        $content = $this->content; // disponible dans le layout via $content
        include $layoutPath;
    }

    /**
     * Inclut un fragment de vue partielle (partial).
     * Usage dans une vue : <?= $this->partial('partials/card', ['offer' => $offer]) ?>
     */
    public static function partial(string $template, array $data = []): string
    {
        $path = VIEW_PATH . '/' . $template . '.php';
        if (!file_exists($path)) return '';
        ob_start();
        extract($data, EXTR_SKIP);
        include $path;
        return ob_get_clean();
    }

    /** Échappe les caractères HTML */
    public static function e(mixed $val): string
    {
        return htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
