<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../../config/ai_config.php';

class PitchDeckModel extends BaseModel {

    public function generate($projet) {
        $prompt = "Génère un pitch deck de 10 slides en JSON pour le projet: \"{$projet['titre']}\". Description: \"{$projet['description']}\".";
        $result = callAI($prompt);
        $slides = json_decode($result, true);
        if (!$slides || !is_array($slides)) {
            $slides = $this->defaultSlides($projet);
        }

        $this->query("INSERT INTO pitch_deck (id_projet, slides) VALUES (?, ?)",
            [$projet['id_projet'], json_encode($slides, JSON_UNESCAPED_UNICODE)]);

        return $slides;
    }

    private function defaultSlides($p) {
        $t = $p['titre'] ?? 'Projet';
        return [
            ['titre'=>$t,'contenu'=>"Solution innovante pour le marché tunisien"],
            ['titre'=>'Le Problème','contenu'=>"• Solutions actuelles coûteuses\n• Manque d'accessibilité\n• Perte de productivité 30%"],
            ['titre'=>'Notre Solution','contenu'=>"• Plateforme intelligente\n• Réduction coûts 40%\n• Support 24/7"],
            ['titre'=>'Marché','contenu'=>"• TAM: 50M TND\n• SAM: 15M TND\n• Croissance: 15%/an"],
            ['titre'=>'Business Model','contenu'=>"• SaaS mensuel\n• Basic: 99 TND\n• Pro: 299 TND"],
            ['titre'=>'Traction','contenu'=>"• 500+ beta users\n• 3 partenariats\n• Rétention: 85%"],
            ['titre'=>'Concurrence','contenu'=>"• Solution locale adaptée\n• Prix -50% vs international\n• Support arabe/français"],
            ['titre'=>'Équipe','contenu'=>"• CEO: 8 ans exp\n• CTO: Ingénieur senior\n• 5 développeurs"],
            ['titre'=>'Financier','contenu'=>"• Besoin: 150K TND\n• Break-even: 14 mois\n• CA Y3: 1.2M TND"],
            ['titre'=>'Call to Action','contenu'=>"Seed round: 200K TND\n\nDev: 40% | Marketing: 30%\nOps: 20% | Reserve: 10%"]
        ];
    }

    public function getByProjet($id_projet) {
        $pd = $this->query("SELECT * FROM pitch_deck WHERE id_projet = ? ORDER BY created_at DESC LIMIT 1", [$id_projet])->fetch();
        if ($pd) $pd['slides_data'] = json_decode($pd['slides'], true);
        return $pd;
    }

    public function generateHTML($slides, $projet) {
        $titre = htmlspecialchars($projet['titre'] ?? 'Pitch Deck');
        $colors = ['#1D9E75','#534AB7','#EF9F27','#3B82F6','#10B981','#8B5CF6','#F59E0B','#06B6D4','#E24B4A','#1D9E75'];
        $html = "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Pitch Deck - $titre</title>";
        $html .= "<style>
            *{margin:0;padding:0;box-sizing:border-box;}
            body{font-family:'Segoe UI',sans-serif;background:#111;}
            .slide{width:100%;min-height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px;page-break-after:always;color:white;text-align:center;}
            .slide h1{font-size:3em;margin-bottom:30px;text-shadow:2px 2px 10px rgba(0,0,0,.3);}
            .slide .content{font-size:1.4em;line-height:2;white-space:pre-line;max-width:800px;}
            .slide-num{position:absolute;bottom:30px;right:40px;font-size:.9em;opacity:.6;}
            .footer{font-size:.8em;opacity:.4;margin-top:40px;}
            @media print{.slide{height:100vh;}}
        </style></head><body>";

        foreach ($slides as $i => $slide) {
            $bg = $colors[$i % count($colors)];
            $num = $i + 1;
            $html .= "<div class='slide' style='background:linear-gradient(135deg,{$bg},{$bg}dd);position:relative;'>";
            $html .= "<h1>" . htmlspecialchars($slide['titre'] ?? "Slide $num") . "</h1>";
            $html .= "<div class='content'>" . nl2br(htmlspecialchars($slide['contenu'] ?? '')) . "</div>";
            $html .= "<div class='slide-num'>$num / " . count($slides) . "</div>";
            $html .= "<div class='footer'>$titre — ImpactVenture</div></div>";
        }
        $html .= "</body></html>";
        return $html;
    }
}
