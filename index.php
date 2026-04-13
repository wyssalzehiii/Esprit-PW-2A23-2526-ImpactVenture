<?php
require_once __DIR__ . '/app/controllers/ThemeController.php';
require_once __DIR__ . '/app/controllers/InvestisseurController.php';

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($action) {
    // === Existing Theme routes ===
    case 'list':
    case 'create':
    case 'store':
    case 'edit':
    case 'update':
    case 'delete':
    case 'admin':
    case 'admin_create':
    case 'admin_store':
    case 'admin_edit':
    case 'admin_update':
    case 'admin_delete':
    case 'trending':
        $ctrl = new ThemeController();
        switch ($action) {
            case 'list':          $ctrl->index();          break;
            case 'create':        $ctrl->create();         break;
            case 'store':         $ctrl->store();          break;
            case 'edit':          $ctrl->edit($id);        break;
            case 'update':        $ctrl->update($id);      break;
            case 'delete':        $ctrl->delete($id);      break;
            case 'admin':         $ctrl->adminIndex();     break;
            case 'admin_create':  $ctrl->adminCreate();    break;
            case 'admin_store':   $ctrl->adminStore();     break;
            case 'admin_edit':    $ctrl->adminEdit($id);   break;
            case 'admin_update':  $ctrl->adminUpdate($id); break;
            case 'admin_delete':  $ctrl->adminDelete($id); break;
            case 'trending':      $ctrl->adminTrending();  break;
            default:              $ctrl->index();
        }
        break;

    // === YOUR NEW FINANCEMENT ROUTES ===
    case 'financement':
        $ctrl = new InvestisseurController();
        $ctrl->index();
        break;

    case 'financement_submit':
        $ctrl = new InvestisseurController();
        $ctrl->submit();
        break;

    default:
        $ctrl = new ThemeController();
        $ctrl->index();
}