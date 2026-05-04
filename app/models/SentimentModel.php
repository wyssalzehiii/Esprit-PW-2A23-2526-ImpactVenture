<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../../config/ai_config.php';

class SentimentModel extends BaseModel {

    public function analyze($text) {
        $prompt = "Analyse le sentiment et la qualité de ce pitch startup. Texte: \"$text\"";
        $result = callAI($prompt);
        $data = json_decode($result, true);
        if (!$data || !isset($data['sentiment'])) {
            $data = $this->localAnalysis($text);
        }
        return $data;
    }

    private function localAnalysis($text) {
        $t = mb_strtolower($text);
        $sentiment = 60; $clarte = 55; $pro = 55; $confiance = 55;

        $pos = ['innovant','solution','croissance','impact','durable','efficace','unique','leader','premier','intelligent','performant','optimiser','révolutionnaire','excellent','meilleur'];
        $neg = ['problème','difficulté','risque','cher','compliqué','manque','faible','lent','obsolète','échec'];
        $proW = ['marché','stratégie','roi','investissement','partenariat','scalable','b2b','b2c','kpi','saas','revenue','clients','croissance'];

        foreach ($pos as $w) if (mb_strpos($t,$w)!==false) { $sentiment+=5; $confiance+=3; }
        foreach ($neg as $w) if (mb_strpos($t,$w)!==false) { $sentiment-=3; }
        foreach ($proW as $w) if (mb_strpos($t,$w)!==false) { $pro+=5; $clarte+=3; }

        $wc = str_word_count($t);
        if ($wc > 50) $clarte += 10;
        if ($wc > 100) { $clarte += 10; $pro += 5; }

        $sentiment = min(98, max(15, $sentiment));
        $clarte = min(98, max(15, $clarte));
        $pro = min(98, max(15, $pro));
        $confiance = min(98, max(15, $confiance));
        $quality = round(($sentiment + $clarte + $pro + $confiance) / 4);

        return [
            'sentiment' => $sentiment,
            'sentiment_label' => $sentiment > 70 ? 'Positif' : ($sentiment > 45 ? 'Neutre' : 'Négatif'),
            'clarte' => $clarte, 'professionnalisme' => $pro, 'confiance' => $confiance,
            'pitch_quality' => $quality,
            'conseils' => [
                $sentiment < 60 ? "Ajoutez plus de termes positifs et d'enthousiasme." : "Bon ton positif !",
                $clarte < 60 ? "Développez davantage votre description." : "Description claire.",
                $pro < 60 ? "Utilisez des termes business (marché, ROI, stratégie)." : "Vocabulaire professionnel adapté.",
                "Ajoutez un call-to-action clair.", "Quantifiez votre impact avec des chiffres."
            ]
        ];
    }
}
