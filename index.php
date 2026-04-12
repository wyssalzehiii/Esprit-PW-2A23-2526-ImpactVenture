<?php
require_once __DIR__ . '/app/controllers/ThemeController.php';
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$ctrl   = new ThemeController();

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
