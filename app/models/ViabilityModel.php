<?php
require_once __DIR__ . '/BaseModel.php';

class ViabilityModel extends BaseModel {

    private $weights = [
        'marche'     => 0.30,
        'equipe'     => 0.25,
        'innovation' => 0.20,
        'finances'   => 0.25
    ];

    /**
     * Calculer le score de viabilité d'un projet
     */
    public function calculate($data) {
        $scores = [];

        // 1. Score Marché (30%)
        $marcheScores = ['grand' => 90, 'moyen' => 65, 'petit' => 40, 'niche' => 55];
        $concurrenceScores = ['faible' => 85, 'moyen' => 60, 'fort' => 35];
        $scores['marche'] = (
            ($marcheScores[strtolower($data['taille_marche'] ?? 'moyen')] ?? 60) * 0.6 +
            ($concurrenceScores[strtolower($data['niveau_concurrence'] ?? 'moyen')] ?? 55) * 0.4
        );

        // 2. Score Équipe (25%)
        $taille = intval($data['taille_equipe'] ?? 1);
        if ($taille >= 5) $scores['equipe'] = 90;
        elseif ($taille >= 3) $scores['equipe'] = 75;
        elseif ($taille >= 2) $scores['equipe'] = 60;
        else $scores['equipe'] = 40;

        // 3. Score Innovation (20%) - basé sur description
        $desc = strtolower($data['description'] ?? '');
        $innovationScore = 50;
        $innovKeywords = ['innovant','ia','intelligence artificielle','machine learning','blockchain','iot','big data','cloud','automatisation','disruptif','unique','premier','brevets','R&D','technologie','plateforme','algorithme','smart'];
        foreach ($innovKeywords as $kw) {
            if (strpos($desc, strtolower($kw)) !== false) $innovationScore += 6;
        }
        $scores['innovation'] = min(98, $innovationScore);

        // 4. Score Finances (25%)
        $coutInitial = floatval($data['cout_initial'] ?? 0);
        $budgetDemande = floatval($data['budget_demande'] ?? 0);
        $modele = strtolower($data['modele_economique'] ?? '');
        
        $finScore = 55;
        if (!empty($modele)) $finScore += 10;
        $modeleBonus = ['saas' => 20, 'abonnement' => 18, 'commission' => 15, 'marketplace' => 15, 'freemium' => 12, 'b2b' => 10, 'b2c' => 8];
        foreach ($modeleBonus as $m => $bonus) {
            if (strpos($modele, $m) !== false) { $finScore += $bonus; break; }
        }
        if ($coutInitial > 0 && $budgetDemande >= $coutInitial) $finScore += 10;
        $scores['finances'] = min(98, $finScore);

        // Score global pondéré
        $globalScore = 0;
        foreach ($this->weights as $key => $weight) {
            $globalScore += ($scores[$key] ?? 50) * $weight;
        }
        $globalScore = round(min(98, max(10, $globalScore)), 1);

        // Niveau de risque
        if ($globalScore >= 75) $risk = ['level' => 'Faible', 'color' => '#1D9E75', 'label' => 'Excellent potentiel'];
        elseif ($globalScore >= 55) $risk = ['level' => 'Modéré', 'color' => '#EF9F27', 'label' => 'Potentiel avec améliorations'];
        else $risk = ['level' => 'Élevé', 'color' => '#E24B4A', 'label' => 'Nécessite des ajustements majeurs'];

        $result = [
            'global_score' => $globalScore,
            'scores' => $scores,
            'weights' => $this->weights,
            'risk' => $risk,
            'recommendations' => $this->getRecommendations($scores)
        ];

        // Sauvegarder
        if (!empty($data['id_projet'])) {
            $this->query("
                INSERT INTO viability_history (id_projet, score, details, created_at) VALUES (?, ?, ?, NOW())
            ", [$data['id_projet'], $globalScore, json_encode($result)]);

            $this->query("UPDATE projet SET viability_score = ? WHERE id_projet = ?", [$globalScore, $data['id_projet']]);
        }

        return $result;
    }

    private function getRecommendations($scores) {
        $recs = [];
        if ($scores['marche'] < 60) $recs[] = ['icon' => '📊', 'text' => 'Élargissez votre étude de marché et identifiez des niches à fort potentiel.'];
        if ($scores['equipe'] < 60) $recs[] = ['icon' => '👥', 'text' => 'Renforcez votre équipe avec des profils complémentaires (tech, business, marketing).'];
        if ($scores['innovation'] < 60) $recs[] = ['icon' => '💡', 'text' => 'Intégrez plus d\'éléments innovants (IA, IoT, blockchain) pour vous différencier.'];
        if ($scores['finances'] < 60) $recs[] = ['icon' => '💰', 'text' => 'Clarifiez votre modèle économique et vos projections financières.'];
        if (empty($recs)) $recs[] = ['icon' => '🎯', 'text' => 'Excellent profil ! Concentrez-vous sur l\'exécution et la croissance.'];
        return $recs;
    }

    public function getHistory($id_projet) {
        return $this->query("
            SELECT * FROM viability_history WHERE id_projet = ? ORDER BY created_at DESC LIMIT 10
        ", [$id_projet])->fetchAll();
    }
}
