<?php
require_once __DIR__ . '/BaseModel.php';

class SDGModel extends BaseModel {

    public function analyze($description, $titre = '') {
        try {
            $sdgs = $this->query("SELECT * FROM sdg ORDER BY numero")->fetchAll();
        } catch (Throwable $e) {
            // Fallback en cas d'absence de la table sdg
            $sdgs = [
                ['numero' => 7, 'nom' => 'Énergie propre', 'icone' => '☀️', 'couleur' => '#FCC30B', 'mots_cles' => 'énergie, solaire, éolien, durable, renouvelable, vert'],
                ['numero' => 9, 'nom' => 'Industrie et Innovation', 'icone' => '🏭', 'couleur' => '#FD6925', 'mots_cles' => 'tech, innovation, ai, ia, digital, plateforme'],
                ['numero' => 11, 'nom' => 'Villes durables', 'icone' => '🏙️', 'couleur' => '#FD9D24', 'mots_cles' => 'ville, transport, urbain, mobilité, intelligent, connect'],
                ['numero' => 12, 'nom' => 'Consommation responsable', 'icone' => '♻️', 'couleur' => '#BF8B2E', 'mots_cles' => 'recyclage, circulaire, déchet, éco, bio, responsable'],
                ['numero' => 13, 'nom' => 'Lutte contre le changement climatique', 'icone' => '🌍', 'couleur' => '#3F7E44', 'mots_cles' => 'climat, co2, carbone, écologie, environnement']
            ];
        }
        
        $text = mb_strtolower($titre . ' ' . $description);
        $matched = [];

        foreach ($sdgs as $sdg) {
            $keywords = array_map('trim', explode(',', mb_strtolower($sdg['mots_cles'])));
            $matchCount = 0;
            $matchedKeywords = [];

            foreach ($keywords as $kw) {
                if (!empty($kw) && mb_strpos($text, $kw) !== false) {
                    $matchCount++;
                    $matchedKeywords[] = $kw;
                }
            }

            if ($matchCount >= 1) {
                $relevance = min(100, $matchCount * 25);
                $matched[] = [
                    'numero' => $sdg['numero'], 'nom' => $sdg['nom'],
                    'icone' => $sdg['icone'], 'couleur' => $sdg['couleur'],
                    'relevance' => $relevance, 'keywords_matched' => $matchedKeywords,
                    'description' => $sdg['description_fr'] ?? ''
                ];
            }
        }
        usort($matched, fn($a, $b) => $b['relevance'] <=> $a['relevance']);
        return $matched;
    }

    public function saveForProjet($id_projet, $sdgResults) {
        $tags = array_map(fn($s) => 'ODD' . $s['numero'], $sdgResults);
        $tagsStr = implode(',', $tags);
        $this->query("UPDATE projet SET sdg_tags = ? WHERE id_projet = ?", [$tagsStr, $id_projet]);
        return $tagsStr;
    }

    public function getAll() {
        try {
            return $this->query("SELECT * FROM sdg ORDER BY numero")->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getForProjet($id_projet) {
        try {
            $projet = $this->query("SELECT sdg_tags FROM projet WHERE id_projet = ?", [$id_projet])->fetch();
            if (!$projet || empty($projet['sdg_tags'])) return [];
            
            $tags = explode(',', $projet['sdg_tags']);
            $numeros = array_map(function($t) { return (int)str_replace('ODD', '', trim($t)); }, $tags);
            $numerosStr = implode(',', $numeros);
            
            return $this->query("SELECT * FROM sdg WHERE numero IN ($numerosStr)")->fetchAll();
        } catch (Throwable $e) {
            // Simulated fallback for demo if table missing
            return [
                ['numero' => 9, 'nom' => 'Industrie et Innovation', 'icone' => '🏭', 'couleur' => '#FD6925', 'description_fr' => 'Bâtir une infrastructure résiliente']
            ];
        }
    }
}
