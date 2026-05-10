<?php
/**
 * Scoring helpers (anti‑gaming, diversification) + reco engine.
 */

if (!function_exists('iv_word_tokens_fr')) {
    /** @return list<string> */
    function iv_word_tokens_fr(string $text): array
    {
        if (!preg_match_all('/\p{L}+/u', mb_strtolower($text, 'UTF-8'), $m)) {
            return [];
        }
        return $m[0];
    }
}

if (!function_exists('iv_word_count_fr')) {
    function iv_word_count_fr(string $text): int
    {
        return count(iv_word_tokens_fr($text));
    }
}

if (!function_exists('iv_unique_word_ratio')) {
    function iv_unique_word_ratio(string $text): float
    {
        $w = iv_word_tokens_fr($text);
        if ($w === []) {
            return 0.0;
        }
        return count(array_unique($w)) / max(1, count($w));
    }
}

if (!function_exists('iv_keyword_bonus_once_each')) {
    /**
     * @param list<string> $keywords lowercase substrings — each counted at most once if present anywhere.
     */
    function iv_keyword_bonus_once_each(string $haystackLc, array $keywords): int
    {
        $n = 0;
        foreach ($keywords as $kw) {
            if ($kw === '') {
                continue;
            }
            if (strpos($haystackLc, $kw) !== false) {
                $n++;
            }
        }
        return $n;
    }
}

if (!function_exists('iv_structure_triple_bonus')) {
    /** +15 si problème ET solution ET marché (indicateurs) */
    function iv_structure_triple_bonus(string $textLc): bool
    {
        $problem = preg_match('/\b(problème|defi|défi|besoin)\b/u', $textLc);
        $solution = preg_match('/\b(solution|permet|offre|résoud|ameliore|améliore)\b/u', $textLc);
        $market = preg_match('/\b(client|marché|marche|utilisateur|usage|segments?)\b/u', $textLc);
        return (bool) ($problem && $solution && $market);
    }
}

if (!function_exists('iv_positive_keyword_set')) {
    /** @return list<string> */
    function iv_positive_keyword_set(): array
    {
        return ['innovant', 'durable', 'rentable', 'succès', 'succes', 'opportunité', 'opportunite', 'croissance', 'impact'];
    }
}

if (!function_exists('iv_negative_keyword_set')) {
    /** @return list<string> */
    function iv_negative_keyword_set(): array
    {
        return ['risque', 'difficile', 'incertain', 'coût', 'cout', 'problème', 'probleme', 'faible'];
    }
}

if (!function_exists('iv_green_keyword_set')) {
    /** @return list<string> */
    function iv_green_keyword_set(): array
    {
        return ['vert', 'solaire', 'énergie', 'energie', 'durable', 'environnement', 'co2', 'bio',
            'agriculture', 'renouvelable', 'écologie', 'ecologie', 'green', 'impact', 'carbone', 'climat'];
    }
}

if (!function_exists('iv_innovation_keyword_set')) {
    /** @return list<string> */
    function iv_innovation_keyword_set(): array
    {
        return ['innovant', 'ia', 'machine learning', 'blockchain', 'iot', 'cloud', 'automatisation',
            'unique', 'technologie', 'plateforme', 'algo', 'application', 'app', 'logiciel'];
    }
}

if (!function_exists('iv_text_scoring_adjustments')) {
    /**
     * @return array{length_penalty:int,structure_bonus:int,diversity_bonus:int}
     */
    function iv_text_scoring_adjustments(string $text): array
    {
        $wc = iv_word_count_fr($text);
        $lengthPenalty = $wc < 30 ? -20 : 0;
        $ratio = iv_unique_word_ratio($text);
        $structure = iv_structure_triple_bonus(mb_strtolower($text, 'UTF-8')) ? 15 : 0;
        $diversity = $ratio > 0.6 ? 10 : 0;
        return [
            'length_penalty' => $lengthPenalty,
            'structure_bonus' => $structure,
            'diversity_bonus' => $diversity,
            'unique_ratio' => $ratio,
            'words' => $wc,
        ];
    }
}

if (!function_exists('iv_risk_score_from_text_pitch')) {
    /** Score risque élevé = mauvais (court / peu diversifié), 0–100 */
    function iv_risk_score_from_text_pitch(string $description, float $pitchQuality): float
    {
        $wc = iv_word_count_fr($description);
        $ratio = iv_unique_word_ratio($description);
        $risk = 40.0;
        if ($wc < 30) {
            $risk += 35.0;
        } elseif ($wc < 60) {
            $risk += 18.0;
        }
        if ($ratio > 0.0 && $ratio < 0.35) {
            $risk += 18.0;
        }
        if (!iv_structure_triple_bonus(mb_strtolower($description, 'UTF-8'))) {
            $risk += 12.0;
        }
        $risk -= ($pitchQuality / 100.0) * 25.0;
        return round(min(100.0, max(0.0, $risk)), 1);
    }
}

/**
 * Texte des recommandations « prochaines étapes » — logique métier demandée.
 *
 * @return list<string>
 */
function iv_build_next_step_recommendations(float $market, float $innovation, float $sustainability, float $risk): array
{
    $recs = [];
    if ($market < 50.0) {
        $recs[] = '💰 Votre score Marché est faible. Ajoutez dans votre description: qui sont vos clients cibles, quel est le prix, et comment vous gagnez de l\'argent.';
    }
    if ($innovation < 50.0) {
        $recs[] = '💡 Votre score Innovation est faible. Mentionnez la technologie utilisée (app, IA, IoT, plateforme...).';
    }
    if ($sustainability < 60.0) {
        $recs[] = '🌱 Score Durabilité bas. Expliquez l\'impact environnemental ou social de votre projet.';
    }
    if ($risk > 60.0) {
        $recs[] = '⚠️ Risque élevé détecté. Votre description est trop courte ou vague. Ajoutez minimum 3 phrases structurées.';
    }
    if ($recs === []) {
        $recs[] = '🏆 Excellent! Votre projet est bien structuré. Pensez à ajouter vos KPIs et métriques de traction.';
    }
    return $recs;
}
