<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Formation.php';

class FormationController {
    
    // ==========================================
    // FRONT OFFICE
    // ==========================================

    public function indexFront() {
        $formationModel = new Formation();
        $formations = $formationModel->findAll();
        require __DIR__ . '/../views/frontoffice/formation_list.php';
    }

    public function detailFront($id) {
        $formationModel = new Formation();
        $formation = $formationModel->findById($id);
        
        if (!$formation) {
            $_SESSION['error'] = "Formation not found.";
            header("Location: index.php?action=list_formations_front");
            exit;
        }
        
        require __DIR__ . '/../views/frontoffice/formation_detail.php';
    }

    // ==========================================
    // BACK OFFICE
    // ==========================================

    public function index() {
        $formationModel = new Formation();
        $formations = $formationModel->findAll();
        require __DIR__ . '/../views/backoffice/formation_list.php';
    }

    public function create() {
        require __DIR__ . '/../views/backoffice/formation_create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            
            $errors = $this->validate($title, $content);
            
            if (!empty($errors)) {
                require __DIR__ . '/../views/backoffice/formation_create.php';
                return;
            }

            $formation = new Formation();
            $formation->setTitle($title);
            $formation->setContent($content);
            
            if ($formation->create()) {
                $_SESSION['success'] = "Formation created successfully!";
                header("Location: index.php?action=list_formations");
                exit;
            } else {
                $errors[] = "Database error. Could not save formation.";
                require __DIR__ . '/../views/backoffice/formation_create.php';
            }
        }
    }

    public function edit($id) {
        $formationModel = new Formation();
        $formation = $formationModel->findById($id);
        
        if (!$formation) {
            $_SESSION['error'] = "Formation not found.";
            header("Location: index.php?action=list_formations");
            exit;
        }
        
        $title = $formation->getTitle();
        $content = $formation->getContent();
        
        require __DIR__ . '/../views/backoffice/formation_edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            
            $errors = $this->validate($title, $content);
            
            if (!empty($errors)) {
                $formation_id = $id; 
                require __DIR__ . '/../views/backoffice/formation_edit.php';
                return;
            }

            $formation = new Formation();
            if ($formation->findById($id)) {
                $formation->setTitle($title);
                $formation->setContent($content);
                
                if ($formation->update()) {
                    $_SESSION['success'] = "Formation updated successfully!";
                    header("Location: index.php?action=list_formations");
                    exit;
                } else {
                    $errors[] = "Database error. Could not update formation.";
                    $formation_id = $id;
                    require __DIR__ . '/../views/backoffice/formation_edit.php';
                }
            } else {
                $_SESSION['error'] = "Formation not found.";
                header("Location: index.php?action=list_formations");
                exit;
            }
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formation = new Formation();
            if ($formation->findById($id)) {
                if ($formation->delete()) {
                    $_SESSION['success'] = "Formation deleted successfully!";
                } else {
                    $_SESSION['error'] = "Could not delete formation.";
                }
            } else {
                $_SESSION['error'] = "Formation not found.";
            }
            header("Location: index.php?action=list_formations");
            exit;
        } else {
            // Confirm delete view
            $formationModel = new Formation();
            $formation = $formationModel->findById($id);
            if (!$formation) {
                $_SESSION['error'] = "Formation not found.";
                header("Location: index.php?action=list_formations");
                exit;
            }
            require __DIR__ . '/../views/backoffice/formation_delete.php';
        }
    }

    // ==========================================
    // VALIDATION
    // ==========================================
    
    private function validate($title, $content) {
        $errors = [];
        
        if (empty($title)) {
            $errors['title'] = "Title is required.";
        } elseif (mb_strlen($title) < 5) {
            $errors['title'] = "Title must be at least 5 characters.";
        } elseif (mb_strlen($title) > 255) {
            $errors['title'] = "Title must be less than 255 characters.";
        }
        
        if (empty($content)) {
            $errors['content'] = "Content is required.";
        } elseif (mb_strlen($content) < 20) {
            $errors['content'] = "Content must be at least 20 characters.";
        }
        
        return $errors;
    }
}
?>
