<?php
// ================================================
// index.php - Routeur principal ImpactVenture
// ================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/FicheEntrepriseController.php';
require_once __DIR__ . '/app/controllers/ProjetController.php';

$action = $_GET['action'] ?? 'fiche_list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

$ctrlFiche = new FicheEntrepriseController();
$ctrlProjet = new ProjetController();

switch ($action) {
    // === FICHE ENTREPRISE (1ère entité) ===
    case 'fiche_list':
        $ctrlFiche->index();
        break;
    case 'fiche_create':
        $ctrlFiche->create();
        break;
    case 'fiche_store':
        $ctrlFiche->store();
        break;
    case 'fiche_edit':
        $ctrlFiche->edit($id);
        break;
    case 'fiche_update':
        $ctrlFiche->update($id);
        break;
    case 'fiche_delete':
        $ctrlFiche->delete($id);
        break;

    // === PROJET (2ème entité) ===
    case 'projet_list':
        $ctrlProjet->index();
        break;
    case 'projet_create':
        $ctrlProjet->create();
        break;
    case 'projet_store':
        $ctrlProjet->store();
        break;

    // === BACK OFFICE ===
    case 'admin':
        $ctrlFiche->adminIndex();
        break;
    case 'admin_trending':
        $ctrlFiche->adminTrending();
        break;

    default:
        $ctrlFiche->index();
}