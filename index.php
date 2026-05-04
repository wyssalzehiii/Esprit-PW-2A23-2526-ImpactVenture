<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/controllers/FicheEntrepriseController.php';
require_once __DIR__ . '/app/controllers/ProjetController.php';
require_once __DIR__ . '/app/controllers/AdvancedController.php';

$action = $_GET['action'] ?? 'fiche_list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

$ctrlFiche    = new FicheEntrepriseController();
$ctrlProjet   = new ProjetController();
$ctrlAdvanced = new AdvancedController();

switch ($action) {

    // ─── FRONT – FicheEntreprise ───────────────────────────
    case 'fiche_list':           $ctrlFiche->index();              break;
    case 'fiche_create':         $ctrlFiche->create();             break;
    case 'fiche_store':          $ctrlFiche->store();              break;
    case 'fiche_edit':           $ctrlFiche->edit($id);            break;
    case 'fiche_update':         $ctrlFiche->update($id);          break;
    case 'fiche_delete':         $ctrlFiche->delete($id);          break;

    // ─── FRONT – Projet ───────────────────────────────────
    case 'projet_list':          $ctrlProjet->index();             break;
    case 'projet_create':        $ctrlProjet->create();            break;
    case 'projet_store':         $ctrlProjet->store();             break;
    case 'projet_edit':          $ctrlProjet->edit($id);           break;
    case 'projet_update':        $ctrlProjet->update($id);         break;
    case 'projet_delete':        $ctrlProjet->delete($id);         break;
    case 'projet_show':          $ctrlProjet->show($id);           break;
    case 'analyze_projet':       $ctrlProjet->analyzeAjax();       break;
    case 'co2_simulator':        $ctrlProjet->co2Simulator();      break;

    // ─── BACK – FicheEntreprise ───────────────────────────
    case 'admin':
    case 'admin_fiche':          $ctrlFiche->adminIndex();         break;
    case 'admin_fiche_edit':     $ctrlFiche->adminEdit($id);       break;
    case 'admin_fiche_update':   $ctrlFiche->adminUpdate($id);     break;
    case 'admin_fiche_delete':   $ctrlFiche->adminDelete($id);     break;

    // ─── BACK – Projet ────────────────────────────────────
    case 'admin_projet':         $ctrlProjet->adminIndex();        break;
    case 'admin_projet_edit':    $ctrlProjet->adminEdit($id);      break;
    case 'admin_projet_update':  $ctrlProjet->adminUpdate($id);    break;
    case 'admin_projet_delete':  $ctrlProjet->adminDelete($id);    break;
    case 'trending':             $ctrlProjet->trending();           break;

    // ─── ADVANCED – 10 fonctionnalités avancées ───────────
    case 'matching':             $ctrlAdvanced->matching();         break;
    case 'business_plan':        $ctrlAdvanced->businessPlan();     break;
    case 'viability':            $ctrlAdvanced->viability();        break;
    case 'sdg':                  $ctrlAdvanced->sdg();              break;
    case 'map':                  $ctrlAdvanced->map();              break;
    case 'chatbot':              $ctrlAdvanced->chatbot();          break;
    case 'chatbot_api':          $ctrlAdvanced->chatbotApi();       break;
    case 'sentiment':            $ctrlAdvanced->sentiment();        break;
    case 'badges':               $ctrlAdvanced->badges();           break;
    case 'pitch_deck':           $ctrlAdvanced->pitchDeck();        break;
    case 'advanced_dashboard':   $ctrlAdvanced->dashboard();        break;

    default:
        $ctrlFiche->index();
}
