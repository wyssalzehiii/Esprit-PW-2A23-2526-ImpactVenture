<?php

// Front-Controller (Simple Router)
require_once __DIR__ . '/controllers/backoffice/FormationController.php';
require_once __DIR__ . '/controllers/frontoffice/FormationController.php';
require_once __DIR__ . '/controllers/backoffice/BadgeController.php';
require_once __DIR__ . '/controllers/frontoffice/BadgeController.php';
require_once __DIR__ . '/controllers/frontoffice/ProjectController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list_formations_front';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$formationControllerBO = new \controllers\backoffice\FormationController();
$formationControllerFO = new \controllers\frontoffice\FormationController();
$badgeControllerBO = new \controllers\backoffice\BadgeController();
$badgeControllerFO = new \controllers\frontoffice\BadgeController();
$projectControllerFO = new \controllers\frontoffice\ProjectController();

switch ($action) {
    // =============================
    // FRONT OFFICE ROUTES
    // =============================
    case 'list_formations_front':
        $formationControllerFO->indexFront();
        break;
    case 'detail_formation_front':
        $formationControllerFO->detailFront($id);
        break;
    case 'recommended_formations':
        $formationControllerFO->indexRecommended(1); // hardcoded user
        break;
    case 'complete_formation':
        $formationControllerFO->complete($id);
        break;
    case 'my_badges':
        $badgeControllerFO->myBadges(1); // hardcoded user
        break;
    case 'list_badges_front':
        $badgeControllerFO->indexFront();
        break;
    case 'detail_badge_front':
        $badgeControllerFO->detailFront($id);
        break;
    case 'list_projects_front':
        $projectControllerFO->indexFront();
        break;
    case 'create_project_front':
        $projectControllerFO->create();
        break;
    case 'store_project_front':
        $projectControllerFO->store();
        break;
    case 'edit_project_front':
        $projectControllerFO->edit($id);
        break;
    case 'update_project_front':
        $projectControllerFO->update($id);
        break;
    case 'delete_project_front':
        $projectControllerFO->delete($id);
        break;
    case 'toggle_project_link':
        $projectControllerFO->toggleLink($id);
        break;

    // =============================
    // BACK OFFICE ROUTES
    // =============================
    case 'list_formations':
        $formationControllerBO->index();
        break;
    case 'create_formation':
        $formationControllerBO->create();
        break;
    case 'store_formation':
        $formationControllerBO->store();
        break;
    case 'edit_formation':
        $formationControllerBO->edit($id);
        break;
    case 'update_formation':
        $formationControllerBO->update($id);
        break;
    case 'delete_formation':
        $formationControllerBO->delete($id);
        break;
    case 'list_badges':
        $badgeControllerBO->index();
        break;
    case 'create_badge':
        $badgeControllerBO->create();
        break;
    case 'store_badge':
        $badgeControllerBO->store();
        break;
    case 'edit_badge':
        $badgeControllerBO->edit($id);
        break;
    case 'update_badge':
        $badgeControllerBO->update($id);
        break;
    case 'delete_badge':
        $badgeControllerBO->delete($id);
        break;
    
    // =============================
    // FALLBACK
    // =============================
    default:
        $formationControllerFO->indexFront();
        break;
}
