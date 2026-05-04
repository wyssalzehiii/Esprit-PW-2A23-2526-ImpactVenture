<?php
require_once __DIR__ . '/BaseModel.php';

class BadgeModel extends BaseModel {

    public function getAllBadges() {
        return $this->query("SELECT * FROM badge ORDER BY id")->fetchAll();
    }

    public function getBadgesForFiche($id_fiche) {
        return $this->query("
            SELECT b.*, ub.awarded_at FROM badge b 
            INNER JOIN user_badges ub ON ub.id_badge = b.id 
            WHERE ub.id_fiche_entreprise = ? ORDER BY ub.awarded_at DESC
        ", [$id_fiche])->fetchAll();
    }

    public function awardBadge($id_fiche, $id_badge) {
        try {
            $this->query("INSERT IGNORE INTO user_badges (id_fiche_entreprise, id_badge) VALUES (?, ?)", [$id_fiche, $id_badge]);
            return true;
        } catch (Exception $e) { return false; }
    }

    public function checkAndAward($id_fiche) {
        $fiche = $this->query("SELECT * FROM fiche_entreprise WHERE id = ?", [$id_fiche])->fetch();
        if (!$fiche) return [];

        $badges = $this->getAllBadges();
        $awarded = [];

        $projets = $this->query("SELECT * FROM projet WHERE id_fiche_entreprise = ?", [$id_fiche])->fetchAll();
        $projetCount = count($projets);
        $hasAccepted = false; $maxScoreIA = 0; $maxViability = 0; $maxPitch = 0; $sdgCount = 0;
        $hasCO2 = $this->query("SELECT COUNT(*) FROM co2_calculation WHERE id_fiche_entreprise = ?", [$id_fiche])->fetchColumn();

        foreach ($projets as $p) {
            if ($p['statut'] === 'accepté') $hasAccepted = true;
            if (($p['score_ia'] ?? 0) > $maxScoreIA) $maxScoreIA = $p['score_ia'];
            if (($p['viability_score'] ?? 0) > $maxViability) $maxViability = $p['viability_score'];
            if (($p['pitch_score'] ?? 0) > $maxPitch) $maxPitch = $p['pitch_score'];
            $tags = array_filter(explode(',', $p['sdg_tags'] ?? ''));
            if (count($tags) > $sdgCount) $sdgCount = count($tags);
        }

        foreach ($badges as $b) {
            $shouldAward = false;
            switch ($b['condition_type']) {
                case 'green_score': $shouldAward = ($fiche['score_green'] ?? 0) >= $b['condition_value']; break;
                case 'pitch_score': $shouldAward = $maxPitch >= $b['condition_value']; break;
                case 'project_count': $shouldAward = $projetCount >= $b['condition_value']; break;
                case 'project_accepted': $shouldAward = $hasAccepted; break;
                case 'score_ia': $shouldAward = $maxScoreIA >= $b['condition_value']; break;
                case 'sdg_count': $shouldAward = $sdgCount >= $b['condition_value']; break;
                case 'viability_score': $shouldAward = $maxViability >= $b['condition_value']; break;
                case 'co2_calculated': $shouldAward = $hasCO2 > 0; break;
            }
            if ($shouldAward) {
                $this->awardBadge($id_fiche, $b['id']);
                $awarded[] = $b;
            }
        }
        return $awarded;
    }

    public function getLeaderboard() {
        return $this->query("
            SELECT f.id, f.nom, f.score_green, COUNT(ub.id) as badge_count
            FROM fiche_entreprise f 
            LEFT JOIN user_badges ub ON ub.id_fiche_entreprise = f.id
            GROUP BY f.id ORDER BY badge_count DESC, f.score_green DESC LIMIT 10
        ")->fetchAll();
    }

    public function getProgress($id_fiche) {
        $total = (int)$this->query("SELECT COUNT(*) FROM badge")->fetchColumn();
        $earned = (int)$this->query("SELECT COUNT(*) FROM user_badges WHERE id_fiche_entreprise = ?", [$id_fiche])->fetchColumn();
        return ['total' => $total, 'earned' => $earned, 'percent' => $total > 0 ? round(($earned / $total) * 100) : 0];
    }
}
