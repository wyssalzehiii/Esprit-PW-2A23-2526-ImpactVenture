<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/FicheEntrepriseController.php';
require_once __DIR__ . '/app/controllers/ProjetController.php';

$action = $_GET['action'] ?? 'fiche_list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

$ctrlFiche = new FicheEntrepriseController();
$ctrlProjet = new ProjetController();

switch ($action) {
    // Front Office
    case 'fiche_list':     $ctrlFiche->index(); break;
    case 'fiche_create':   $ctrlFiche->create(); break;
    case 'fiche_store':    $ctrlFiche->store(); break;
    case 'projet_list':    $ctrlProjet->index(); break;
    case 'projet_create':  $ctrlProjet->create(); break;
    case 'projet_store':   $ctrlProjet->store(); break;

    // Back Office
    case 'admin':
    case 'admin_fiche':    $ctrlFiche->adminIndex(); break;
    case 'admin_projet':   $ctrlProjet->adminIndex(); break;

    default:
        $ctrlFiche->index();
}