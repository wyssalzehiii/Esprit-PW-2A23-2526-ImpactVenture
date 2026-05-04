<?php

namespace controllers\frontoffice;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Badge.php';

class BadgeController {
    
    // ==========================================
    // FRONT OFFICE
    // ==========================================

    public function indexFront() {
        $badgeModel = new \Badge();
        $badges = $badgeModel->findAll();
        require __DIR__ . '/../../views/frontoffice/badge_list.php';
    }

    public function detailFront($id) {
        $badgeModel = new \Badge();
        $badge = $badgeModel->findById($id);
        
        if (!$badge) {
            $_SESSION['error'] = "Badge not found.";
            header("Location: index.php?action=list_badges_front");
            exit;
        }
        
        require __DIR__ . '/../../views/frontoffice/badge_detail.php';
    }

    public function myBadges($userId) {
        $badgeModel = new \Badge();
        $badges = $badgeModel->getUserBadges($userId);
        require __DIR__ . '/../../views/frontoffice/my_badges.php';
    }
}