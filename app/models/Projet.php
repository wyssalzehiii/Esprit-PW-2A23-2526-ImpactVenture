<?php
require_once __DIR__ . '/BaseModel.php';

class Projet extends BaseModel {

    public function getAll() {
        return $this->query("
            SELECT p.*, f.nom as entreprise_nom, f.categorie as entreprise_categorie 
            FROM projet p 
            LEFT JOIN fiche_entreprise f ON p.id_fiche_entreprise = f.id 
            ORDER BY p.date_soumission DESC
        ")->fetchAll();
    }

    public function getById($id) {
        return $this->query("
            SELECT p.*, f.nom as entreprise_nom, f.categorie as entreprise_categorie 
            FROM projet p 
            LEFT JOIN fiche_entreprise f ON p.id_fiche_entreprise = f.id 
            WHERE p.id_projet = ?
        ", [$id])->fetch();
    }

    public function create($data) {
        $this->query("
            INSERT INTO projet (titre, description, id_fiche_entreprise, id_user, statut) 
            VALUES (?, ?, ?, ?, 'soumis')
        ", [
            $data['titre'], 
            $data['description'], 
            $data['id_fiche_entreprise'], 
            $data['id_user'] ?? 1
        ]);

        $id_projet = $this->pdo->lastInsertId();

        // Système de Badges
        $this->checkProjectBadges($data['id_user'] ?? 1);

        return $id_projet;
    }

    public function update($id, $data) {
        $this->query("
            UPDATE projet SET 
                titre = ?, description = ?, id_fiche_entreprise = ?, statut = ?
            WHERE id_projet = ?
        ", [
            $data['titre'], 
            $data['description'], 
            $data['id_fiche_entreprise'], 
            $data['statut'] ?? 'soumis',
            $id
        ]);

        // Système de Badges
        $this->checkProjectBadges();
    }
    // ===================== SYSTEME DE BADGES (intégré dans projet) =====================
    private function awardProjectBadge($id_fiche_entreprise, $badge_name) {
        if (empty($id_fiche_entreprise)) return false;

        $ficheModel = new FicheEntreprise();
        $fiche = $ficheModel->getById($id_fiche_entreprise);
        if (!$fiche) return false;

        $current = !empty($fiche['badges']) ? explode(',', $fiche['badges']) : [];
        if (in_array($badge_name, $current)) return false;

        $current[] = $badge_name;
        $new_badges = implode(',', $current);

        $ficheModel->query("UPDATE fiche_entreprise SET badges = ? WHERE id = ?", 
            [$new_badges, $id_fiche_entreprise]);
        return true;
    }

    public function checkProjectBadges($id_user = 1) {
        // Impact Pioneer + Green Innovator
        $projets = $this->query("SELECT * FROM projet WHERE id_user = ?", [$id_user])->fetchAll();

        foreach ($projets as $p) {
            if ($p['statut'] === 'accepté') {
                $this->awardProjectBadge($p['id_fiche_entreprise'], 'Impact Pioneer');
            }
            if (!empty($p['score_ia']) && $p['score_ia'] > 85) {
                $this->awardProjectBadge($p['id_fiche_entreprise'], 'Green Innovator');
            }
        }

        // Consistency King
        if (count($projets) >= 5) {
            $firstFiche = $projets[0]['id_fiche_entreprise'] ?? null;
            if ($firstFiche) {
                $this->awardProjectBadge($firstFiche, 'Consistency King');
            }
        }
    }

    public function delete($id) {
        return $this->query("DELETE FROM projet WHERE id_projet = ?", [$id]);
    }

    public function count() {
        return (int)$this->query("SELECT COUNT(*) FROM projet")->fetchColumn();
    }

    public function getStats() {
        $row = $this->query("
            SELECT 
                COUNT(*) as total,
                SUM(statut='accepté') as acceptes,
                SUM(statut='en_evaluation') as en_evaluation,
                SUM(statut='soumis') as soumis,
                SUM(statut='rejeté') as rejetes,
                AVG(score_ia) as avg_score_ia
            FROM projet
        ")->fetch();
        return $row ?: [];
    }
}