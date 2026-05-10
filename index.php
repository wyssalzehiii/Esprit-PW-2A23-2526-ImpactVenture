<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/url.php';
require_once __DIR__ . '/app/controllers/FicheEntrepriseController.php';
require_once __DIR__ . '/app/controllers/ProjetController.php';
require_once __DIR__ . '/app/controllers/InvestisseurController.php';

$action = isset($_GET['action']) ? trim((string) $_GET['action']) : 'fiche_list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

$ctrlFiche  = new FicheEntrepriseController();
$ctrlProjet = new ProjetController();

switch ($action) {

    // ─── FRONT – FicheEntreprise ───────────────────────────────────
    case 'fiche_list':          $ctrlFiche->index();           break;
    case 'fiche_create':        $ctrlFiche->create();          break;
    case 'fiche_store':         $ctrlFiche->store();           break;
    case 'fiche_generate_logo': $ctrlFiche->generateLogoSuggestionsAjax(); break;
    case 'fiche_edit':          $ctrlFiche->edit($id);         break;
    case 'fiche_update':        $ctrlFiche->update($id);       break;
    case 'fiche_delete':        $ctrlFiche->delete($id);       break;

    // ─── FRONT – Projet ───────────────────────────────────────────
    case 'projet_list':         $ctrlProjet->index();          break;
    case 'projet_list_advanced': $ctrlProjet->listAdvanced();     break;
    case 'projet_create':       $ctrlProjet->create();         break;
    case 'projet_store':        $ctrlProjet->store();          break;
    case 'projet_edit':         $ctrlProjet->edit($id);        break;
    case 'projet_update':       $ctrlProjet->update($id);      break;
    case 'projet_delete':       $ctrlProjet->delete($id);      break;
    case 'projet_show':         $ctrlProjet->show($id);        break;
    case 'analyze_projet':      $ctrlProjet->analyzeAjax();    break;
    case 'co2_simulator':       $ctrlProjet->co2Simulator();   break;

    // ─── BACK – FicheEntreprise ───────────────────────────────────
    case 'admin':
    case 'admin_fiche':         $ctrlFiche->adminIndex();      break;

    // ─── BACK – Projet ────────────────────────────────────────────
    case 'admin_projet':        $ctrlProjet->adminIndex();     break;
    case 'admin_projet_edit':   $ctrlProjet->adminEdit($id);   break;
    case 'admin_projet_update': $ctrlProjet->adminUpdate($id); break;
    case 'admin_projet_delete': $ctrlProjet->adminDelete($id); break;
    case 'trending':            $ctrlProjet->trending();        break;

    case 'advanced_dashboard':
    case 'dashboard_ia':
        $ctrlProjet->advancedDashboard();
        break;

    case 'badges':
        $ctrlProjet->badgesPage(isset($_GET['id_fiche']) ? (int) $_GET['id_fiche'] : null);
        break;
    case 'map':
        $ctrlFiche->map();
        break;
    case 'matching':
        $ctrlProjet->matching();
        break;
    case 'business_plan':
        $ctrlProjet->businessPlan();
        break;

    case 'comprehensive_pdf':
        $id = isset($_GET['id_projet']) ? (int)$_GET['id_projet'] : null;
        $ctrlProjet->generateComprehensivePDF($id);
        break;
    case 'viability':
        $ctrlProjet->viability();
        break;
    case 'sdg':
        $ctrlProjet->sdg();
        break;
    case 'chatbot':
        $ctrlProjet->chatbot();
        break;
    case 'chatbot_api':
        $ctrlProjet->chatbotApi();
        break;
    case 'sentiment':
        $ctrlProjet->sentiment();
        break;
    case 'pitch_deck':
        $ctrlProjet->pitchDeck();
        break;

    case 'founder_dashboard':
        $projet_id = isset($_GET['projet_id']) ? (int)$_GET['projet_id'] : null;
        $ctrlProjet->founderDashboard($projet_id);
        break;

    case 'entreprise_show':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $ctrlFiche->entrepriseShow($id);
        break;

    case 'mentor_interest':
        $ctrlProjet->mentorInterest();
        break;

    case 'admin_projet_export':
        $format = $_GET['format'] ?? 'csv';
        $ctrlProjet->adminExport($format);
        break;

    // ─── FRONT – Financement (Investisseurs) ──────────────────────
    case 'financement':
        $ctrl = new InvestisseurController();
        $ctrl->index();
        break;
    case 'financement_submit':
        $ctrl = new InvestisseurController();
        $ctrl->submit();
        break;
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
    case 'edit_demande':
        $ctrl = new InvestisseurController();
        $ctrl->editDemande($id);
        break;
    case 'update_demande':
        $ctrl = new InvestisseurController();
        $ctrl->updateDemande($id);
        break;
    case 'espace_investisseur':
        $ctrl = new InvestisseurController();
        $ctrl->espaceInvestisseur();
        break;
    case 'investisseur_action':
        $ctrl = new InvestisseurController();
        $ctrl->investisseurAction();
        break;
    case 'fund_demande':
        $ctrl = new InvestisseurController();
        $ctrl->fundDemande();
        break;

    // ─── BACK – Financement (Admin) ───────────────────────────────
    case 'admin_financement':
        $ctrl = new InvestisseurController();
        $ctrl->adminList();
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
    case 'admin_investisseur_create':
        $ctrl = new InvestisseurController();
        $ctrl->adminCreate();
        break;
    case 'admin_store_investisseur':
    case 'admin_investisseur_store':
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
    case 'admin_delete_investisseur':
        $ctrl = new InvestisseurController();
        $ctrl->deleteInvestisseur();
        break;

    default:
        $ctrlFiche->index();
}
?>
