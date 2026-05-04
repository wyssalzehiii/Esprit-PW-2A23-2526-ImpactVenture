<?php
require_once __DIR__ . '/app/controllers/ThemeController.php';
require_once __DIR__ . '/app/controllers/InvestisseurController.php';

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($action) {
    // === Theme routes ===
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

    // === YOUR FINANCEMENT ROUTES ===
    case 'financement':
        $ctrl = new InvestisseurController();
        $ctrl->index();
        break;

    case 'financement_submit':
        $ctrl = new InvestisseurController();
        $ctrl->submit();
        break;

    // NEW: Investor Profile
    case 'investisseur_profile':
        $ctrl = new InvestisseurController();
        $ctrl->profile($id);
        break;
    case 'mes_demandes':
        $ctrl = new InvestisseurController();
        $ctrl->mesDemandes();
        break;
    case 'delete_demande':
        $ctrl = new InvestisseurController();
        $ctrl->deleteDemande();
        break;
    case 'admin_financement':
        $ctrl = new InvestisseurController();
        $ctrl->adminList();
        break;

    case 'admin_investisseur_create':
        $ctrl = new InvestisseurController();
        $ctrl->adminCreate();
        break;

    case 'admin_investisseur_store':
        $ctrl = new InvestisseurController();
        $ctrl->adminStore();
        break;

    case 'admin_delete_investisseur':
        $ctrl = new InvestisseurController();
        $ctrl->deleteInvestisseur();
        break;

    case 'admin_demandes':
        $ctrl = new InvestisseurController();
        $ctrl->adminDemandes();
        break;

    case 'update_demande_status':
        $ctrl = new InvestisseurController();
        $ctrl->updateDemandeStatus();
        break;
    case 'admin_add_investisseur':
        $ctrl = new InvestisseurController();
        $ctrl->adminCreate();
        break;

    case 'admin_store_investisseur':
        $ctrl = new InvestisseurController();
        $ctrl->adminStore();
        break;
    case 'admin_investisseur_edit':
        $ctrl = new InvestisseurController();
        $ctrl->adminEdit($id);
        break;

    case 'admin_investisseur_update':
        $ctrl = new InvestisseurController();
        $ctrl->adminUpdate($id);
        break;
    case 'edit_demande':
        $ctrl = new InvestisseurController();
        $ctrl->editDemande($id);
        break;

    case 'update_demande':
        $ctrl = new InvestisseurController();
        $ctrl->updateDemande($id);
        break;
    case 'espace_investisseur':
        $controller = new InvestisseurController();
        $controller->espaceInvestisseur();
        break;

    case 'investisseur_action':
        $controller = new InvestisseurController();
        $controller->investisseurAction();
        break;
    case 'fund_demande':
        $controller = new InvestisseurController();
        $controller->fundDemande();
        break;

    default:
        $ctrl = new ThemeController();
        $ctrl->index();
}