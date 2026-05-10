<?php
/**
 * Lien vers index.php (contrôleur frontal) — chemin absolu depuis la racine du site.
 * Évite les href relatifs qui pointent vers le mauvais dossier.
 */
function iv_url(array $params = []): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $script = str_replace('\\', '/', $script);
    if ($params === []) {
        return $script;
    }
    return $script . '?' . http_build_query($params);
}
