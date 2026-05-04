<?php
require_once __DIR__ . '/BaseModel.php';

class MatchingModel extends BaseModel {

    /**
     * Trouver les mentors compatibles avec un projet
     */
    public function findMentors($projet, $limit = 5) {
        $mentors = $this->query("SELECT * FROM mentor WHERE disponible = 1")->fetchAll();
        $scores = [];

        foreach ($mentors as $m) {
            $score = $this->calculateMatchScore($projet, $m);
            $scores[] = array_merge($m, ['match_score' => $score]);
        }

        usort($scores, fn($a, $b) => $b['match_score'] <=> $a['match_score']);
        return array_slice($scores, 0, $limit);
    }

    /**
     * Trouver les investisseurs compatibles avec un projet
     */
    public function findInvestors($projet, $limit = 5) {
        $investisseurs = $this->query("SELECT * FROM investisseur WHERE actif = 1")->fetchAll();
        $scores = [];

        foreach ($investisseurs as $inv) {
            $score = $this->calculateInvestorMatchScore($projet, $inv);
            $scores[] = array_merge($inv, ['match_score' => $score]);
        }

        usort($scores, fn($a, $b) => $b['match_score'] <=> $a['match_score']);
        return array_slice($scores, 0, $limit);
    }

    /**
     * Score de matching Mentor ↔ Projet
     */
    private function calculateMatchScore($projet, $mentor) {
        $score = 0;
        $max = 100;

        // 1. Catégorie matching (30 pts)
        $mentorSecteurs = array_map('trim', explode(',', strtolower($mentor['secteurs'] ?? '')));
        $projetCategorie = strtolower($projet['entreprise_categorie'] ?? '');
        if (in_array($projetCategorie, $mentorSecteurs)) {
            $score += 30;
        } else {
            foreach ($mentorSecteurs as $s) {
                if (!empty($s) && strpos($projetCategorie, $s) !== false) {
                    $score += 15;
                    break;
                }
            }
        }

        // 2. Mots-clés communs (30 pts)
        $mentorKw = array_map('trim', explode(',', strtolower($mentor['mots_cles'] ?? '')));
        $projetDesc = strtolower(($projet['titre'] ?? '') . ' ' . ($projet['description'] ?? ''));
        $ficheKw = array_map('trim', explode(',', strtolower($projet['mots_cles'] ?? '')));
        
        $commonCount = 0;
        foreach ($mentorKw as $kw) {
            if (!empty($kw) && (strpos($projetDesc, $kw) !== false || in_array($kw, $ficheKw))) {
                $commonCount++;
            }
        }
        $score += min(30, $commonCount * 10);

        // 3. Localisation (20 pts)
        $mentorVille = strtolower(trim($mentor['ville'] ?? ''));
        $projetVille = strtolower(trim($projet['ville'] ?? ''));
        if (!empty($mentorVille) && !empty($projetVille) && $mentorVille === $projetVille) {
            $score += 20;
        }

        // 4. Expérience (20 pts)
        $exp = $mentor['experience_annees'] ?? 0;
        $score += min(20, $exp * 2);

        return min($max, $score);
    }

    /**
     * Score de matching Investisseur ↔ Projet
     */
    private function calculateInvestorMatchScore($projet, $investisseur) {
        $score = 0;
        $max = 100;

        // 1. Catégorie (25 pts)
        $invSecteurs = array_map('trim', explode(',', strtolower($investisseur['secteurs'] ?? '')));
        $projetCategorie = strtolower($projet['entreprise_categorie'] ?? '');
        if (in_array($projetCategorie, $invSecteurs)) {
            $score += 25;
        }

        // 2. Mots-clés (25 pts)
        $invKw = array_map('trim', explode(',', strtolower($investisseur['mots_cles'] ?? '')));
        $projetDesc = strtolower(($projet['titre'] ?? '') . ' ' . ($projet['description'] ?? ''));
        $commonCount = 0;
        foreach ($invKw as $kw) {
            if (!empty($kw) && strpos($projetDesc, $kw) !== false) {
                $commonCount++;
            }
        }
        $score += min(25, $commonCount * 8);

        // 3. Budget matching (30 pts)
        $budgetDemande = floatval($projet['budget_demande'] ?? 0);
        $budgetMin = floatval($investisseur['budget_min'] ?? 0);
        $budgetMax = floatval($investisseur['budget_max'] ?? 0);
        if ($budgetDemande > 0 && $budgetDemande >= $budgetMin && $budgetDemande <= $budgetMax) {
            $score += 30;
        } elseif ($budgetDemande > 0 && $budgetDemande <= $budgetMax * 1.2) {
            $score += 15;
        }

        // 4. Localisation (20 pts)
        $invVille = strtolower(trim($investisseur['ville'] ?? ''));
        $projetVille = strtolower(trim($projet['ville'] ?? ''));
        if (!empty($invVille) && !empty($projetVille) && $invVille === $projetVille) {
            $score += 20;
        }

        return min($max, $score);
    }

    public function getAllMentors() {
        return $this->query("SELECT * FROM mentor ORDER BY experience_annees DESC")->fetchAll();
    }

    public function getAllInvestors() {
        return $this->query("SELECT * FROM investisseur ORDER BY budget_max DESC")->fetchAll();
    }
}
