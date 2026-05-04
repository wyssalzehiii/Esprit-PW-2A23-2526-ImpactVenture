<?php
require_once __DIR__ . '/BaseModel.php';

class FicheEntreprise extends BaseModel {

    public function getAll() {
        return $this->query("
            SELECT f.*, COUNT(p.id_projet) as nb_projets 
            FROM fiche_entreprise f 
            LEFT JOIN projet p ON p.id_fiche_entreprise = f.id 
            GROUP BY f.id 
            ORDER BY f.created_at DESC
        ")->fetchAll();
    }

    public function getById($id) {
        return $this->query("SELECT * FROM fiche_entreprise WHERE id = ?", [$id])->fetch();
    }
    public function create($data) {
        $score_green = $this->calculateGreenScore($data['description'] ?? '');
        $logo = !empty($data['logo']) ? $data['logo'] : $this->generateLogoPath($data['nom'] ?? '');

        $this->query("
            INSERT INTO fiche_entreprise (nom, description, logo, categorie, mots_cles, score_green, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ", [$data['nom'], $data['description'], $logo, $data['categorie'], $data['mots_cles'] ?? '', $score_green]);

        // Système de Badges
        $this->checkGreenBadges();
    }

    public function update($id, $data) {
        $score_green = $this->calculateGreenScore($data['description'] ?? '');
        $logo = !empty($data['logo']) ? $data['logo'] : $this->generateLogoPath($data['nom'] ?? '');

        $this->query("
            UPDATE fiche_entreprise SET 
                nom = ?, description = ?, logo = ?, categorie = ?, 
                mots_cles = ?, score_green = ?
            WHERE id = ?
        ", [$data['nom'], $data['description'], $logo, $data['categorie'], $data['mots_cles'] ?? '', $score_green, $id]);

        // Système de Badges
        $this->checkGreenBadges();
    }
    public function delete($id) {
        return $this->query("DELETE FROM fiche_entreprise WHERE id = ?", [$id]);
    }

    public function count() {
        return (int)$this->query("SELECT COUNT(*) FROM fiche_entreprise")->fetchColumn();
    }

    public function avgGreen() {
        $val = $this->query("SELECT AVG(score_green) FROM fiche_entreprise")->fetchColumn();
        return round($val ?: 0);
    }

    // IA Simulation
    private function calculateGreenScore($description) {
        $score = 55;
        $text = strtolower($description);
        $keywords = ['vert','solaire','énergie','durable','environnement','co2','bio','agriculture','renouvelable','écologie','green','impact'];
        foreach ($keywords as $kw) {
            if (strpos($text, $kw) !== false) $score += 7;
        }
        return min(98, max(40, $score));
    }

    private function generateLogoPath($nom) {
        if (empty($nom)) return null;
        $safe = preg_replace('/[^a-z0-9]/i', '-', strtolower(trim($nom)));
        return "logos/" . $safe . ".png";
    }
    // ===================== SYSTEME DE BADGES (intégré dans fiche_entreprise) =====================
    private function awardBadge($id_fiche, $badge_name) {
        $fiche = $this->getById($id_fiche);
        if (!$fiche) return false;

        $current = !empty($fiche['badges']) ? explode(',', $fiche['badges']) : [];
        if (in_array($badge_name, $current)) return false;

        $current[] = $badge_name;
        $new_badges = implode(',', $current);

        $this->query("UPDATE fiche_entreprise SET badges = ? WHERE id = ?", [$new_badges, $id_fiche]);
        return true;
    }

     public function checkGreenBadges() {
        $fiches = $this->getAll();
        foreach ($fiches as $f) {
            if (!empty($f['score_green']) && $f['score_green'] >= 80) {
                $this->awardBadge($f['id'], 'Eco Warrior');
                echo "<!-- Badge Eco Warrior donné à : " . $f['nom'] . " -->"; // pour debug
            }
        }
    }

    public function getTopFiches($limit = 5) {
        return $this->query("
            SELECT f.*, COUNT(p.id_projet) as nb_projets 
            FROM fiche_entreprise f 
            LEFT JOIN projet p ON p.id_fiche_entreprise = f.id 
            GROUP BY f.id 
            ORDER BY nb_projets DESC, f.score_green DESC 
            LIMIT $limit
        ")->fetchAll();
    }
}