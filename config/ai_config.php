<?php
/**
 * Configuration API IA pour ImpactVenture
 * Mode: 'api' (OpenAI réel) ou 'simulation' (fallback local)
 */
define('AI_MODE', 'simulation'); // Changer en 'api' si clé OpenAI disponible
define('OPENAI_API_KEY', ''); // Mettre votre clé OpenAI ici
define('OPENAI_MODEL', 'gpt-3.5-turbo');
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');

/**
 * Appel API OpenAI (ou simulation)
 */
function callAI($prompt, $systemPrompt = "Tu es un assistant expert en entrepreneuriat et startups en Tunisie.") {
    if (AI_MODE === 'api' && !empty(OPENAI_API_KEY)) {
        return callOpenAI($prompt, $systemPrompt);
    }
    return simulateAI($prompt);
}

function callOpenAI($prompt, $systemPrompt) {
    $data = [
        'model' => OPENAI_MODEL,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.7,
        'max_tokens' => 2000
    ];

    $ch = curl_init(OPENAI_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY
        ]
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['choices'][0]['message']['content'] ?? 'Erreur API';
}

/**
 * Simulation IA locale (fallback intelligent)
 */
function simulateAI($prompt) {
    $prompt_lower = mb_strtolower($prompt);
    
    // Business Plan
    if (strpos($prompt_lower, 'business plan') !== false || strpos($prompt_lower, 'plan d\'affaires') !== false) {
        return generateSimulatedBusinessPlan($prompt);
    }
    
    // Pitch Deck
    if (strpos($prompt_lower, 'pitch deck') !== false || strpos($prompt_lower, 'slides') !== false) {
        return generateSimulatedPitchDeck($prompt);
    }
    
    // Sentiment / Pitch analysis
    if (strpos($prompt_lower, 'sentiment') !== false || strpos($prompt_lower, 'analyse') !== false) {
        return generateSimulatedSentiment($prompt);
    }
    
    // Chatbot
    return generateSimulatedChatResponse($prompt);
}

function generateSimulatedBusinessPlan($prompt) {
    // Extraire le titre du projet du prompt
    preg_match('/projet[:\s]*"?([^".\n]+)/i', $prompt, $matches);
    $titre = $matches[1] ?? 'Projet Innovant';
    
    return json_encode([
        'resume_executif' => "Ce business plan présente \"$titre\", un projet innovant qui répond à un besoin concret du marché tunisien. Notre solution combine technologie et impact social pour créer de la valeur durable.",
        'probleme' => "Le marché tunisien fait face à des défis majeurs dans ce secteur : inefficacité des solutions existantes, coûts élevés, et manque d'accessibilité pour les PME et les consommateurs.",
        'solution' => "Notre approche innovante utilise les dernières technologies pour offrir une solution accessible, efficace et écologique. Nous proposons une plateforme qui simplifie les processus et réduit les coûts de 40%.",
        'marche' => "Le marché adressable est estimé à 50M TND en Tunisie, avec une croissance annuelle de 15%. Notre segment cible représente 12M TND avec 2000+ clients potentiels identifiés.",
        'modele_economique' => "Modèle SaaS avec abonnement mensuel (Basic: 99 TND/mois, Pro: 299 TND/mois, Enterprise: sur devis). Revenus complémentaires via commissions et services premium.",
        'swot' => [
            'forces' => ['Équipe technique qualifiée', 'Solution innovante et différenciée', 'Premier arrivé sur le marché local'],
            'faiblesses' => ['Ressources financières limitées', 'Marque peu connue', 'Dépendance technologique'],
            'opportunites' => ['Marché en forte croissance', 'Soutien gouvernemental aux startups', 'Digitalisation accélérée post-COVID'],
            'menaces' => ['Concurrence internationale', 'Réglementation changeante', 'Instabilité économique']
        ],
        'plan_financier' => [
            'investissement_initial' => '150,000 TND',
            'ca_annee_1' => '200,000 TND',
            'ca_annee_2' => '500,000 TND',
            'ca_annee_3' => '1,200,000 TND',
            'point_equilibre' => '14 mois',
            'roi_3ans' => '320%'
        ],
        'equipe' => "Fondateurs avec 10+ ans d'expérience cumulée dans le secteur tech et entrepreneuriat. Équipe de 5 personnes couvrant tech, business et marketing.",
        'timeline' => [
            'T1 2025' => 'MVP et tests utilisateurs',
            'T2 2025' => 'Lancement beta et premiers clients',
            'T3 2025' => 'Levée seed et expansion',
            'T4 2025' => 'Scaling et partenariats stratégiques'
        ]
    ], JSON_UNESCAPED_UNICODE);
}

