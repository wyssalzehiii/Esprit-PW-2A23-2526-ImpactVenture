<?php

namespace controllers\backoffice;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Badge.php';
require_once __DIR__ . '/../../models/Formation.php';

class BadgeController {
    
    // ==========================================
    // BACK OFFICE
    // ==========================================

    public function index() {
        $badgeModel = new \Badge();
        $badges = $badgeModel->findAll();
        require __DIR__ . '/../../views/backoffice/badge_list.php';
    }

    public function create() {
        $formationModel = new \Formation();
        $formations = $formationModel->findAll();
        require __DIR__ . '/../../views/backoffice/badge_create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $image = isset($_POST['image']) ? trim($_POST['image']) : '';
            $points = isset($_POST['points']) ? (int)$_POST['points'] : 0;
            $formation_id = isset($_POST['formation_id']) ? (int)$_POST['formation_id'] : 0;
            
            $errors = $this->validate($nom, $description, $points, $formation_id);
            
            if (!empty($errors)) {
                $formationModel = new Formation();
                $formations = $formationModel->findAll();
                require __DIR__ . '/../../views/backoffice/badge_create.php';
                return;
            }

            $badge = new \Badge();
            $badge->setNom($nom);
            $badge->setDescription($description);
            $badge->setImage($image);
            $badge->setPoints($points);
            $badge->setFormationId($formation_id);
            
            if ($badge->create()) {
                $_SESSION['success'] = "Badge created successfully!";
                header("Location: index.php?action=list_badges");
                exit;
            } else {
                $errors[] = "Database error. Could not save badge.";
                $formationModel = new Formation();
                $formations = $formationModel->findAll();
                require __DIR__ . '/../../views/backoffice/badge_create.php';
            }
        }
    }

    public function edit($id) {
        $badgeModel = new \Badge();
        $badge = $badgeModel->findById($id);
        
        if (!$badge) {
            $_SESSION['error'] = "Badge not found.";
            header("Location: index.php?action=list_badges");
            exit;
        }
        
        $formationModel = new Formation();
        $formations = $formationModel->findAll();
        
        $nom = $badge['nom'];
        $description = $badge['description'];
        $image = $badge['image'];
        $points = $badge['points'];
        $formation_id = $badge['formation_id'];
        $badge_id = $id;
        
        require __DIR__ . '/../../views/backoffice/badge_edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $image = isset($_POST['image']) ? trim($_POST['image']) : '';
            $points = isset($_POST['points']) ? (int)$_POST['points'] : 0;
            $formation_id = isset($_POST['formation_id']) ? (int)$_POST['formation_id'] : 0;
            
            $errors = $this->validate($nom, $description, $points, $formation_id);
            
            if (!empty($errors)) {
                $formationModel = new Formation();
                $formations = $formationModel->findAll();
                $badge_id = $id;
                require __DIR__ . '/../../views/backoffice/badge_edit.php';
                return;
            }

            $badge = new \Badge();
            $badge->setId($id);
            $badge->setNom($nom);
            $badge->setDescription($description);
            $badge->setImage($image);
            $badge->setPoints($points);
            $badge->setFormationId($formation_id);
            
            if ($badge->update()) {
                $_SESSION['success'] = "Badge updated successfully!";
                header("Location: index.php?action=list_badges");
                exit;
            } else {
                $errors[] = "Database error. Could not update badge.";
                $formationModel = new Formation();
                $formations = $formationModel->findAll();
                $badge_id = $id;
                require __DIR__ . '/../../views/backoffice/badge_edit.php';
            }
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $badge = new \Badge();
            $badge->setId($id);
            if ($badge->delete()) {
                $_SESSION['success'] = "Badge deleted successfully!";
            } else {
                $_SESSION['error'] = "Could not delete badge.";
            }
            header("Location: index.php?action=list_badges");
            exit;
        } else {
            // Confirm delete view
            $badgeModel = new \Badge();
            $badge = $badgeModel->findById($id);
            if (!$badge) {
                $_SESSION['error'] = "Badge not found.";
                header("Location: index.php?action=list_badges");
                exit;
            }
            require __DIR__ . '/../../views/backoffice/badge_delete.php';
        }
    }

    // ==========================================
    // VALIDATION
    // ==========================================
    
    private function validate($nom, $description, $points, $formation_id) {
        $errors = [];
        
        if (empty($nom)) {
            $errors['nom'] = "Name is required.";
        } elseif (mb_strlen($nom) < 3) {
            $errors['nom'] = "Name must be at least 3 characters.";
        } elseif (mb_strlen($nom) > 255) {
            $errors['nom'] = "Name must be less than 255 characters.";
        }
        
        if (empty($description)) {
            $errors['description'] = "Description is required.";
        } elseif (mb_strlen($description) < 10) {
            $errors['description'] = "Description must be at least 10 characters.";
        }
        
        if ($points < 0 || $points > 1000) {
            $errors['points'] = "Points must be between 0 and 1000.";
        }
        
        // Check if formation_id exists
        $formationModel = new \Formation();
        if (!$formationModel->findById($formation_id)) {
            $errors['formation_id'] = "Invalid formation selected.";
        }
        
        return $errors;
    }
}