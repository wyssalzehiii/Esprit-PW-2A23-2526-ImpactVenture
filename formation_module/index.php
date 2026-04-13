<?php

// Front-Controller (Simple Router)
require_once __DIR__ . '/controllers/FormationController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list_formations_front';
$controller = new FormationController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($action) {
    // =============================
    // FRONT OFFICE ROUTES
    // =============================
    case 'list_formations_front':
        $controller->indexFront();
        break;
    case 'detail_formation_front':
        $controller->detailFront($id);
        break;

    // =============================
    // BACK OFFICE ROUTES
    // =============================
    case 'list_formations':
        $controller->index();
        break;
    case 'create_formation':
        $controller->create();
        break;
    case 'store_formation':
        $controller->store();
        break;
    case 'edit_formation':
        $controller->edit($id);
        break;
    case 'update_formation':
        $controller->update($id);
        break;
    case 'delete_formation':
        $controller->delete($id);
        break;
    
    // =============================
    // FALLBACK
    // =============================
    default:
        $controller->indexFront();
        break;
}