function generateSimulatedPitchDeck($prompt) {
    preg_match('/projet[:\s]*"?([^".\n]+)/i', $prompt, $matches);
    $titre = $matches[1] ?? 'Projet Innovant';
    
    return json_encode([
        ['titre' => $titre, 'contenu' => "Solution innovante pour le marché tunisien\n\nTransformer les défis en opportunités"],
        ['titre' => 'Le Problème', 'contenu' => "• Les solutions actuelles sont coûteuses et inefficaces\n• 70% des PME n'ont pas accès aux outils modernes\n• Perte de productivité estimée à 30%"],
        ['titre' => 'Notre Solution', 'contenu' => "• Plateforme intelligente et accessible\n• Réduction des coûts de 40%\n• Interface intuitive et support 24/7"],
        ['titre' => 'Marché Cible', 'contenu' => "• TAM: 50M TND\n• SAM: 15M TND\n• SOM: 5M TND (Année 1)\n• Croissance: 15% par an"],
        ['titre' => 'Modèle Économique', 'contenu' => "• SaaS - Abonnement mensuel\n• Basic: 99 TND/mois\n• Pro: 299 TND/mois\n• Commission: 2-5% par transaction"],
        ['titre' => 'Traction', 'contenu' => "• 500+ utilisateurs beta\n• 3 partenariats stratégiques\n• Taux de rétention: 85%\n• NPS Score: 72"],
        ['titre' => 'Concurrence', 'contenu' => "• Avantage compétitif: solution locale adaptée\n• Prix 50% inférieur aux concurrents internationaux\n• Support en arabe et français"],
        ['titre' => 'Équipe', 'contenu' => "• CEO: Expert business, 8 ans d'expérience\n• CTO: Ingénieur senior, ex-SSII\n• CMO: Marketing digital, 5 ans\n• 5 développeurs full-stack"],
        ['titre' => 'Plan Financier', 'contenu' => "• Investissement initial: 150K TND\n• Break-even: 14 mois\n• CA Année 1: 200K TND\n• CA Année 3: 1.2M TND"],
        ['titre' => 'Call to Action', 'contenu' => "Nous cherchons 200,000 TND en seed\n\n• Développement produit: 40%\n• Marketing & Growth: 30%\n• Opérations: 20%\n• Reserve: 10%\n\n📧 contact@projet.tn"]
    ], JSON_UNESCAPED_UNICODE);
}

function generateSimulatedSentiment($prompt) {
    // Analyse basique du texte
    $text = mb_strtolower($prompt);
    $score_sentiment = 65;
    $score_clarte = 60;
    $score_pro = 60;
    $score_confiance = 60;
    
    $positive = ['innovant','solution','croissance','impact','durable','efficace','unique','leader','premier','révolutionnaire','intelligent','performant','optimiser'];
    $negative = ['problème','difficulté','risque','cher','compliqué','manque','faible','lent','obsolète'];
    $pro_words = ['marché','stratégie','ROI','investissement','partenariat','scalable','B2B','B2C','KPI','metrics','pivot'];
    
    foreach ($positive as $w) { if (strpos($text, $w) !== false) { $score_sentiment += 5; $score_confiance += 3; } }
    foreach ($negative as $w) { if (strpos($text, $w) !== false) { $score_sentiment -= 3; } }
    foreach ($pro_words as $w) { if (stripos($text, $w) !== false) { $score_pro += 5; $score_clarte += 3; } }
    
    $word_count = str_word_count($text);
    if ($word_count > 50) $score_clarte += 10;
    if ($word_count > 100) $score_clarte += 10;
    if ($word_count > 200) $score_pro += 10;
    
    return json_encode([
        'sentiment' => min(98, max(20, $score_sentiment)),
        'sentiment_label' => $score_sentiment > 70 ? 'Positif' : ($score_sentiment > 45 ? 'Neutre' : 'Négatif'),
        'clarte' => min(98, max(20, $score_clarte)),
        'professionnalisme' => min(98, max(20, $score_pro)),
        'confiance' => min(98, max(20, $score_confiance)),
        'pitch_quality' => min(98, max(20, round(($score_sentiment + $score_clarte + $score_pro + $score_confiance) / 4))),
        'conseils' => [
            $score_sentiment < 60 ? "Ajoutez plus de termes positifs et d'enthousiasme dans votre pitch." : "Bon ton positif, continuez ainsi !",
            $score_clarte < 60 ? "Développez davantage votre description pour plus de clarté." : "Description claire et bien structurée.",
            $score_pro < 60 ? "Utilisez des termes business (marché, ROI, stratégie) pour renforcer le professionnalisme." : "Vocabulaire professionnel adapté.",
            "Ajoutez un call-to-action clair à la fin de votre pitch.",
            "Quantifiez votre impact avec des chiffres concrets."
        ]
    ], JSON_UNESCAPED_UNICODE);
}

