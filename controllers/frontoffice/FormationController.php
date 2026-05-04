<?php

namespace controllers\frontoffice;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Formation.php';
require_once __DIR__ . '/../../models/Badge.php';

class FormationController {
    
    // ==========================================
    // FRONT OFFICE
    // ==========================================

    public function indexFront() {
        $formationModel = new \Formation();
        $formations = $formationModel->findAll();
        require __DIR__ . '/../../views/frontoffice/formation_list.php';
    }

    public function detailFront($id) {
        $formationModel = new \Formation();
        $formation = $formationModel->findById($id);
        
        if (!$formation) {
            $_SESSION['error'] = "Formation not found.";
            header("Location: index.php?action=list_formations_front");
            exit;
        }
        
        require __DIR__ . '/../../views/frontoffice/formation_detail.php';
    }

    public function indexRecommended($userId) {
        $formationModel = new \Formation();
        $formations = $formationModel->findByUserProject($userId);
        require __DIR__ . '/../../views/frontoffice/formation_recommended.php';
    }

    public function complete($id) {
        // Simulate user with hardcoded id
        $userId = 1;
        
        $formationModel = new \Formation();
        $formation = $formationModel->findById($id);
        
        if (!$formation) {
            $_SESSION['error'] = "Formation not found.";
            header("Location: index.php?action=list_formations_front");
            exit;
        }
        
        // Award badges linked to this formation
        $badgeModel = new \Badge();
        $badges = $badgeModel->findByFormationId($id);
        foreach ($badges as $badge) {
            $badgeModel->awardBadge($userId, $badge['id']);
        }
        
        $_SESSION['success'] = "Formation completed! Badges awarded.";
        header("Location: index.php?action=my_badges");
        exit;
    }
}