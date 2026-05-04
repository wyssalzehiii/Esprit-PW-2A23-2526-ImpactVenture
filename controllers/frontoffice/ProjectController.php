<?php

namespace controllers\frontoffice;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../models/Project.php';

class ProjectController {

    // ==========================================
    // FRONT OFFICE
    // ==========================================

    public function indexFront() {
        $projectModel = new \Project();
        $projects = $projectModel->findAllByUser(1); // hardcoded user
        require __DIR__ . '/../../views/frontoffice/project_list.php';
    }

    public function create() {
        require __DIR__ . '/../../views/frontoffice/project_create.php';
    }

    public function store() {
        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?action=create_project_front");
            exit;
        }

        $project = new \Project();
        $project->setUserId(1); // hardcoded
        $project->setTitle($_POST['title']);
        $project->setCategory($_POST['category']);
        $project->setDescription($_POST['description'] ?? '');
        $project->setLinkedToPath(0);

        if ($project->create()) {
            $_SESSION['success'] = "Project created successfully!";
            header("Location: index.php?action=list_projects_front");
            exit;
        } else {
            $_SESSION['error'] = "Database error. Could not save project.";
            header("Location: index.php?action=create_project_front");
            exit;
        }
    }

    public function edit($id) {
        $projectModel = new \Project();
        $project = $projectModel->findById($id);

        if (!$project || $project['user_id'] != 1) { // hardcoded
            $_SESSION['error'] = "Project not found.";
            header("Location: index.php?action=list_projects_front");
            exit;
        }

        require __DIR__ . '/../../views/frontoffice/project_edit.php';
    }

    public function update($id) {
        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?action=edit_project_front&id=$id");
            exit;
        }

        $project = new \Project();
        $project->setId($id);
        $project->setTitle($_POST['title']);
        $project->setCategory($_POST['category']);
        $project->setDescription($_POST['description'] ?? '');
        // Preserve linked_to_path
        $existing = $project->findById($id);
        $project->setLinkedToPath($existing['linked_to_path']);

        if ($project->update()) {
            $_SESSION['success'] = "Project updated successfully!";
            header("Location: index.php?action=list_projects_front");
            exit;
        } else {
            $_SESSION['error'] = "Database error. Could not update project.";
            header("Location: index.php?action=edit_project_front&id=$id");
            exit;
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project = new \Project();
            $project->setId($id);
            if ($project->delete()) {
                $_SESSION['success'] = "Project deleted successfully!";
            } else {
                $_SESSION['error'] = "Could not delete project.";
            }
            header("Location: index.php?action=list_projects_front");
            exit;
        } else {
            // Confirm delete view
            $projectModel = new \Project();
            $project = $projectModel->findById($id);
            if (!$project || $project['user_id'] != 1) {
                $_SESSION['error'] = "Project not found.";
                header("Location: index.php?action=list_projects_front");
                exit;
            }
            require __DIR__ . '/../../views/frontoffice/project_delete.php';
        }
    }

    public function toggleLink($id) {
        $project = new \Project();
        $project->setId($id);
        if ($project->toggleLinkedToPath()) {
            $_SESSION['success'] = "Project link status updated!";
        } else {
            $_SESSION['error'] = "Could not update project link status.";
        }
        header("Location: index.php?action=list_projects_front");
        exit;
    }

    private function validate($data) {
        $errors = [];

        if (empty($data['title']) || strlen($data['title']) < 3 || strlen($data['title']) > 255) {
            $errors['title'] = "Title must be between 3 and 255 characters.";
        }

        $validCategories = ['FinTech', 'AgriTech', 'CleanTech', 'AI', 'General'];
        if (empty($data['category']) || !in_array($data['category'], $validCategories)) {
            $errors['category'] = "Please select a valid category.";
        }

        if (isset($data['description']) && strlen($data['description']) > 1000) {
            $errors['description'] = "Description must be less than 1000 characters.";
        }

        return $errors;
    }
}
?>