function generateSimulatedChatResponse($prompt) {
    $prompt_lower = mb_strtolower($prompt);
    
    $responses = [
        'pitch' => "Pour améliorer votre pitch, voici mes conseils :\n\n1. **Commencez par le problème** : Décrivez clairement le problème que vous résolvez\n2. **Votre solution unique** : Qu'est-ce qui vous différencie ?\n3. **Le marché** : Quantifiez l'opportunité (TAM, SAM, SOM)\n4. **Traction** : Montrez vos résultats concrets\n5. **L'équipe** : Pourquoi vous êtes les bonnes personnes\n6. **L'ask** : Combien vous levez et pour quoi\n\n💡 Astuce : Un bon pitch tient en 60 secondes !",
        
        'marketing' => "Voici une stratégie marketing adaptée aux startups tunisiennes :\n\n1. **Réseaux sociaux** : Facebook et Instagram sont dominants en Tunisie\n2. **Content Marketing** : Blog + vidéos en arabe/français\n3. **Growth Hacking** : Programme de referral avec bonus\n4. **Partenariats** : Technopoles, incubateurs (BIAT Labs, Flat6Labs)\n5. **Events** : Participez aux Startup Weekends et hackathons\n6. **PR** : Relations avec les médias tech locaux\n\n💡 Budget recommandé : 15-20% de votre CA prévu.",
        
        'fonds' => "Pour lever des fonds en Tunisie, voici les étapes :\n\n1. **Pre-seed** (10-50K TND) : FFF, concours startup\n2. **Seed** (50-200K TND) : Business Angels (TBAC), Smart Capital\n3. **Série A** (200K-2M TND) : VC locaux et régionaux\n\n📋 Documents nécessaires :\n- Pitch deck (10 slides)\n- Business plan détaillé\n- Projections financières 3 ans\n- MVP fonctionnel\n\n🏢 Organismes utiles : Startup Act Tunisie, CDC, BPIFRANCE Tunisie",
        
        'default' => "Excellent question ! En tant que coach entrepreneur IA, voici mon analyse :\n\n1. **Validez votre idée** : Parlez à 50 clients potentiels minimum\n2. **Construisez un MVP** : Version minimale mais fonctionnelle\n3. **Mesurez** : Définissez vos KPIs dès le départ\n4. **Itérez** : Pivotez si nécessaire, c'est normal\n5. **Réseautez** : Rejoignez les communautés startup en Tunisie\n\n🎯 Prochaine étape : Définissez votre proposition de valeur unique en une phrase.\n\nN'hésitez pas à me poser une question plus spécifique !"
    ];
    
    if (strpos($prompt_lower, 'pitch') !== false) return $responses['pitch'];
    if (strpos($prompt_lower, 'marketing') !== false || strpos($prompt_lower, 'promouvoir') !== false) return $responses['marketing'];
    if (strpos($prompt_lower, 'fonds') !== false || strpos($prompt_lower, 'lever') !== false || strpos($prompt_lower, 'investiss') !== false) return $responses['fonds'];
    
    return $responses['default'];
}
