<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/scoring_helpers.php';
require_once __DIR__ . '/../models/FicheEntreprise.php';
require_once __DIR__ . '/../models/projet.php';
require_once __DIR__ . '/../../config/ai_config.php';

class ProjetController {
    private $pdo;
    private static $advancedTablesReady = false;
    public function __construct() { $this->pdo = config::getConnexion(); }

    private function ensureAdvancedTables(): void
    {
        if (self::$advancedTablesReady) {
            return;
        }
        self::$advancedTablesReady = true;
        try {
            $this->pdo->exec(
                'CREATE TABLE IF NOT EXISTS project_scores (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    id_projet INT NOT NULL,
                    final_score FLOAT NOT NULL,
                    delta_pts FLOAT DEFAULT 0,
                    change_reason VARCHAR(255) DEFAULT NULL,
                    market_score FLOAT DEFAULT NULL,
                    innovation_score FLOAT DEFAULT NULL,
                    sustainability_score FLOAT DEFAULT NULL,
                    risk_score FLOAT DEFAULT NULL,
                    snapshot_json JSON DEFAULT NULL,
                    created_at DATETIME DEFAULT NOW(),
                    KEY idx_proj_created (id_projet, created_at),
                    CONSTRAINT fk_iv_proj_scores FOREIGN KEY (id_projet) REFERENCES projet(id_projet) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );
        } catch (Exception $e) { /* table may exist under other FK name */ }

        try {
            $this->pdo->exec(
                'CREATE TABLE IF NOT EXISTS mentor_interests (
                    mentor_slug VARCHAR(128) NOT NULL,
                    projet_id INT NOT NULL,
                    is_interested TINYINT(1) NOT NULL DEFAULT 0,
                    noted_at DATETIME DEFAULT NULL,
                    PRIMARY KEY (mentor_slug, projet_id),
                    KEY idx_mi_projet_interest (projet_id, is_interested),
                    CONSTRAINT fk_iv_mi_projet FOREIGN KEY (projet_id) REFERENCES projet(id_projet) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );
        } catch (Exception $e) { /* ignore */ }
    }

    // ─── FRONT CRUD ──────────────────────────────────────────────────
    public function index() {
        $projets = $this->getAllProjets();
        require_once __DIR__ . '/../views/front/Projet/list.php';
    }

    // Alias for index() — fixes "Call to undefined method ProjetController::list()"
    public function list() {
        $this->index();
    }
    public function listAdvanced() {
        $this->ensureAdvancedTables();
        $projets = $this->getAllProjets();
        
        // Pass projects to the advanced view
        require_once __DIR__ . '/../views/front/Projet/list_advanced.php';
    }
    public function create() {
        $fiches=$this->getAllFiches(); $errors=[]; require_once __DIR__ . '/../views/front/Projet/create_journey.php';
    }
    public function store() {
        $errors = $this->valider($_POST);
        if (!empty($errors)) { $fiches = $this->getAllFiches(); require_once __DIR__ . '/../views/front/Projet/create_journey.php'; return; }
        $projet = new Projet(isset($_POST['id_fiche_entreprise']) ? $_POST['id_fiche_entreprise'] : null,isset($_POST['titre']) ? $_POST['titre'] : '',isset($_POST['description']) ? $_POST['description'] : '',isset($_POST['budget_demande']) ? $_POST['budget_demande'] : 0,'soumis');
        $id = $this->addProjet($projet);
        $this->ensureProjetEnriched((int) $id);
        $this->reconcileProjetScores((int) $id);
        header('Location: index.php?action=projet_show&id='.$id.'&msg=created_with_ai'); exit;
    }
    public function edit($id) {
        $projet = $this->getProjetById($id);
        if (!$projet) { header('Location: index.php?action=projet_list&msg=notfound'); exit; }
        $fiches = $this->getAllFiches(); $errors = [];
        require_once __DIR__ . '/../views/front/Projet/edit.php';
    }
    public function update($id) {
        $errors = $this->valider($_POST);
        if (!empty($errors)) { $projet=$this->getProjetById($id); $fiches=$this->getAllFiches(); require_once __DIR__ . '/../views/front/Projet/edit.php'; return; }
        $projet = new Projet(isset($_POST['id_fiche_entreprise']) ? $_POST['id_fiche_entreprise'] : null,isset($_POST['titre']) ? $_POST['titre'] : '',isset($_POST['description']) ? $_POST['description'] : '',isset($_POST['budget_demande']) ? $_POST['budget_demande'] : 0,isset($_POST['statut']) ? $_POST['statut'] : 'soumis');
        $projet->setIdProjet($id);
        $this->updateProjet($projet);
        $this->ensureProjetEnriched((int) $id);
        $this->reconcileProjetScores((int) $id);
        header('Location: index.php?action=projet_list&msg=updated'); exit;
    }
    public function delete($id) { $this->deleteProjet($id); header('Location: index.php?action=projet_list&msg=deleted'); exit; }
    public function show($id) {
        if (!$id) { header('Location: ' . iv_url(['action' => 'projet_list'])); exit; }
        $this->ensureProjetEnriched((int) $id);
        $projet = $this->getProjetById($id);
        if (!$projet) { header('Location: ' . iv_url(['action' => 'projet_list'])); exit; }

        $rawSdg = $this->performSDGAnalysis(isset($projet['description']) ? $projet['description'] : '', isset($projet['titre']) ? $projet['titre'] : '');
        $sdgs = $this->decorateSdgsForView($rawSdg);

        $this->ensureAdvancedTables();
        $this->reconcileProjetScores((int) $id);
        $projet = $this->getProjetById($id);

        $mentors = $this->decodeJsonList(isset($projet['mentor_suggestions']) ? $projet['mentor_suggestions'] : null);
        $investors = $this->decodeJsonList(isset($projet['investor_suggestions']) ? $projet['investor_suggestions'] : null);

        $sentimentData = !empty($projet['sentiment_data']) ? json_decode($projet['sentiment_data'], true) : null;
        $pitchScore = (int) round((float) (isset($projet['pitch_score']) ? $projet['pitch_score'] : 0));
        $scoreGreen = (int) (isset($projet['score_green']) ? $projet['score_green'] : 0);
        $viabilityScore = (int) round((float) (isset($projet['viability_score']) ? $projet['viability_score'] : 0));
        $blueprint = $this->composeScoreBlueprint($projet);
        $nextStepRecs = iv_build_next_step_recommendations(
            $blueprint['market_score'],
            $blueprint['innovation_score'],
            $blueprint['sustainability_score'],
            $blueprint['risk_score']
        );
        $scoreHistory = $this->getScoreTimeline($id);
        $timeline = array_slice(array_reverse($scoreHistory), 0, 8);
        $mentorInterestedCount = $this->countMentorInterested((int) $id);
        require_once __DIR__ . '/../views/front/Projet/show_tabbed.php';
    }

    // ─── BACK OFFICE ─────────────────────────────────────────────────
    public function adminIndex() { $projets=$this->getAllProjets(); $stats=$this->getStats(); require_once __DIR__ . '/../views/back/projet/list.php'; }
    public function adminEdit($id) { $projet=$this->getProjetById($id); $fiches=$this->getAllFiches(); $errors=[]; require_once __DIR__ . '/../views/back/projet/edit.php'; }
    public function adminUpdate($id) { $this->update($id); }
    public function adminDelete($id) { $this->deleteProjet($id); header('Location: index.php?action=admin_projet&msg=deleted'); exit; }
    
    public function adminExport($format = 'csv') {
        $projets = $this->getAllProjets();
        
        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="projets_export.csv"');
            
            // Add UTF-8 BOM to fix French character encoding
            echo "\xEF\xBB\xBF";
            
            $output = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($output, ['ID', 'Titre', 'Entreprise', 'Categorie', 'Statut', 'Score IA', 'Score Green', 'Date Soumission', 'Description']);
            
            // CSV Data - Remove duplicates and clean description
            $seenIds = [];
            foreach ($projets as $projet) {
                // Skip duplicates
                if (in_array($projet['id_projet'], $seenIds)) {
                    continue;
                }
                $seenIds[] = $projet['id_projet'];
                
                // Clean description - remove repeated words and limit length
                $description = isset($projet['description']) ? $projet['description'] : '';
                $description = strip_tags($description);
                $description = preg_replace('/\b(\w+)\s+\1\b/i', '$1', $description); // Remove repeated words
                $description = substr($description, 0, 200);
                
                fputcsv($output, [
                    $projet['id_projet'],
                    $projet['titre'],
                    isset($projet['entreprise_nom']) ? $projet['entreprise_nom'] : 'N/A',
                    isset($projet['entreprise_categorie']) ? $projet['entreprise_categorie'] : 'N/A',
                    $projet['statut'],
                    round(isset($projet['score_ia']) ? $projet['score_ia'] : 0),
                    round(isset($projet['score_green']) ? $projet['score_green'] : 0),
                    !empty($projet['date_soumission']) ? date('d/m/Y', strtotime($projet['date_soumission'])) : 'N/A',
                    $description
                ]);
            }
            
            fclose($output);
            exit;
        } 
    }
    public function trending() {
        $fiches=$this->getAllFiches();
        $total=$this->pdo->query("SELECT COUNT(*) FROM projet")->fetchColumn();
        $stats=$this->getStats();
        require_once __DIR__ . '/../views/back/projet/trending.php';
    }

    /** Dashboard IA / avancé — navigation ne renvoie plus vers fiche_list. */
    public function advancedDashboard() {
        $projets = $this->getAllProjets();
        $fiches = $this->getAllFiches();
        require_once __DIR__ . '/../views/front/Advanced/dashboard.php';
    }

    // ─── CRUD SQL ─────────────────────────────────────────────────────
    public function getAllFiches() {
        return $this->pdo->query("SELECT f.*, COUNT(p.id_projet) as nb_projets FROM fiche_entreprise f LEFT JOIN projet p ON p.id_fiche_entreprise=f.id GROUP BY f.id ORDER BY f.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllProjets() {
        $this->ensureAdvancedTables();
        return $this->pdo->query("
            SELECT p.*, f.nom as entreprise_nom, f.categorie as entreprise_categorie, f.score_green, f.mots_cles,
                   ps.innovation_score, ps.sustainability_score, ps.market_score, ps.risk_score, 
                   ps.final_score, ps.change_reason
            FROM projet p 
            LEFT JOIN fiche_entreprise f ON p.id_fiche_entreprise=f.id 
            LEFT JOIN project_scores ps ON ps.id_projet = p.id_projet
            ORDER BY p.date_soumission DESC, p.id_projet DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProjetById($id) {
        $stmt=$this->pdo->prepare("SELECT p.*, f.nom as entreprise_nom, f.categorie as entreprise_categorie, f.score_green, f.mots_cles, f.ville FROM projet p LEFT JOIN fiche_entreprise f ON p.id_fiche_entreprise=f.id WHERE p.id_projet=:id");
        $stmt->execute([':id'=>$id]); return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addProjet(Projet $p) {
        $stmt=$this->pdo->prepare("INSERT INTO projet (id_fiche_entreprise,titre,description,budget_demande,statut) VALUES (:fiche,:titre,:desc,:budget,:statut)");
        $stmt->execute([':fiche'=>$p->getIdFicheEntreprise(),':titre'=>$p->getTitre(),':desc'=>$p->getDescription(),':budget'=>$p->getBudgetDemande(),':statut'=>$p->getStatut()]);
        return $this->pdo->lastInsertId();
    }
    public function updateProjet(Projet $p) {
        $stmt=$this->pdo->prepare("UPDATE projet SET id_fiche_entreprise=:fiche,titre=:titre,description=:desc,budget_demande=:budget,statut=:statut WHERE id_projet=:id");
        $stmt->execute([':fiche'=>$p->getIdFicheEntreprise(),':titre'=>$p->getTitre(),':desc'=>$p->getDescription(),':budget'=>$p->getBudgetDemande(),':statut'=>$p->getStatut(),':id'=>$p->getIdProjet()]);
    }
    public function deleteProjet($id) { $this->pdo->prepare("DELETE FROM projet WHERE id_projet=:id")->execute([':id'=>$id]); }
    public function getStats() {
        $res=$this->pdo->query("SELECT statut, COUNT(*) as count FROM projet GROUP BY statut")->fetchAll(PDO::FETCH_ASSOC);
        // Clés alignées sur module2.sql (ENUM en minuscules / accentué)
        $stats=['soumis'=>0,'en_evaluation'=>0,'accepté'=>0,'rejeté'=>0];
        foreach($res as $r) {
            if (isset($stats[$r['statut']])) {
                $stats[$r['statut']] = (int) $r['count'];
            }
        }
        return $stats;
    }
    public function analyzeAjax() {
        header('Content-Type: application/json');
        $a=$this->performSentimentAnalysis(isset($_POST['description']) ? $_POST['description'] : '');
        $s=$this->performSDGAnalysis(isset($_POST['description']) ? $_POST['description'] : '',isset($_POST['titre']) ? $_POST['titre'] : '');
        echo json_encode(['pitch_score'=>isset($a['pitch_quality']) ? $a['pitch_quality'] : 0,'sdgs'=>array_map(fn($x)=>$x['nom'],$s),'viability'=>min(100,strlen(isset($_POST['description']) ? $_POST['description'] : '')>50?85:60)]);
        exit;
    }
    public function co2Simulator() {
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $r=(floatval(isset($_POST['transport']) ? $_POST['transport'] : 0)*0.192)+(floatval(isset($_POST['energie']) ? $_POST['energie'] : 0)*0.475)+(floatval(isset($_POST['dechets']) ? $_POST['dechets'] : 0)*2.5);
            $result=['total'=>round($r,1)];
        }
        $history=[];
        require_once __DIR__ . '/../views/front/CO2/simulator.php';
    }

    // ─── SENTIMENT ────────────────────────────────────────────────────
    private function performSentimentAnalysis($text) {
        $lc = mb_strtolower($text, 'UTF-8');
        $adj = iv_text_scoring_adjustments($text);
        $posHits = iv_keyword_bonus_once_each($lc, iv_positive_keyword_set());
        $negHits = iv_keyword_bonus_once_each($lc, iv_negative_keyword_set());
        $base = 50 + $posHits * 10 - $negHits * 5 + $adj['length_penalty'] + $adj['structure_bonus'] + $adj['diversity_bonus'];
        $score = (int) min(100, max(0, $base));
        $words = iv_word_tokens_fr($text);
        $clarte = min(98, max(12, round(38 + ($adj['unique_ratio'] * 42) + (min(220, iv_word_count_fr($text)) / 220) * 28)));
        $proHits = iv_keyword_bonus_once_each($lc, ['marché', 'marche', 'stratégie', 'strategie', 'roi', 'b2b', 'b2c', 'kpi', 'saas', 'revenu', 'client', 'utilisateur']);
        $prof = min(98, max(14, round(42 + $proHits * 7)));
        $conf = min(98, max(14, round(40 + $posHits * 8 - $negHits * 4)));
        $pitch_quality = (int) min(100, max(8, round($score * 0.52 + $clarte * 0.21 + $prof * 0.15 + $conf * 0.12)));

        return [
            'score' => $score,
            'label' => $score >= 70 ? 'Positif 🟢' : ($score <= 40 ? 'Négatif 🔴' : 'Neutre 🟡'),
            'pitch_quality' => $pitch_quality,
            'sentiment' => min(98, max(12, round(35 + ($posHits * 14) - ($negHits * 7)))),
            'sentiment_label' => ($score >= 70 ? 'Positif' : ($score <= 40 ? 'Négatif' : 'Neutre')),
            'clarte' => $clarte,
            'professionnalisme' => $prof,
            'confiance' => $conf,
            'clarity' => $clarte,
            'professionalism' => $prof,
            'confidence' => $conf,
            'advice' => $adj['words'] < 30
                ? 'Allongez et structurez votre texte : problème, solution, marché — au moins 30 mots utiles.'
                : ($adj['unique_ratio'] < 0.5
                    ? 'Évitez de répéter les mêmes mots pour « gonfler » le score ; développez davantage vos idées.'
                    : ($adj['structure_bonus'] > 0
                        ? 'Bonne structure (problème / solution / marché). Renforcez les chiffres et preuves concrètes.'
                        : 'Ajoutez en une phrase chacune : quel problème, quelle solution, pour quel client précisément.')),
            'conseils' => array_values(array_filter([
                $adj['words'] < 30 ? 'Ajoutez au minimum 30 mots explicites (besoin réel + solution). ' : '',
                $adj['unique_ratio'] <= 0.55 ? 'Diversifiez le vocabulaire : moins de répétitions, plus de précision métier.' : '',
                $adj['structure_bonus'] <= 0 ? 'Formulez clairement le problème, la solution et le marché cible dans la description.' : '',
                'Ajoutez 2–3 chiffres ou preuves (taille marché tranche, prix indicatif, early adopters).'
            ], static fn ($x) => $x !== '')),
        ];
    }
    private function analyzeAndSaveSentiment($id,$desc) {
        $a=$this->performSentimentAnalysis($desc);
        $this->pdo->prepare("UPDATE projet SET pitch_score=?,sentiment_data=? WHERE id_projet=?")->execute([isset($a['pitch_quality']) ? $a['pitch_quality'] : 0,json_encode($a),$id]);
    }
    public function sentiment() {
        $projets=$this->getAllProjets(); $analysis=null; $selectedProjet=null;
        if (isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            if ($selectedProjet) {
                $analysis=$this->performSentimentAnalysis($selectedProjet['description']);
                $this->pdo->prepare("UPDATE projet SET pitch_score=?,sentiment_data=? WHERE id_projet=?")->execute([isset($analysis['pitch_quality']) ? $analysis['pitch_quality'] : 0,json_encode($analysis),$selectedProjet['id_projet']]);
                $this->reconcileProjetScores((int) $selectedProjet['id_projet']);
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/sentiment.php';
    }

    // ─── SDG ──────────────────────────────────────────────────────────
    private function performSDGAnalysis($desc,$titre) {
        $text=strtolower($titre.' '.$desc);
        $sdgs=[
            ['id'=>1,'nom'=>'Pas de pauvreté','kw'=>['pauvreté','social','inclusion']],
            ['id'=>2,'nom'=>'Faim zéro','kw'=>['faim','agriculture','alimentation']],
            ['id'=>3,'nom'=>'Bonne santé','kw'=>['santé','médical','soins','bien-être']],
            ['id'=>4,'nom'=>'Éducation','kw'=>['éducation','école','formation','apprentissage']],
            ['id'=>5,'nom'=>'Égalité','kw'=>['égalité','femmes','genre','droits']],
            ['id'=>6,'nom'=>'Eau propre','kw'=>['eau','assainissement','potable']],
            ['id'=>7,'nom'=>'Énergie propre','kw'=>['énergie','solaire','renouvelable','éolien']],
            ['id'=>8,'nom'=>'Travail décent','kw'=>['emploi','travail','économie','croissance']],
            ['id'=>9,'nom'=>'Industrie et Innovation','kw'=>['innovation','industrie','technologie','digital','logiciel','app','paiement','fintech','plateforme','iot']],
            ['id'=>11,'nom'=>'Villes durables','kw'=>['livraison','logistique','urbain','transport','mobilité','vélo']],
            ['id'=>13,'nom'=>'Climat','kw'=>['climat','réchauffement','co2','carbone']],
        ];
        $matches=[];
        foreach ($sdgs as $sdg) {
            $score = 0;
            foreach ($sdg['kw'] as $kw) {
                if (strpos($text, $kw) !== false) {
                    $score += 21;
                }
            }
            if ($score > 0) {
                $matches[] = ['id' => $sdg['id'], 'nom' => $sdg['nom'], 'score' => min(95, 43 + min(72, $score))];
            }
        }
        usort($matches,fn($a,$b)=>$b['score']<=>$a['score']);
        return array_slice($matches,0,3);
    }
    private function analyzeAndSaveSDG($id,$desc,$titre) {
        $results = $this->performSDGAnalysis($desc, $titre);
        $tags = array_map(fn($s) => 'ODD' . $s['id'], $results);
        try {
            $this->pdo->prepare("UPDATE projet SET sdg_tags=? WHERE id_projet=?")->execute([implode(',', $tags), $id]);
        } catch (Exception $e) { /* ignore */ }
    }
    private function getSDGForProjet($id) {
        try {
            $stmt=$this->pdo->prepare("SELECT s.*,ps.confidence_score FROM projet_sdg ps JOIN sdg s ON ps.sdg_id=s.id WHERE ps.id_projet=:id");
            $stmt->execute([':id'=>$id]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) { return []; }
    }
    public function sdg() {
        $projets=$this->getAllProjets(); $sdgResults=[]; $selectedProjet=null; $allSdgs=[];
        try { $allSdgs=$this->pdo->query("SELECT * FROM sdg ORDER BY numero")->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){}
        if (isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            if ($selectedProjet) {
                $sdgResults=$this->performSDGAnalysis($selectedProjet['description'],$selectedProjet['titre']);
                $tags=array_map(fn($s)=>'ODD'.$s['id'],$sdgResults);
                try { $this->pdo->prepare("UPDATE projet SET sdg_tags=? WHERE id_projet=?")->execute([implode(',',$tags),$selectedProjet['id_projet']]); } catch(Exception $e){}
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/sdg.php';
    }

    // ─── MATCHING ─────────────────────────────────────────────────────
    private function findMentors($projet) {
        try { $mentors=$this->pdo->query("SELECT * FROM mentor WHERE disponible=1")->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){ return []; }
        $scores=[]; $cat=strtolower(isset($projet['entreprise_categorie']) ? $projet['entreprise_categorie'] : ''); $desc=strtolower((isset($projet['titre']) ? $projet['titre'] : '').' '.(isset($projet['description']) ? $projet['description'] : ''));
        foreach($mentors as $m) {
            $score=0;
            foreach(array_map('trim',explode(',',strtolower(isset($m['secteurs']) ? $m['secteurs'] : ''))) as $s) if(!empty($s)&&strpos($cat,$s)!==false) { $score+=30; break; }
            foreach(array_map('trim',explode(',',strtolower(isset($m['mots_cles']) ? $m['mots_cles'] : ''))) as $kw) if(!empty($kw)&&strpos($desc,$kw)!==false) $score+=15;
            $score+=min(20,(isset($m['experience_annees']) ? $m['experience_annees'] : 0)*2);
            $scores[]=array_merge($m,['match_score'=>min(100,$score)]);
        }
        usort($scores,fn($a,$b)=>$b['match_score']<=>$a['match_score']); return array_slice($scores,0,5);
    }
    private function findInvestors($projet) {
        try { $investors=$this->pdo->query("SELECT * FROM investisseur WHERE actif=1")->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){ return []; }
        $scores=[]; $cat=strtolower(isset($projet['entreprise_categorie']) ? $projet['entreprise_categorie'] : ''); $budget=floatval(isset($projet['budget_demande']) ? $projet['budget_demande'] : 0);
        foreach($investors as $inv) {
            $score=0;
            foreach(array_map('trim',explode(',',strtolower(isset($inv['secteurs']) ? $inv['secteurs'] : ''))) as $s) if(!empty($s)&&strpos($cat,$s)!==false) { $score+=25; break; }
            if($budget>0&&$budget<=floatval(isset($inv['budget_max']) ? $inv['budget_max'] : 0)) $score+=30;
            $scores[]=array_merge($inv,['match_score'=>min(100,$score)]);
        }
        usort($scores,fn($a,$b)=>$b['match_score']<=>$a['match_score']); return array_slice($scores,0,5);
    }
    public function matching() {
        $projets=$this->getAllProjets(); $mentors=[]; $investors=[]; $selectedProjet=null;
        if (isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            if ($selectedProjet) { $mentors=$this->findMentors($selectedProjet); $investors=$this->findInvestors($selectedProjet); }
        }
        require_once __DIR__ . '/../views/front/Advanced/matching.php';
    }

    // ─── VIABILITY ────────────────────────────────────────────────────
    public function viability() {
        $projets=$this->getAllProjets(); $result=null; $selectedProjet=null;
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $selectedProjet=$this->getProjetById((int)$_POST['id_projet']);
            $result=$this->calculateViability($_POST, true);
            if ($selectedProjet) {
                $this->reconcileProjetScores((int) $selectedProjet['id_projet']);
            }
        } elseif (isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
        }
        require_once __DIR__ . '/../views/front/Advanced/viability.php';
    }
    private function viabilityBreakdown(array $data): array
    {
        $mS=['grand'=>90,'moyen'=>65,'petit'=>40,'niche'=>55];
        $cS=['faible'=>85,'moyen'=>60,'fort'=>35];
        $scores=[];
        $marcheKey = strtolower(isset($data['taille_marche']) ? $data['taille_marche'] : 'moyen');
        $concurrenceKey = strtolower(isset($data['niveau_concurrence']) ? $data['niveau_concurrence'] : 'moyen');
        $scores['marche']=(isset($mS[$marcheKey]) ? $mS[$marcheKey] : 60)*0.6+(isset($cS[$concurrenceKey]) ? $cS[$concurrenceKey] : 55)*0.4;
        $t=intval(isset($data['taille_equipe']) ? $data['taille_equipe'] : 1); $scores['equipe']=$t>=5?90:($t>=3?75:($t>=2?60:40));
        $desc=mb_strtolower(isset($data['description']) ? $data['description'] : '', 'UTF-8');
        $iHits = iv_keyword_bonus_once_each($desc, iv_innovation_keyword_set());
        $scores['innovation']=min(98,48+$iHits*6);
        $modele=strtolower(isset($data['modele_economique']) ? $data['modele_economique'] : ''); $fScore=55+(!empty($modele)?10:0);
        foreach(['saas'=>20,'abonnement'=>18,'commission'=>15,'marketplace'=>15,'freemium'=>12,'b2b'=>10,'b2c'=>8] as $m=>$b)
            if(strpos($modele,$m)!==false){$fScore+=$b;break;}
        if(floatval(isset($data['cout_initial']) ? $data['cout_initial'] : 0)>0&&floatval(isset($data['budget_demande']) ? $data['budget_demande'] : 0)>=floatval(isset($data['cout_initial']) ? $data['cout_initial'] : 0)) $fScore+=10;
        $scores['finances']=min(98,$fScore);
        $w=['marche'=>0.30,'equipe'=>0.25,'innovation'=>0.20,'finances'=>0.25];
        $g=round(min(98,max(10,array_sum(array_map(function($k,$wt) use ($scores) { return (isset($scores[$k]) ? $scores[$k] : 50) * $wt; },array_keys($w),array_values($w))))),1);
        $risk=$g>=75?['level'=>'Faible','color'=>'#1D9E75','label'=>'Excellent potentiel']:($g>=55?['level'=>'Modéré','color'=>'#EF9F27','label'=>'Potentiel avec améliorations']:['level'=>'Élevé','color'=>'#E24B4A','label'=>'Nécessite des ajustements']);
        $recs=[];
        if($scores['marche']<60) $recs[]=['icon'=>'📊','text'=>'Élargissez votre étude de marché.'];
        if($scores['equipe']<60) $recs[]=['icon'=>'👥','text'=>'Renforcez votre équipe.'];
        if($scores['innovation']<60) $recs[]=['icon'=>'💡','text'=>"Intégrez plus d'éléments innovants."];
        if($scores['finances']<60) $recs[]=['icon'=>'💰','text'=>'Clarifiez votre modèle économique.'];
        if(empty($recs)) $recs[]=['icon'=>'🎯','text'=>"Excellent profil ! Concentrez-vous sur l'exécution."];
        return ['global_score'=>$g,'scores'=>$scores,'weights'=>$w,'risk'=>$risk,'recommendations'=>$recs];
    }

    private function calculateViability(array $data, bool $persistHistory = false) {
        $out = $this->viabilityBreakdown($data);
        if(!empty($data['id_projet'])) {
            if ($persistHistory) {
                try { $this->pdo->prepare("INSERT INTO viability_history (id_projet,score,details,created_at) VALUES (?,?,?,NOW())")->execute([$data['id_projet'],$out['global_score'],json_encode(['scores'=>isset($out['scores']) ? $out['scores'] : [],'risk'=>isset($out['risk']) ? $out['risk'] : null])]); } catch(Exception $e){}
            }
            try { $this->pdo->prepare("UPDATE projet SET viability_score=? WHERE id_projet=?")->execute([$out['global_score'],$data['id_projet']]); } catch(Exception $e){}
        }
        return $out;
    }

    // ─── BUSINESS PLAN ────────────────────────────────────────────────
    public function businessPlan() {
        $projets=$this->getAllProjets(); $businessPlan=null; $selectedProjet=null;
        if (isset($_GET['pdf'])&&isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            $bp=$this->getBusinessPlanByProjet((int)$_GET['id_projet']);
            if($bp&&$selectedProjet) { echo $this->buildBusinessPlanHTML($bp['data'],$selectedProjet); exit; }
        }
        if (isset($_GET['generate'])&&isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            if($selectedProjet) $businessPlan=$this->generateBusinessPlan($selectedProjet);
        } elseif (isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            $bp=$this->getBusinessPlanByProjet((int)$_GET['id_projet']);
            if($bp) $businessPlan=$bp['data'];
        }
        require_once __DIR__ . '/../views/front/Advanced/business_plan.php';
    }
    private function generateBusinessPlan($projet) {
        $prompt="Génère un business plan structuré en JSON pour le projet: \"{$projet['titre']}\". Description: \"{$projet['description']}\". Catégorie: \"{$projet['entreprise_categorie']}\".";
        $result=callAI($prompt); $data=json_decode($result,true);
        if(!$data) $data=['erreur'=>'Impossible de générer le business plan.'];
        try {
            $this->pdo->prepare("UPDATE projet SET business_plan_text=? WHERE id_projet=?")->execute([$result,$projet['id_projet']]);
        } catch(Exception $e){}
        return $data;
    }
    private function getBusinessPlanByProjet($id) {
        try {
            $stmt=$this->pdo->prepare("SELECT business_plan_text FROM projet WHERE id_projet=? LIMIT 1");
            $stmt->execute([$id]);
            $raw=$stmt->fetchColumn();
            if($raw===false||$raw===null||$raw==='') return null;
            $decoded=json_decode((string)$raw,true);
            return ['data'=>$decoded,'contenu'=>(string)$raw];
        } catch(Exception $e){ return null; }
    }
    public function generateComprehensivePDF($id) {
    $projet = $this->getProjetById($id);
    if (!$projet) {
        header('Location: index.php?action=projet_list&msg=notfound');
        exit;
    }

    // Get all data for comprehensive analysis
    $this->ensureProjetEnriched($id);
    $this->reconcileProjetScores($id);
    $projet = $this->getProjetById($id);
    
    $blueprint = $this->composeScoreBlueprint($projet);
    $rawSdg = $this->performSDGAnalysis(isset($projet['description']) ? $projet['description'] : '', isset($projet['titre']) ? $projet['titre'] : '');
    $sdgs = $this->decorateSdgsForView($rawSdg);
    $mentors = $this->decodeJsonList(isset($projet['mentor_suggestions']) ? $projet['mentor_suggestions'] : null);
    $investors = $this->decodeJsonList(isset($projet['investor_suggestions']) ? $projet['investor_suggestions'] : null);
    $sentimentData = !empty($projet['sentiment_data']) ? json_decode($projet['sentiment_data'], true) : null;
    $scoreHistory = $this->getScoreTimeline($id);
    $nextStepRecs = iv_build_next_step_recommendations(
        $blueprint['market_score'],
        $blueprint['innovation_score'],
        $blueprint['sustainability_score'],
        $blueprint['risk_score']
    );

    // Generate comprehensive PDF HTML
    $html = $this->buildComprehensivePDFHTML($projet, $blueprint, $sdgs, $mentors, $investors, $sentimentData, $scoreHistory, $nextStepRecs);
    
    echo $html;
    exit;
}

private function buildComprehensivePDFHTML($projet, $blueprint, $sdgs, $mentors, $investors, $sentimentData, $scoreHistory, $nextStepRecs) {
    $titre = htmlspecialchars(isset($projet['titre']) ? $projet['titre'] : 'Projet');
    $entreprise = htmlspecialchars(isset($projet['entreprise_nom']) ? $projet['entreprise_nom'] : 'Entreprise');
    $date = date('d/m/Y');
    
    $html = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Rapport d'Impact - $titre</title>
    <style>
        @page { margin: 2cm; }
        body { 
            font-family: 'Inter', Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            color: #1f2937; 
            line-height: 1.6;
        }
        .page-break { page-break-before: always; }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 3px solid #1D9E75; 
            padding-bottom: 20px;
        }
        .logo { 
            max-width: 120px; 
            margin-bottom: 20px;
        }
        h1 { 
            color: #1D9E75; 
            font-size: 28px; 
            font-weight: 700; 
            margin: 0;
        }
        h2 { 
            color: #534AB7; 
            font-size: 20px; 
            font-weight: 600; 
            margin: 25px 0 15px 0;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }
        h3 { 
            color: #374151; 
            font-size: 16px; 
            font-weight: 600; 
            margin: 20px 0 10px 0;
        }
        .score-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 20px; 
            margin: 20px 0;
        }
        .score-card { 
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); 
            border: 1px solid #e5e7eb; 
            border-radius: 12px; 
            padding: 20px; 
            text-align: center;
        }
        .score-value { 
            font-size: 36px; 
            font-weight: 700; 
            margin: 10px 0;
        }
        .score-label { 
            font-size: 14px; 
            color: #6b7280; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .gauge { 
            width: 120px; 
            height: 120px; 
            border-radius: 50%; 
            margin: 0 auto 15px;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: bold; 
            color: white;
            font-size: 18px;
        }
        .sdg-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin: 20px 0;
        }
        .sdg-item { 
            padding: 15px; 
            border-radius: 8px; 
            color: white; 
            font-weight: 600;
        }
        .mentor-card, .investor-card { 
            background: #f9fafb; 
            border: 1px solid #e5e7eb; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 15px;
        }
        .match-score { 
            background: #10b981; 
            color: white; 
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 12px; 
            font-weight: 600;
        }
        .recommendation { 
            background: #fef3c7; 
            border-left: 4px solid #f59e0b; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 0 8px 8px 0;
        }
        .timeline-item { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 10px 0; 
            border-bottom: 1px solid #e5e7eb;
        }
        .badge { 
            display: inline-block; 
            padding: 8px 12px; 
            border-radius: 6px; 
            margin: 5px; 
            font-size: 12px; 
            font-weight: 600;
        }
        .badge-earned { 
            background: #dcfce7; 
            color: #166534; 
            border: 1px solid #bbf7d0;
        }
        .badge-locked { 
            background: #f3f4f6; 
            color: #6b7280; 
            border: 1px solid #d1d5db;
        }
        .footer { 
            margin-top: 40px; 
            padding-top: 20px; 
            border-top: 1px solid #e5e7eb; 
            text-align: center; 
            color: #6b7280; 
            font-size: 12px;
        }
        .sentiment-grid { 
            display: grid; 
            grid-template-columns: repeat(4, 1fr); 
            gap: 15px; 
            margin: 20px 0;
        }
        .sentiment-item { 
            background: #f9fafb; 
            border: 1px solid #e5e7eb; 
            border-radius: 8px; 
            padding: 15px; 
            text-align: center;
        }
        .sentiment-value { 
            font-size: 24px; 
            font-weight: 700; 
            color: #1f2937;
        }
        .sentiment-label { 
            font-size: 12px; 
            color: #6b7280; 
            margin-top: 5px;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>";

    // PAGE 1: COVER
    $html .= "
<div class='header'>
    <img src='logo.png' alt='ImpactVenture Logo' class='logo'>
    <h1>$titre</h1>
    <p style='font-size: 18px; color: #6b7280; margin: 10px 0;'>$entreprise</p>
    <p style='font-size: 16px; color: #9ca3af;'>Rapport d'Impact — ImpactVenture</p>
    <p style='font-size: 14px; color: #9ca3af;'>Généré le $date</p>
</div>

<div style='background: linear-gradient(135deg, #1D9E75 0%, #534AB7 100%); height: 200px; border-radius: 12px; margin: 20px 0;'></div>";

    // PAGE 2: SCORES DASHBOARD
    $html .= "<div class='page-break'>";
    $html .= "<h2>📊 Tableau de Bord des Scores</h2>";
    
    $scores = [
        ['Innovation', $blueprint['innovation_score'], '#3b82f6'],
        ['Durabilité', $blueprint['sustainability_score'], '#10b981'],
        ['Marché', $blueprint['market_score'], '#8b5cf6'],
        ['Risque', $blueprint['risk_score'], '#ef4444']
    ];
    
    $html .= "<div class='score-grid'>";
    foreach ($scores as $score) {
        $percentage = round($score[1]);
        $angle = ($percentage / 100) * 360;
        $html .= "
        <div class='score-card'>
            <div class='gauge' style='background: conic-gradient(from 0deg, {$score[2]} {$angle}deg, #e5e7eb {$angle}deg);'>
                {$percentage}%
            </div>
            <h3 style='margin: 0; color: {$score[2]};'>{$score[0]}</h3>
            <div class='score-label'>Score sur 100</div>
        </div>";
    }
    $html .= "</div>";
    
    $finalScore = round($blueprint['final_score']);
    $interpretation = $finalScore >= 80 ? 'Excellent' : ($finalScore >= 60 ? 'Bon' : 'À améliorer');
    $html .= "
    <div style='text-align: center; margin: 30px 0;'>
        <h2 style='color: #1D9E75; font-size: 48px; margin: 0;'>$finalScore/100</h2>
        <p style='font-size: 20px; color: #6b7280; margin: 10px 0;'>Score Global</p>
        <div style='background: " . ($finalScore >= 80 ? '#10b981' : ($finalScore >= 60 ? '#f59e0b' : '#ef4444')) . "; color: white; padding: 10px 20px; border-radius: 20px; display: inline-block; font-weight: 600;'>
            $interpretation
        </div>
    </div>";
    $html .= "</div>";

    // PAGE 3: ANALYSE DU PITCH
    $html .= "<div class='page-break'>";
    $html .= "<h2>🎤 Analyse du Pitch</h2>";
    $html .= "<div style='background: #f9fafb; border-left: 4px solid #1D9E75; padding: 20px; margin: 20px 0; border-radius: 0 8px 8px 0; font-style: italic;'>";
    $html .= "<p style='margin: 0;'>" . nl2br(htmlspecialchars(isset($projet['description']) ? $projet['description'] : '')) . "</p>";
    $html .= "</div>";
    
    if ($sentimentData) {
        $html .= "<div class='sentiment-grid'>";
        $sentiments = [
            ['Sentiment', isset($sentimentData['sentiment']) ? $sentimentData['sentiment'] : 0],
            ['Clarté', isset($sentimentData['clarte']) ? $sentimentData['clarte'] : 0],
            ['Professionnalisme', isset($sentimentData['professionnalisme']) ? $sentimentData['professionnalisme'] : 0],
            ['Confiance', isset($sentimentData['confiance']) ? $sentimentData['confiance'] : 0]
        ];
        foreach ($sentiments as $sentiment) {
            $html .= "
            <div class='sentiment-item'>
                <div class='sentiment-value'>{$sentiment[1]}/100</div>
                <div class='sentiment-label'>{$sentiment[0]}</div>
            </div>";
        }
        $html .= "</div>";
        
        $pitchScore = round(isset($projet['pitch_score']) ? $projet['pitch_score'] : 0);
        $html .= "
        <div style='text-align: center; margin: 30px 0;'>
            <h3 style='color: #534AB7; font-size: 32px; margin: 0;'>$pitchScore/100</h3>
            <p style='font-size: 16px; color: #6b7280;'>Qualité du Pitch</p>
        </div>";
        
        if (!empty($sentimentData['advice'])) {
            $html .= "
            <div class='recommendation'>
                <strong>💡 Conseil IA :</strong> " . htmlspecialchars($sentimentData['advice']) . "
            </div>";
        }
    }
    $html .= "</div>";

    // PAGE 4: ODD DÉTECTÉS
    $html .= "<div class='page-break'>";
    $html .= "<h2>🌍 ODD Détectés</h2>";
    
    if (!empty($sdgs)) {
        $html .= "
            <div class='sdg-grid'>";
        foreach ($sdgs as $sdg) {
            $sdgColor = isset($sdg['color']) ? $sdg['color'] : '#000000';
            $sdgNumero = isset($sdg['numero']) ? $sdg['numero'] : '';
            $sdgTitre = isset($sdg['titre']) ? $sdg['titre'] : '';
            $html .= "
            <div class='sdg-item' style='background: {$sdgColor};'>
                <strong>ODD {$sdgNumero}</strong><br>
                {$sdgTitre}
            </div>";
        }
        $html .= "</div>";
        
        $html .= "<h3>Pourquoi ces ODD ont été détectés</h3>";
        foreach ($sdgs as $sdg) {
            $sdgColor = isset($sdg['color']) ? $sdg['color'] : '#000000';
            $sdgNumero = isset($sdg['numero']) ? $sdg['numero'] : '';
            $sdgTitre = isset($sdg['titre']) ? $sdg['titre'] : '';
            $sdgDescription = isset($sdg['description']) ? $sdg['description'] : '';
            $html .= "
            <div style='margin: 15px 0; padding: 15px; background: #f9fafb; border-radius: 8px; border-left: 4px solid {$sdgColor};'>
                <strong style='color: {$sdgColor};'>ODD {$sdgNumero}: {$sdgTitre}</strong>
                <p style='margin: 8px 0 0 0; font-size: 14px; color: #6b7280;'>{$sdgDescription}</p>
            </div>";
        }
    } else {
        $html .= "<p style='color: #6b7280; text-align: center; padding: 40px;'>Aucun ODD détecté pour ce projet.</p>";
    }
    $html .= "</div>";

    // PAGE 5: MENTORS & INVESTISSEURS
    $html .= "<div class='page-break'>";
    $html .= "<h2>👥 Mentors & Investisseurs Matchés</h2>";
    
    // Top 3 Mentors
    $html .= "<h3>Top 3 Mentors Recommandés</h3>";
    foreach (array_slice($mentors, 0, 3) as $mentor) {
        $html .= "
        <div class='mentor-card'>
            <div style='display: flex; justify-content: space-between; align-items: center;'>
                <div>
                    <strong style='font-size: 16px;'>" . htmlspecialchars($mentor['nom']) . "</strong><br>
                    <span style='color: #6b7280; font-size: 14px;'>" . htmlspecialchars($mentor['specialite']) . "</span>
                </div>
                <div style='text-align: right;'>
                    <span class='match-score'>" . round($mentor['score']) . "% Match</span><br>
                    <small style='color: #6b7280;'>" . htmlspecialchars($mentor['email']) . "</small>
                </div>
            </div>
        </div>";
    }
    
    // Top 3 Investors
    $html .= "<h3>Top 3 Investisseurs Recommandés</h3>";
    foreach (array_slice($investors, 0, 3) as $investor) {
        $html .= "
        <div class='investor-card'>
            <div style='display: flex; justify-content: space-between; align-items: center;'>
                <div>
                    <strong style='font-size: 16px;'>" . htmlspecialchars($investor['nom']) . "</strong><br>
                    <span style='color: #6b7280; font-size: 14px;'>" . htmlspecialchars($investor['focus']) . "</span><br>
                    <small style='color: #6b7280;'>" . htmlspecialchars($investor['stage']) . "</small>
                </div>
                <div style='text-align: right;'>
                    <span class='match-score' style='background: #3b82f6;'>" . round($investor['score']) . "% Match</span>
                </div>
            </div>
        </div>";
    }
    $html .= "</div>";

    // PAGE 6: RECOMMANDATIONS
    $html .= "<div class='page-break'>";
    $html .= "<h2>🎯 Vos Prochaines Étapes</h2>";
    
    if (!empty($nextStepRecs)) {
        foreach ($nextStepRecs as $rec) {
            $html .= "<div class='recommendation'>$rec</div>";
        }
    } else {
        $html .= "<p style='color: #6b7280;'>Aucune recommandation disponible pour le moment.</p>";
    }
    
    // Score History
    $html .= "<h3>📈 Historique d'Évolution des Scores</h3>";
    if (!empty($scoreHistory)) {
        $html .= "<div style='background: #f9fafb; border-radius: 8px; padding: 20px;'>";
        foreach (array_reverse(array_slice($scoreHistory, 0, 10)) as $snapshot) {
            $html .= "
            <div class='timeline-item'>
                <div>
                    <strong>" . date('d/m/Y', strtotime($snapshot['created_at'])) . "</strong>";
            if (!empty($snapshot['change_reason'])) {
                $html .= "<br><small style='color: #6b7280;'>" . htmlspecialchars($snapshot['change_reason']) . "</small>";
            }
            $html .= "</div>
                <div style='text-align: right;'>
                    <strong style='font-size: 18px; color: #1D9E75;'>" . round($snapshot['final_score'], 1) . "</strong>";
            if ($snapshot['delta_pts'] > 0) {
                $html .= "<br><small style='color: #10b981;'>+{$snapshot['delta_pts']} pts</small>";
            }
            $html .= "</div>
            </div>";
        }
        $html .= "</div>";
    } else {
        $html .= "<p style='color: #6b7280; text-align: center; padding: 20px;'>Aucun historique disponible.</p>";
    }
    $html .= "</div>";

    // PAGE 7: BADGES
    $html .= "<div class='page-break'>";
    $html .= "<h2>🏆 Badges Obtenus</h2>";
    
    $badges = [
        ['🌱', 'Éco Warrior', 'Score vert >80%', $projet['score_green'] >= 80],
        ['🚀', 'Innovateur', 'Score innovation >70%', $blueprint['innovation_score'] >= 70],
        ['🏆', 'Impact Leader', 'Top 10% des scores', $blueprint['final_score'] >= 80],
        ['🔒', 'Expert Complet', 'Score final >90%', $blueprint['final_score'] >= 90],
        ['📊', 'Analyste', '5+ évaluations', count($scoreHistory) >= 5],
        ['🌍', 'Global Citizen', '3+ ODD détectés', count($sdgs) >= 3]
    ];
    
    $html .= "<div style='display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0;'>";
    foreach ($badges as $badge) {
        $earned = $badge[3];
        $html .= "
        <div class='badge " . ($earned ? 'badge-earned' : 'badge-locked') . "'>
            <div style='font-size: 32px; margin-bottom: 10px;'>{$badge[0]}</div>
            <div style='font-weight: 600; margin-bottom: 5px;'>{$badge[1]}</div>
            <div style='font-size: 12px;'>{$badge[2]}</div>
        </div>";
    }
    $html .= "</div>";
    
    $html .= "</div>";

    // Footer
    $html .= "
<div class='footer'>
    <p><strong>ImpactVenture</strong> — Plateforme d'Évaluation et d'Accompagnement de Startups</p>
    <p>Page " . (isset($page) ? $page : '1') . " sur 7 | Généré le $date</p>
</div>";

    $html .= "</body></html>";
    
    return $html;
}

    // ─── PITCH DECK ───────────────────────────────────────────────────
    public function pitchDeck() {
        $projets=$this->getAllProjets(); $slides=null; $selectedProjet=null;
        if (isset($_GET['export'])&&isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            $pd=$this->getPitchDeckByProjet((int)$_GET['id_projet']);
            if($pd&&$selectedProjet) { echo $this->buildPitchDeckHTML($pd['slides_data'],$selectedProjet); exit; }
        }
        if (isset($_GET['generate'])&&isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            if($selectedProjet) $slides=$this->generatePitchDeck($selectedProjet);
        } elseif (isset($_GET['id_projet'])) {
            $selectedProjet=$this->getProjetById((int)$_GET['id_projet']);
            $pd=$this->getPitchDeckByProjet((int)$_GET['id_projet']);
            if($pd) $slides=$pd['slides_data'];
        }
        require_once __DIR__ . '/../views/front/Advanced/pitch_deck.php';
    }
    private function generatePitchDeck($projet) {
        // Get comprehensive project data
        $this->ensureProjetEnriched($projet['id_projet']);
        $this->reconcileProjetScores($projet['id_projet']);
        $projet = $this->getProjetById($projet['id_projet']);
        
        $blueprint = $this->composeScoreBlueprint($projet);
        $sector = strtolower(isset($projet['entreprise_categorie']) ? $projet['entreprise_categorie'] : '');
        
        // Generate sector-specific pitch deck with real data
        $slides = $this->generateEnhancedPitchDeck($projet, $blueprint, $sector);
        
        $enc = json_encode($slides, JSON_UNESCAPED_UNICODE);
        try {
            $this->pdo->prepare("UPDATE projet SET pitch_deck_json=? WHERE id_projet=?")->execute([$enc, $projet['id_projet']]);
        } catch(Exception $e){}
        return $slides;
    }

    private function generateEnhancedPitchDeck($projet, $blueprint, $sector) {
        $titre = isset($projet['titre']) ? $projet['titre'] : 'Projet';
        $entreprise = isset($projet['entreprise_nom']) ? $projet['entreprise_nom'] : 'Entreprise';
        $description = isset($projet['description']) ? $projet['description'] : '';
        $tagline = $this->extractTagline($description);
        
        // Sector-specific colors and backgrounds
        $sectors = [
            'agriculture' => ['primary' => '#10B981', 'secondary' => '#059669', 'gradient' => 'linear-gradient(135deg, #10B981 0%, #059669 100%)'],
            'tech' => ['primary' => '#3B82F6', 'secondary' => '#1D4ED8', 'gradient' => 'linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%)'],
            'fintech' => ['primary' => '#F59E0B', 'secondary' => '#D97706', 'gradient' => 'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)'],
            'health' => ['primary' => '#EF4444', 'secondary' => '#DC2626', 'gradient' => 'linear-gradient(135deg, #EF4444 0%, #DC2626 100%)'],
            'education' => ['primary' => '#8B5CF6', 'secondary' => '#7C3AED', 'gradient' => 'linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%)'],
            'retail' => ['primary' => '#EC4899', 'secondary' => '#DB2777', 'gradient' => 'linear-gradient(135deg, #EC4899 0%, #DB2777 100%)']
        ];
        
        $sectorConfig = $sectors['tech']; // default
        foreach ($sectors as $key => $config) {
            if (strpos($sector, $key) !== false) {
                $sectorConfig = $config;
                break;
            }
        }
        
        return [
            [
                'titre' => 'COVER',
                'background' => $sectorConfig['gradient'],
                'content' => [
                    'title' => $titre,
                    'tagline' => $tagline,
                    'subtitle' => 'Présenté à ImpactVenture',
                    'date' => date('d F Y'),
                    'company' => $entreprise
                ]
            ],
            [
                'titre' => 'LE PROBLÈME',
                'background' => 'linear-gradient(135deg, #1F2937 0%, #111827 100%)',
                'content' => [
                    'problem' => $this->extractProblemStatement($description),
                    'icon' => '🎯',
                    'stats' => $this->extractProblemStats($description)
                ]
            ],
            [
                'titre' => 'NOTRE SOLUTION',
                'background' => $sectorConfig['gradient'],
                'content' => [
                    'solution' => $this->formatSolution($description),
                    'title' => $titre,
                    'key_points' => $this->extractKeyPoints($description)
                ]
            ],
            [
                'titre' => 'MARCHÉ CIBLE',
                'background' => 'linear-gradient(135deg, #06B6D4 0%, #0891B2 100%)',
                'content' => [
                    'sector' => ucfirst(isset($projet['entreprise_categorie']) ? $projet['entreprise_categorie'] : 'Technologie'),
                    'market_score' => round($blueprint['market_score']),
                    'incubator' => 'ImpactVenture',
                    'market_size' => $this->estimateMarketSize($sector)
                ]
            ],
            [
                'titre' => 'MODÈLE ÉCONOMIQUE',
                'background' => 'linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%)',
                'content' => [
                    'model' => $this->suggestBusinessModel($sector, isset($projet['modele_economique']) ? $projet['modele_economique'] : ''),
                    'revenue_streams' => $this->getRevenueStreams($sector),
                    'pricing' => $this->suggestPricing($sector)
                ]
            ],
            [
                'titre' => 'TRACTION',
                'background' => 'linear-gradient(135deg, #F97316 0%, #EA580C 100%)',
                'content' => [
                    'innovation_score' => round($blueprint['innovation_score']),
                    'sustainability_score' => round($blueprint['sustainability_score']),
                    'status' => isset($projet['statut']) ? $projet['statut'] : 'soumis',
                    'mentors_matched' => count($this->decodeJsonList(isset($projet['mentor_suggestions']) ? $projet['mentor_suggestions'] : null))
                ]
            ],
            [
                'titre' => 'CONCURRENCE',
                'background' => 'linear-gradient(135deg, #1F2937 0%, #111827 100%)',
                'content' => [
                    'competitors' => $this->generateCompetitorAnalysis($sector),
                    'differentiation' => $this->extractDifferentiation($description),
                    'competitive_advantage' => $this->getCompetitiveAdvantage($blueprint)
                ]
            ],
            [
                'titre' => 'ÉQUIPE',
                'background' => 'linear-gradient(135deg, #6B7280 0%, #4B5563 100%)',
                'content' => [
                    'team_size' => max(1, (int)(isset($projet['taille_equipe']) ? $projet['taille_equipe'] : 1)),
                    'incubator' => 'ImpactVenture',
                    'expertise' => $this->suggestTeamExpertise($sector)
                ]
            ],
            [
                'titre' => 'PLAN FINANCIER',
                'background' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                'content' => [
                    'final_score' => round($blueprint['final_score']),
                    'funding_stage' => $this->suggestFundingStage($blueprint['final_score']),
                    'budget' => number_format((float)(isset($projet['budget_demande']) ? $projet['budget_demande'] : 0), 0, ',', ' ') . ' TND'
                ]
            ],
            [
                'titre' => 'CALL TO ACTION',
                'background' => $sectorConfig['gradient'],
                'content' => [
                    'cta_text' => "Rejoignez l'aventure {$titre}",
                    'next_steps' => $this->suggestNextSteps($blueprint),
                    'contact_info' => 'contact@impactventure.tn',
                    'sdg_badges' => $this->getSDGBadges($projet['id_projet'])
                ]
            ]
        ];
    }

    private function extractTagline($description) {
        $sentences = preg_split('/[.!?]+/', $description, 2);
        return trim(isset($sentences[0]) ? $sentences[0] : 'Solution innovante pour le marché tunisien');
    }

    private function extractProblemStatement($description) {
        $problem_keywords = ['problème', 'défi', 'besoin', 'manque', 'difficile', 'pain', 'friction'];
        $sentences = preg_split('/[.!?]+/', $description);
        
        foreach ($sentences as $sentence) {
            foreach ($problem_keywords as $keyword) {
                if (stripos($sentence, $keyword) !== false) {
                    return trim($sentence);
                }
            }
        }
        
        return "Le marché actuel fait face à des défis importants que notre solution adresse directement.";
    }

    private function extractProblemStats($description) {
        // Extract any numbers or statistics from description
        preg_match_all('/\d+[%]?/', $description, $matches);
        return array_slice(isset($matches[0]) ? $matches[0] : [], 0, 3);
    }

    private function formatSolution($description) {
        $sentences = preg_split('/[.!?]+/', $description);
        $solution = [];
        
        foreach (array_slice($sentences, 0, 3) as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 20) {
                $solution[] = '• ' . ucfirst($sentence);
            }
        }
        
        return empty($solution) ? ['• Solution innovante basée sur la technologie'] : $solution;
    }

    private function extractKeyPoints($description) {
        $points = [];
        $keywords = ['innovante', 'unique', 'première', 'exclusif', 'révolutionnaire'];
        
        foreach ($keywords as $keyword) {
            if (stripos($description, $keyword) !== false) {
                $points[] = ucfirst($keyword) . ' approche';
            }
        }
        
        return array_slice($points, 0, 3);
    }

    private function estimateMarketSize($sector) {
        $sizes = [
            'agriculture' => 'Marché agricole tunisien: 2.5M TND',
            'tech' => 'Marché digital tunisien: 800M TND',
            'fintech' => 'Marché fintech MENA: 2.2B TND',
            'health' => 'Marché santé tunisien: 1.2M TND',
            'education' => 'Marché éducation Tunisie: 450M TND',
            'retail' => 'Marché e-commerce Tunisie: 600M TND'
        ];
        
        foreach ($sizes as $key => $size) {
            if (strpos($sector, $key) !== false) {
                return $size;
            }
        }
        
        return 'Marché tunisien: 500M TND';
    }

    private function suggestBusinessModel($sector, $existingModel) {
        if (!empty($existingModel)) {
            return $existingModel;
        }
        
        $models = [
            'fintech' => 'Freemium, abonnement, commission sur transactions',
            'agriculture' => 'Vente directe, B2B, abonnement IoT, marketplace',
            'tech' => 'SaaS, licence, marketplace, freemium',
            'health' => 'B2B, licence SaaS, abonnement plateforme',
            'education' => 'Freemium, abonnement, B2B, licence institutionnelle',
            'retail' => 'Marketplace, commission, abonnement premium, B2C'
        ];
        
        foreach ($models as $key => $model) {
            if (strpos($sector, $key) !== false) {
                return $model;
            }
        }
        
        return 'SaaS, abonnement, marketplace';
    }

    private function getRevenueStreams($sector) {
        $streams = [
            'fintech' => ['Commissions transactions', 'Abonnements premium', 'Services B2B'],
            'agriculture' => ['Vente produits', 'Abonnements IoT', 'Services conseils'],
            'tech' => ['Licences SaaS', 'Abonnements', 'Marketplace fees'],
            'health' => ['Licences logicielles', 'Abonnements plateforme', 'Services B2B'],
            'education' => ['Abonnements étudiants', 'Licences institutions', 'Contenu premium'],
            'retail' => ['Commissions ventes', 'Abonnements premium', 'Publicités ciblées']
        ];
        
        return isset($streams[$sector]) ? $streams[$sector] : ['Licences', 'Abonnements', 'Services'];
    }

    private function suggestPricing($sector) {
        $pricing = [
            'fintech' => '1-3% commission ou 29-99€/mois abonnement',
            'agriculture' => '50-200€/mois IoT + 5-10% commission',
            'tech' => '29-299€/mois SaaS selon features',
            'health' => '99-999€/mois B2B selon taille',
            'education' => '9-49€/mois étudiant, 99-499€/mois institution',
            'retail' => 'Commission 2-8% + 9.99-29.99€/mois premium'
        ];
        
        return isset($pricing[$sector]) ? $pricing[$sector] : '29-199€/mois selon plan';
    }

    private function generateCompetitorAnalysis($sector) {
        $competitors = [
            'fintech' => ['Banques traditionnelles', 'Startups fintech locales', 'International players'],
            'agriculture' => ['Agriculteurs traditionnels', 'Importateurs', 'Concurrents locaux'],
            'tech' => ['Solutions existantes', 'International SaaS', 'Startups locales'],
            'health' => ['Hôpitaux publics', 'Privés', 'Solutions digitales'],
            'education' => ['Établissements publics', 'Privés', 'Plateformes e-learning'],
            'retail' => ['Commerces physiques', 'E-commerce existants', 'Marketplaces']
        ];
        
        return isset($competitors[$sector]) ? $competitors[$sector] : ['Acteurs traditionnels', 'Solutions existantes', 'Nouveaux entrants'];
    }

    private function extractDifferentiation($description) {
        $differentiators = ['unique', 'premier', 'exclusif', 'innovant', 'révolutionnaire'];
        $diff = [];
        
        foreach ($differentiators as $word) {
            if (stripos($description, $word) !== false) {
                $diff[] = 'Approche ' . $word . ' sur le marché';
            }
        }
        
        return empty($diff) ? ['Solution unique sur le marché'] : $diff;
    }

    private function getCompetitiveAdvantage($blueprint) {
        $advantages = [];
        
        if ($blueprint['innovation_score'] > 70) {
            $advantages[] = 'Innovation technologique supérieure';
        }
        if ($blueprint['market_score'] > 70) {
            $advantages[] = 'Fort potentiel de marché';
        }
        if ($blueprint['sustainability_score'] > 70) {
            $advantages[] = 'Impact environnemental positif';
        }
        
        return empty($advantages) ? ['Proposition de valeur unique'] : $advantages;
    }

    private function suggestTeamExpertise($sector) {
        $expertise = [
            'fintech' => ['Finance', 'Tech', 'Réglementation', 'Data Science'],
            'agriculture' => ['Agronomie', 'Tech', 'Business', 'Supply Chain'],
            'tech' => ['Développement', 'Design', 'Business', 'Data'],
            'health' => ['Médical', 'Tech', 'Réglementation', 'Data'],
            'education' => ['Pédagogie', 'Tech', 'Business', 'Content'],
            'retail' => ['Commerce', 'Marketing', 'Tech', 'Logistics']
        ];
        
        return isset($expertise[$sector]) ? $expertise[$sector] : ['Business', 'Tech', 'Marketing'];
    }

    private function suggestFundingStage($score) {
        if ($score > 80) return 'Series A';
        if ($score > 60) return 'Seed';
        return 'Pré-seed';
    }

    private function suggestNextSteps($blueprint) {
        $steps = [];
        
        if ($blueprint['market_score'] < 50) {
            $steps[] = 'Validation client et étude marché approfondie';
        }
        if ($blueprint['innovation_score'] < 50) {
            $steps[] = 'Prototypage et tests utilisateurs';
        }
        if ($blueprint['sustainability_score'] < 60) {
            $steps[] = 'Analyse impact environnemental';
        }
        
        return empty($steps) ? ['Déploiement commercial et scaling'] : $steps;
    }

    private function getSDGBadges($projetId) {
        // This would integrate with SDG detection
        return ['ODD 8', 'ODD 9', 'ODD 11'];
    }
    private function defaultSlides(array $p): array
    {
        $t = (string) ($p['titre'] ?? 'Projet');
        $sector = ucfirst(mb_strtolower(trim((string) ($p['entreprise_categorie'] ?? 'votre secteur')), 'UTF-8'));
        $descCanonical = preg_replace('/\s+/u', ' ', trim((string) ($p['description'] ?? '')));
        $descBrief = '';
        if ($descCanonical !== '') {
            $descBrief = mb_strlen($descCanonical, 'UTF-8') > 420 ? mb_substr($descCanonical, 0, 417, 'UTF-8') . '…' : $descCanonical;
        } else {
            $descBrief = "Ajoutez sur la fiche projet : problème précis pour un segment client, valeur unique, première traction ou test terrain.";
        }
        $snippetProblem = mb_substr($descCanonical, 0, min(520, mb_strlen($descCanonical, 'UTF-8')), 'UTF-8');
        if ($snippetProblem === '') {
            $snippetProblem = 'Le terrain en ' . $sector . ' fait face à friction coûts, lenteurs ou rupture du service au moment où les attentes utilisateurs augmentent.';
        }
        $bp = $this->composeScoreBlueprint($p);
        $pitchR = round((float) ($p['pitch_score'] ?? 0));
        $viaR = round((float) ($bp['viability_global'] ?? 0));
        $mkt = round((float) ($bp['market_score'] ?? 0));
        $green = (int) round((float) ($p['score_green'] ?? 0));
        $modele = trim((string) ($p['modele_economique'] ?? ''));
        if ($modele === '') {
            $modele = 'À préciser : revenus récurrents, commissions, marketplace…';
        }
        $budget = number_format((float) ($p['budget_demande'] ?? 0), 0, ',', ' ');
        $teamSz = max(1, (int) ($p['taille_equipe'] ?? 1));

        return [
            [
                'titre' => $t,
                'contenu' => "Accélérateur d’impact en {$sector}.\nSynthèse scores plateforme : pitch {$pitchR}/100 • viabilité {$viaR}/100 • impact green {$green}%.",
            ],
            [
                'titre' => 'Le Problème',
                'contenu' => '• Synthèse extraite du porteur :' . "\n" . mb_substr($snippetProblem, 0, min(760, mb_strlen($snippetProblem, 'UTF-8')), 'UTF-8'),
            ],
            [
                'titre' => 'Notre Solution',
                'contenu' => "• À partir de votre description :\n" . ($descBrief),
            ],
            [
                'titre' => 'Marché Cible',
                'contenu' => "• Vertical : {$sector}\n• Indice lisibilité « marché » (IA) : {$mkt}/100 — affinez précisément clients types, géographie (Tunisie / urbain-regional).\n• Pensez TAM→SAM avec 2 références chiffrées plausibles pour votre créneau.",
            ],
            [
                'titre' => 'Business Model',
                'contenu' => "• Composition actuelle décrite :\n« {$modele} »",
            ],
            [
                'titre' => 'Traction & Feuille de route',
                'contenu' => "À documenter :\n• 3 jalons MVP / pilotes utilisateurs réels.\n• 2 métriques d’impact ou revenus à surveiller tous les mois.\n(score pitch {$pitchR}/100 — renforcez preuves chiffrées)",
            ],
            [
                'titre' => 'Concurrence & Différenciation',
                'contenu' => "• Ancrez-vous avec : pourquoi un client passerait depuis une solution générique ?\n• Mettez prix relatif ou cycle d’usage typique observé localement dans {$sector}.",
            ],
            [
                'titre' => 'Équipe',
                'contenu' => "• Taille équipe déclarée : {$teamSz} personnes.\n• Complétez : rôle lead produit / distribution / techno + expériences sectorielles.",
            ],
            [
                'titre' => 'Financier',
                'contenu' => "• Budget associé projet (indicatif dossier) : {$budget} TND.\n(score viabilité globale {$viaR}/100 — détaillez besoin utilisé et horizon break-even prévisionnel).",
            ],
            [
                'titre' => 'Call to Action',
                'contenu' => "Prochain passage : préciser votre ask (montant phase, usages 60/120 jours) puis valider mentors / investisseurs suggérés ImpactVenture.\nMerci.",
            ]
        ];
    }
    private function getPitchDeckByProjet($id) {
        try {
            $stmt=$this->pdo->prepare("SELECT pitch_deck_json FROM projet WHERE id_projet=? LIMIT 1");
            $stmt->execute([$id]);
            $raw=$stmt->fetchColumn();
            if($raw===false||$raw===null||$raw==='') return null;
            if(is_array($raw)) $slides=$raw;
            else $slides=json_decode((string)$raw,true);
            if(!$slides||!is_array($slides)) return null;
            return ['slides_data'=>$slides];
        } catch(Exception $e){ return null; }
    }
    private function buildPitchDeckHTML($slides,$projet) {
        $titre=htmlspecialchars($projet['titre']??'Pitch Deck');
        $colors=['#1D9E75','#534AB7','#EF9F27','#3B82F6','#10B981','#8B5CF6','#F59E0B','#06B6D4','#E24B4A','#1D9E75'];
        $h="<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Pitch Deck - $titre</title>";
        $h.="<style>*{margin:0;padding:0;box-sizing:border-box;}body{font-family:'Segoe UI',sans-serif;background:#111;}.slide{width:100%;min-height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:60px;color:white;text-align:center;}.slide h1{font-size:3em;margin-bottom:30px;}.slide .content{font-size:1.4em;line-height:2;white-space:pre-line;}</style></head><body>";
        foreach($slides as $i=>$slide) {
            $bg=$colors[$i%count($colors)];
            $h.="<div class='slide' style='background:linear-gradient(135deg,{$bg},{$bg}dd);'>";
            $h.="<h1>".htmlspecialchars($slide['titre']??'Slide '.($i+1))."</h1>";
            $h.="<div class='content'>".nl2br(htmlspecialchars($slide['contenu']??''))."</div></div>";
        }
        $h.="</body></html>"; return $h;
    }

    /** @param mixed $json */
    private function decodeJsonList($json): array
    {
        if (!$json) {
            return [];
        }
        $a = json_decode($json, true);
        return is_array($a) ? $a : [];
    }

    private function decorateSdgsForView(array $analysis): array
    {
        $meta = [
            1 => ['icone' => '🏚️', 'couleur' => '#E5243B'],
            2 => ['icone' => '🍲', 'couleur' => '#DDA63A'],
            3 => ['icone' => '❤️', 'couleur' => '#4C9F38'],
            4 => ['icone' => '📚', 'couleur' => '#C5192D'],
            5 => ['icone' => '⚖️', 'couleur' => '#FF3A21'],
            6 => ['icone' => '💧', 'couleur' => '#26BDE2'],
            7 => ['icone' => '⚡', 'couleur' => '#FCC30B'],
            8 => ['icone' => '💼', 'couleur' => '#A21942'],
            9 => ['icone' => '🏭', 'couleur' => '#FD6925'],
            11 => ['icone' => '🏙️', 'couleur' => '#FD9D24'],
            13 => ['icone' => '🌍', 'couleur' => '#3F7E44']
        ];
        $out = [];
        foreach ($analysis as $r) {
            $id = (int) ($r['id'] ?? 0);
            $m = $meta[$id] ?? ['icone' => '🎯', 'couleur' => '#534AB7'];
            $out[] = [
                'numero' => $id,
                'nom' => $r['nom'] ?? ('ODD ' . $id),
                'icone' => $m['icone'],
                'couleur' => $m['couleur']
            ];
        }
        return $out;
    }

    private function mentorSlug(string $nom): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $nom), '-'));
    }

    private function mentorSeedPool(): array
    {
        return [
            ['nom' => 'Dr. Amel Karoui', 'specialite' => 'Scaling & traction', 'secteurs' => 'tech,digital,fintech,intelligence', 'email' => 'coach.amel.karoui@impactventure.demo'],
            ['nom' => 'Mehdi Jlassi', 'specialite' => 'AgriTech & IoT', 'secteurs' => 'agriculture durable,iot,bio', 'email' => 'coach.mehdi.jlassi@impactventure.demo'],
            ['nom' => 'Sarra Ben Amor', 'specialite' => 'Énergie & climat', 'secteurs' => 'energie renouvelable,vert,co2', 'email' => 'coach.sarra.benamor@impactventure.demo'],
            ['nom' => 'Karim Ouertani', 'specialite' => 'Product & UX', 'secteurs' => 'digital,ecommerce,tech', 'email' => 'coach.karim.ouertani@impactventure.demo'],
            ['nom' => 'Salma Hdhir', 'specialite' => 'Finance & MVP', 'secteurs' => 'fintech,paiement,digital', 'email' => 'coach.salma.hdhir@impactventure.demo']
        ];
    }

    private function investorSeedPool(): array
    {
        return [
            ['nom' => 'Smart Capital Tunisie', 'type_investissement' => 'Seed / impact', 'secteurs' => 'tech,digital,energie renouvelable', 'budget_max' => 500000],
            ['nom' => 'Flat6Labs Angels', 'type_investissement' => 'Pré-seed SaaS', 'secteurs' => 'tech,fintech,digital', 'budget_max' => 300000],
            ['nom' => 'GreenGrowth Fund', 'type_investissement' => 'Climat & agri', 'secteurs' => 'agriculture durable,energie renouvelable,vert', 'budget_max' => 800000],
            ['nom' => 'MENA Impact VC', 'type_investissement' => 'Série A', 'secteurs' => 'digital,ecommerce,fintech', 'budget_max' => 2000000]
        ];
    }

    /** @param array<string,mixed> $projet */
    private function matchMentorsInline(array $projet): array
    {
        $cat = strtolower($projet['entreprise_categorie'] ?? '');
        $blob = strtolower(($projet['titre'] ?? '') . ' ' . ($projet['description'] ?? ''));
        $out = [];
        foreach ($this->mentorSeedPool() as $m) {
            $score = 40;
            foreach (array_map('trim', explode(',', strtolower($m['secteurs'] ?? ''))) as $s) {
                if ($s !== '' && strpos($cat, $s) !== false) {
                    $score += 35;
                    break;
                }
            }
            foreach (preg_split('/\s+/', $blob) ?: [] as $w) {
                if (strlen($w) >= 5 && strpos($m['specialite'], substr($w, 0, 5)) !== false) {
                    $score += 5;
                }
            }
            if (strpos($blob, 'mobile') !== false && strpos(strtolower($m['specialite']), 'finance') !== false) {
                $score += 10;
            }
            $score = min(96, max(52, $score));
            $slug = $this->mentorSlug((string) $m['nom']);
            $out[] = [
                'nom' => $m['nom'],
                'specialite' => $m['specialite'],
                'score' => $score,
                'slug' => $slug,
                'email' => $m['email'] ?? null
            ];
        }
        usort($out, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($out, 0, 5);
    }

    /** @param array<string,mixed> $projet */
    private function matchInvestorsInline(array $projet): array
    {
        $cat = strtolower($projet['entreprise_categorie'] ?? '');
        $budget = floatval($projet['budget_demande'] ?? 0);
        $out = [];
        foreach ($this->investorSeedPool() as $inv) {
            $score = 35;
            foreach (array_map('trim', explode(',', strtolower($inv['secteurs'] ?? ''))) as $s) {
                if ($s !== '' && strpos($cat, $s) !== false) {
                    $score += 40;
                    break;
                }
            }
            if ($budget > 0 && $budget <= floatval($inv['budget_max'] ?? 0)) {
                $score += 20;
            }
            $score = min(94, max(48, $score));
            $out[] = ['nom' => $inv['nom'], 'type_investissement' => $inv['type_investissement'], 'score' => $score];
        }
        usort($out, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($out, 0, 5);
    }

    private function ensureProjetEnriched(int $id): void
    {
        $p = $this->getProjetById($id);
        if (!$p) {
            return;
        }
        $upd = [];

        $viability = isset($p['viability_score']) && $p['viability_score'] !== '' && $p['viability_score'] !== null
            ? (float) $p['viability_score'] : null;
        if ($viability === null || $viability <= 0) {
            $row = [
                'description' => $p['description'] ?? '',
                'taille_marche' => $p['taille_marche'] ?? 'moyen',
                'niveau_concurrence' => $p['niveau_concurrence'] ?? 'moyen',
                'taille_equipe' => $p['taille_equipe'] ?? 3,
                'modele_economique' => $p['modele_economique'] ?? 'saas,b2c',
                'cout_initial' => $p['cout_initial'] ?? 0,
                'budget_demande' => $p['budget_demande'] ?? 0,
                'id_projet' => $id
            ];
            $res = $this->viabilityBreakdown($row);
            $upd['viability_score'] = (float) ($res['global_score'] ?? 0);
        }

        $mentDec = $this->decodeJsonList($p['mentor_suggestions'] ?? null);
        $invDec = $this->decodeJsonList($p['investor_suggestions'] ?? null);
        if (count($mentDec) < 1) {
            $upd['mentor_suggestions'] = json_encode($this->matchMentorsInline($p), JSON_UNESCAPED_UNICODE);
        }
        if (count($invDec) < 1) {
            $upd['investor_suggestions'] = json_encode($this->matchInvestorsInline($p), JSON_UNESCAPED_UNICODE);
        }

        $sets = [];
        $vals = [];
        foreach ($upd as $col => $val) {
            $sets[] = "$col = :$col";
            $vals[":$col"] = $val;
        }
        if ($sets !== []) {
            $vals[':id_projet_u'] = $id;
            $sql = 'UPDATE projet SET ' . implode(', ', $sets) . ' WHERE id_projet = :id_projet_u';
            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($vals);
            } catch (Exception $e) { /* ignore */ }
        }
    }

    private function viabilityRowFromProjet(array $p): array
    {
        return [
            'description' => $p['description'] ?? '',
            'taille_marche' => $p['taille_marche'] ?? 'moyen',
            'niveau_concurrence' => $p['niveau_concurrence'] ?? 'moyen',
            'taille_equipe' => $p['taille_equipe'] ?? 3,
            'modele_economique' => $p['modele_economique'] ?? 'saas,b2c',
            'cout_initial' => $p['cout_initial'] ?? 0,
            'budget_demande' => $p['budget_demande'] ?? 0,
            'id_projet' => $p['id_projet'] ?? null
        ];
    }

    /**
     * @param array<string,mixed> $projet
     * @return array{market_score:float,innovation_score:float,sustainability_score:float,risk_score:float,final_score:float,viability_global:float}
     */
    private function composeScoreBlueprint(array $projet): array
    {
        $vb = $this->viabilityBreakdown($this->viabilityRowFromProjet($projet));
        $pitch = (float) ($projet['pitch_score'] ?? 0);
        $market = (float) ($vb['scores']['marche'] ?? 0);
        $innovation = (float) ($vb['scores']['innovation'] ?? 0);
        $sustainability = (float) ($projet['score_green'] ?? 0);
        $risk = iv_risk_score_from_text_pitch($projet['description'] ?? '', $pitch);
        $viaG = (float) ($vb['global_score'] ?? 0);
        $final = (float) round(0.28 * $pitch + 0.32 * $viaG + 0.22 * $sustainability + 0.18 * $market);

        return [
            'market_score' => $market,
            'innovation_score' => $innovation,
            'sustainability_score' => $sustainability,
            'risk_score' => $risk,
            'final_score' => $final,
            'viability_global' => $viaG
        ];
    }

    /** Recalcule tags SDG / pitch / viabilité, aligne score_ia et ajoute une ligne project_scores si le profil change. */
    private function reconcileProjetScores(int $id): void
    {
        $this->ensureAdvancedTables();
        $p = $this->getProjetById($id);
        if (!$p) {
            return;
        }

        $this->analyzeAndSaveSDG($id, $p['description'] ?? '', $p['titre'] ?? '');
        $a = $this->performSentimentAnalysis($p['description'] ?? '');
        try {
            $this->pdo->prepare('UPDATE projet SET pitch_score = ?, sentiment_data = ? WHERE id_projet = ?')->execute([
                $a['pitch_quality'] ?? 0,
                json_encode($a, JSON_UNESCAPED_UNICODE),
                $id
            ]);
        } catch (Exception $e) { /* ignore */ }

        $vb = $this->viabilityBreakdown($this->viabilityRowFromProjet($p));
        try {
            $this->pdo->prepare('UPDATE projet SET viability_score = ? WHERE id_projet = ?')->execute([(float) ($vb['global_score'] ?? 0), $id]);
        } catch (Exception $e) { /* ignore */ }

        $this->ensureProjetEnriched($id);
        $p = $this->getProjetById($id);
        if (!$p) {
            return;
        }

        $lc = mb_strtolower($p['description'] ?? '', 'UTF-8');
        $posHits = iv_keyword_bonus_once_each($lc, iv_positive_keyword_set());
        $canonical = preg_replace('/\s+/u', ' ', trim($p['description'] ?? ''));

        $bp = $this->composeScoreBlueprint($p);
        $sector = (string) ($p['entreprise_categorie'] ?? '');
        $fingerprint = sha1(implode('|', [
            'iv2',
            $canonical,
            $sector,
            (string) round((float) $bp['market_score']),
            (string) round((float) $bp['final_score']),
            (string) $posHits,
            (string) round((float) ($a['pitch_quality'] ?? 0))
        ]));

        $snapMeta = [
            'fp' => $fingerprint,
            'desc_len' => iv_word_count_fr($canonical),
            'positive_kw' => $posHits,
            'sector' => $sector,
            'pitch' => round((float) ($a['pitch_quality'] ?? 0))
        ];

        try {
            $stmt = $this->pdo->prepare('SELECT final_score, delta_pts, change_reason, created_at, snapshot_json FROM project_scores WHERE id_projet = ? ORDER BY created_at DESC, id DESC LIMIT 1');
            $stmt->execute([$id]);
            $prev = $stmt->fetch(PDO::FETCH_ASSOC);

            $prevFinger = '';
            $prevDecoded = [];
            if (!empty($prev['snapshot_json'])) {
                $prevDecoded = json_decode((string) $prev['snapshot_json'], true);
                $prevFinger = is_array($prevDecoded) ? ($prevDecoded['fp'] ?? '') : '';
            }

            if ($prevFinger !== '' && $prevFinger === $fingerprint) {
                try {
                    $this->pdo->prepare('UPDATE projet SET score_ia = ? WHERE id_projet = ?')->execute([(float) $bp['final_score'], $id]);
                } catch (Exception $e) { /* ignore */ }
                return;
            }

            $prevFinal = $prev !== false ? (float) ($prev['final_score'] ?? 0) : null;
            $delta = $prevFinal === null ? 0.0 : round((float) $bp['final_score'] - $prevFinal, 1);

            $reason = $prev === false ? 'Premier bilan' : '';
            $prevLc = isset($prevDecoded['desc_len']) ? (int) $prevDecoded['desc_len'] : -1;

            $sectorWas = isset($prevDecoded['sector']) ? (string) $prevDecoded['sector'] : '';

            $prevPitch = isset($prevDecoded['pitch']) ? (float) $prevDecoded['pitch'] : null;
            $prevPos = isset($prevDecoded['positive_kw']) ? (int) $prevDecoded['positive_kw'] : -1;

            if ($reason === '' && $sectorWas !== '' && $sectorWas !== $sector) {
                $reason = 'Secteur modifié';
            }
            if ($reason === '') {
                $curPitch = (float) round((float) ($a['pitch_quality'] ?? 0));
                if ($prevPitch !== null && $curPitch > $prevPitch + 4) {
                    $reason = 'Pitch amélioré';
                }
            }
            if ($reason === '' && $prevPos >= 0 && $posHits > $prevPos) {
                $reason = 'Nouveaux mots-clés ajoutés';
            }
            if ($reason === '' && $prevLc >= 0 && $snapMeta['desc_len'] > $prevLc + 4) {
                $reason = 'Description enrichie';
            }
            if ($reason === '') {
                $reason = 'Scores mis à jour';
            }

            $ins = $this->pdo->prepare(
                'INSERT INTO project_scores (id_projet, final_score, delta_pts, change_reason, market_score, innovation_score, sustainability_score, risk_score, snapshot_json, created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())'
            );
            $ins->execute([
                $id,
                (float) $bp['final_score'],
                $delta,
                $reason,
                (float) $bp['market_score'],
                (float) $bp['innovation_score'],
                (float) $bp['sustainability_score'],
                (float) $bp['risk_score'],
                json_encode($snapMeta, JSON_UNESCAPED_UNICODE),
            ]);

            try {
                $this->pdo->prepare('UPDATE projet SET score_ia = ? WHERE id_projet = ?')->execute([(float) $bp['final_score'], $id]);
            } catch (Exception $e) { /* ignore */ }
        } catch (Exception $e) {
            try {
                $this->pdo->prepare('UPDATE projet SET score_ia = ? WHERE id_projet = ?')->execute([(float) $bp['final_score'], $id]);
            } catch (Exception $e2) { /* ignore */ }
        }
    }

    /** @return list<array<string,mixed>> */
    private function getScoreTimeline(int $idProjet): array
    {
        $this->ensureAdvancedTables();
        try {
            $stmt = $this->pdo->prepare('SELECT final_score, delta_pts, change_reason, created_at FROM project_scores WHERE id_projet = ? ORDER BY created_at DESC, id DESC LIMIT 12');
            $stmt->execute([$idProjet]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function countMentorInterested(int $idProjet): int
    {
        $this->ensureAdvancedTables();
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM mentor_interests WHERE projet_id = ? AND is_interested = 1');
            $stmt->execute([$idProjet]);
            return (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

    /** API JSON POST { mentor_slug, id_projet } */
    public function mentorInterestExpress(): void
    {
        $this->ensureAdvancedTables();
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode((string) file_get_contents('php://input'), true) ?: [];
        $slug = trim((string) ($input['mentor_slug'] ?? ''));
        $pid = (int) ($input['id_projet'] ?? $input['projet_id'] ?? 0);
        if ($slug === '' || $pid <= 0) {
            echo json_encode(['ok' => false, 'error' => 'Paramètres invalides']);
            exit;
        }
        $known = [];
        foreach ($this->mentorSeedPool() as $m) {
            $known[$this->mentorSlug((string) $m['nom'])] = true;
        }
        if (!isset($known[$slug])) {
            echo json_encode(['ok' => false, 'error' => 'Mentor inconnu']);
            exit;
        }
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO mentor_interests (mentor_slug, projet_id, is_interested, noted_at) VALUES (?,?,1,NOW()) ON DUPLICATE KEY UPDATE is_interested=1, noted_at=NOW()'
            );
            $stmt->execute([mb_substr($slug, 0, 127), $pid]);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'error' => 'Enregistrement impossible']);
            exit;
        }
        echo json_encode(['ok' => true, 'interested_total' => $this->countMentorInterested($pid)]);
        exit;
    }

    /**
     * Badges dynamiques (sans tables badge/user_badges).
     */
    public function badgesPage(?int $idFiche)
    {
        $fiches = $this->getAllFiches();
        try {
            $topFiveGreenIds = array_values(array_map(
                'intval',
                $this->pdo->query(
                    'SELECT id FROM fiche_entreprise ORDER BY score_green DESC LIMIT 5'
                )->fetchAll(PDO::FETCH_COLUMN) ?: []
            ));
        } catch (Exception $e) {
            $topFiveGreenIds = [];
        }
        $catalog = $this->getBadgeCatalog($topFiveGreenIds);
        $selectedFiche = null;
        $ficheBadges = [];
        $leaderboardRows = [];

        try {
            $leaderboardRows = $this->pdo->query(
                "SELECT id, nom, score_green, badges FROM fiche_entreprise ORDER BY score_green DESC LIMIT 12"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $leaderboardRows = [];
        }
        foreach ($leaderboardRows as &$row) {
            $row['badge_count'] = empty($row['badges'])
                ? 0
                : (substr_count((string) $row['badges'], ',') + 1);
        }
        unset($row);
        usort($leaderboardRows, function ($a, $b) {
            return (($b['badge_count'] ?? 0) <=> ($a['badge_count'] ?? 0))
                ?: (($b['score_green'] ?? 0) <=> ($a['score_green'] ?? 0));
        });
        $leaderboard = array_slice($leaderboardRows, 0, 10);

        if ($idFiche) {
            $stmt = $this->pdo->prepare("SELECT * FROM fiche_entreprise WHERE id = :id");
            $stmt->execute([':id' => $idFiche]);
            $selectedFiche = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $projByFiche = [];
        foreach ($this->getAllProjets() as $pr) {
            $fid = (int) ($pr['id_fiche_entreprise'] ?? 0);
            if ($fid <= 0) {
                continue;
            }
            if (!isset($projByFiche[$fid])) {
                $projByFiche[$fid] = [];
            }
            $projByFiche[$fid][] = $pr;
        }

        if ($selectedFiche) {
            $fid = (int) $selectedFiche['id'];
            $projects = $projByFiche[$fid] ?? [];
            $bestIa = 0;
            foreach ($projects as $pr) {
                $bestIa = max($bestIa, (float) ($pr['score_ia'] ?? 0));
            }
            $hasAccepted = false;
            foreach ($projects as $pr) {
                if (($pr['statut'] ?? '') === 'accepté') {
                    $hasAccepted = true;
                    break;
                }
            }

            foreach ($catalog as $def) {
                if (call_user_func($def['test'], $selectedFiche, $projects, $bestIa, $hasAccepted, $projByFiche)) {
                    $ficheBadges[] = [
                        'id' => $def['id'],
                        'nom' => $def['nom'],
                        'description' => $def['description'],
                        'icone' => $def['icone'],
                        'couleur' => $def['couleur'],
                        'awarded_at' => date('Y-m-d H:i:s')
                    ];
                }
            }

            $total = count($catalog);
            $earned = count($ficheBadges);
            $progress = ['earned' => $earned, 'total' => $total, 'percent' => $total > 0 ? min(100, round($earned * 100 / $total)) : 0];

            foreach ($catalog as &$c) {
                $c = array_merge($c, ['id' => $c['id'], 'couleur' => $c['couleur'] ?? '#1D9E75']);
            }
            unset($c);
            $allBadges = array_map(static function ($c) {
                return [
                    'id' => $c['id'],
                    'nom' => $c['nom'],
                    'description' => $c['description'],
                    'unlock_hint' => $c['unlock_hint'] ?? $c['description'],
                    'icone' => $c['icone'],
                    'couleur' => $c['couleur']
                ];
            }, $catalog);
        } else {
            $allBadges = array_map(static function ($c) {
                return [
                    'id' => $c['id'],
                    'nom' => $c['nom'],
                    'description' => $c['description'],
                    'unlock_hint' => $c['unlock_hint'] ?? $c['description'],
                    'icone' => $c['icone'],
                    'couleur' => $c['couleur']
                ];
            }, $catalog);
            $progress = null;
        }

        require_once __DIR__ . '/../views/front/Advanced/badges.php';
    }

    /** @param list<int> $topFiveGreenIds
     * @return list<array<string,mixed>>
     */
    private function getBadgeCatalog(array $topFiveGreenIds = []): array
    {
        return [
            [
                'id' => 101,
                'nom' => 'Green Pioneer',
                'description' => 'Score green entreprise ≥ 70',
                'unlock_hint' => 'Rédigez une description centrée environnement/climat (+ mots verts : durable, renouvelable, CO₂…) jusqu’à ce que le score green de votre fiche atteigne 70 %.',
                'icone' => '🌱',
                'couleur' => '#16A34A',
                'test' => fn($f,$ps,$bestIa,$accept,$map) => (int)($f['score_green'] ?? 0) >= 70
            ],
            [
                'id' => 102,
                'nom' => 'Innovateur',
                'description' => 'Au moins un projet avec score IA ≥ 72',
                'unlock_hint' => 'Complétez la description projet (pitch + valeur) pour augmenter le score IA ; puis recalculez la page projet pour dépasser 72.',
                'icone' => '🚀',
                'couleur' => '#534AB7',
                'test' => fn($f,$ps,$bestIa,$accept,$map) => $bestIa >= 72
            ],
            [
                'id' => 103,
                'nom' => 'Marché Ready',
                'description' => 'Score IA moyen des projets de la fiche ≥ 65',
                'unlock_hint' => 'Créez au moins 2 projets détaillés pour que la moyenne de leurs scores IA dépasse 65.',
                'icone' => '💰',
                'couleur' => '#F59E0B',
                'test' => static function ($f, $projects, $_a, $_b, $_c) {
                    if (empty($projects)) {
                        return false;
                    }
                    $sum = 0;
                    foreach ($projects as $p) {
                        $sum += (float) ($p['score_ia'] ?? 0);
                    }
                    return ($sum / count($projects)) >= 65;
                }
            ],
            [
                'id' => 104,
                'nom' => 'Projet accepté',
                'description' => 'Un projet incubé avec statut accepté',
                'unlock_hint' => 'Depuis l’admin ou le statut projet, passez au moins un projet en « accepté » pour cette entreprise.',
                'icone' => '✅',
                'couleur' => '#0EA5E9',
                'test' => fn($f,$ps,$bestIa,$accept,$map) => $accept === true
            ],
            [
                'id' => 105,
                'nom' => 'Top cohorte',
                'description' => 'Entreprise dans le top 5 du score green',
                'unlock_hint' => 'Améliorez le score environnemental (description + impact mesurable) pour entrer parmi les 5 meilleurs green scores.',
                'icone' => '🏆',
                'couleur' => '#EF9F27',
                'test' => static function ($f, $ps, $bestIa, $accept, $map) use ($topFiveGreenIds) {
                    return in_array((int) ($f['id'] ?? 0), $topFiveGreenIds, true);
                }
            ],
            [
                'id' => 106,
                'nom' => 'Communauté active',
                'description' => 'Au moins 2 projets rattachés',
                'unlock_hint' => 'Ajoutez un second projet rattaché à cette fiche depuis « Projets » → « Nouveau ».',
                'icone' => '📈',
                'couleur' => '#EC4899',
                'test' => fn($f,$ps,$bestIa,$accept,$map) => count($ps) >= 2
            ]
        ];
    }

    // ─── CHATBOT (historique dans projet.chatbot_history) ────────────
    public function chatbot() {
        $projets = $this->getAllProjets();
        $history = [];
        $id_projet = isset($_GET['id_projet']) ? (int) $_GET['id_projet'] : null;
        if ($id_projet) {
            $p = $this->getProjetById($id_projet);
            if ($p && !empty($p['chatbot_history'])) {
                $decoded = json_decode($p['chatbot_history'], true);
                if (is_array($decoded)) {
                    foreach ($decoded as $row) {
                        if (is_array($row) && isset($row['role'], $row['message'])) {
                            $history[] = ['role' => $row['role'], 'message' => $row['message']];
                        }
                    }
                }
            }
        }
        require_once __DIR__ . '/../views/front/Advanced/chatbot.php';
    }

    public function chatbotApi() {
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode((string) file_get_contents('php://input'), true) ?: [];
        $message = trim((string) ($input['message'] ?? ''));
        $id_projet = isset($input['id_projet']) && $input['id_projet'] !== '' && $input['id_projet'] !== null
            ? (int) $input['id_projet']
            : null;
        $regenerate = $input['regenerate'] ?? false;
        
        if ($message === '' && !$regenerate) {
            echo json_encode(['error' => 'Message vide']);
            exit;
        }
        
        $p = null;
        $conversationHistory = [];
        $systemPrompt = "Tu es un expert entrepreneurship coach pour North African startups, spécialisé en Tunisie. Tu as une connaissance approfondie de:
- Stratégie startup, product-market fit, développement MVP
- Levée de fonds en Tunisie: BFPME, SICAR, Smart Capital, Flat6Labs, Africa Business Angels Network, ANPR grants
- Marketing, growth hacking, B2B/B2C sales
- Impact measurement, ESG, UN SDGs
- Tech startups, agritech, fintech, greentech

Tu es actuellement en train de coacher le fondateur d'une startup spécifique.";
        
        if ($id_projet) {
            $p = $this->getProjetById($id_projet);
            if ($p) {
                $bp = $this->composeScoreBlueprint($p);
                
                // Get conversation history
                $conversationHistory = $this->decodeJsonList($p['chatbot_history'] ?? null);
                if (!is_array($conversationHistory)) {
                    $conversationHistory = [];
                }
                
                // If regenerating, get the last user message
                if ($regenerate && !empty($conversationHistory)) {
                    $lastUserMessage = null;
                    for ($i = count($conversationHistory) - 1; $i >= 0; $i--) {
                        if ($conversationHistory[$i]['role'] === 'user') {
                            $lastUserMessage = $conversationHistory[$i]['message'];
                            break;
                        }
                    }
                    if ($lastUserMessage) {
                        $message = $lastUserMessage;
                        // Remove the last assistant response to regenerate
                        array_pop($conversationHistory);
                    }
                }
                
                $systemPrompt .= "

Projet: {$p['titre']}
Entreprise: {$p['entreprise_nom']} 
Secteur: {$p['entreprise_categorie']}
Description: " . substr($p['description'], 0, 500) . "
Incubateur: ImpactVenture

Scores actuels:
- Score Innovation: " . round($bp['innovation_score']) . "/100
- Score Durabilité: " . round($bp['sustainability_score']) . "/100  
- Score Marché: " . round($bp['market_score']) . "/100
- Score Risque: " . round($bp['risk_score']) . "/100
- Score Final: " . round($bp['final_score']) . "/100
Statut: " . ($p['statut'] ?? 'soumis') . "

RÈGLES STRICTES POUR TES RÉPONSES:
1. NE JAMAIS donner une liste générique qui pourrait s'appliquer à n'importe quelle startup
2. TOUJOURS mentionner la startup par son nom dans au moins une réponse
3. Sois conversationnel et direct — comme un mentor dans un café, pas un manuel
4. Si un score est inférieur à 50, mentionne-le proactivement
5. Varie le format: parfois prose, parfois liste courte, parfois question directe
6. Maximum 200 mots par réponse sauf si question complexe
7. Maximum 2 emojis par réponse, pas un par point
8. Pour le financement Tunisie, mentionne les programmes réels: BFPME, SICAR, Smart Capital, Flat6Labs Tunisia, Africa Business Angels Network
9. Souviens de tout ce qui a été dit précédemment dans cette conversation
10. Si on te pose une question hors entrepreneuriat, réponds-y quand même comme un assistant intelligent
11. Sois spécifique et donne des conseils actionnables";
            }
        }
        
        // Build messages array for AI
        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
        // Add conversation history (last 10 messages to avoid token limits)
        $recentHistory = array_slice($conversationHistory, -10);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['message']
            ];
        }
        
        // Add current message if not regenerating
        if (!$regenerate) {
            $messages[] = ['role' => 'user', 'content' => $message];
        }
        
        $response = callAI($message, $systemPrompt, $messages);

        if ($id_projet && $p) {
            $prev = $this->decodeJsonList($p['chatbot_history'] ?? null);
            if (!is_array($prev)) {
                $prev = [];
            }
            $prev[] = ['role' => 'user', 'message' => $message, 'ts' => time()];
            $prev[] = ['role' => 'assistant', 'message' => $response, 'ts' => time()];
            if (count($prev) > 40) {
                $prev = array_slice($prev, -40);
            }
            try {
                $stmt = $this->pdo->prepare('UPDATE projet SET chatbot_history = ? WHERE id_projet = ?');
                $stmt->execute([json_encode($prev, JSON_UNESCAPED_UNICODE), $id_projet]);
            } catch (Exception $e) { /* ignore */ }
        } elseif (!isset($_SESSION['iv_chat'])) {
            $_SESSION['iv_chat'] = [];
        }
        if (!$id_projet) {
            $_SESSION['iv_chat'][] = ['role' => 'user', 'message' => $message];
            $_SESSION['iv_chat'][] = ['role' => 'assistant', 'message' => $response];
            if (count($_SESSION['iv_chat']) > 40) {
                $_SESSION['iv_chat'] = array_slice($_SESSION['iv_chat'], -40);
            }
        }

        echo json_encode(['response' => $response], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ─── DASHBOARD ────────────────────────────────────────────────────
    public function dashboard() {
        $projets=$this->getAllProjets();
        $fiches=$this->getAllFiches();
        require_once __DIR__ . '/../views/front/Advanced/dashboard.php';
    }

    // ─── FOUNDER DASHBOARD ───────────────────────────────────────────────
    public function founderDashboard($projet_id) {
        if (!$projet_id) {
            header('Location: index.php?action=projet_list&msg=missing_id');
            exit;
        }

        $this->ensureProjetEnriched($projet_id);
        $this->reconcileProjetScores($projet_id);
        
        $projet = $this->getProjetById($projet_id);
        if (!$projet) {
            header('Location: index.php?action=projet_list&msg=notfound');
            exit;
        }

        // Get score history (last 5 snapshots)
        $this->ensureAdvancedTables();
        $stmt = $this->pdo->prepare('SELECT * FROM project_scores WHERE id_projet = ? ORDER BY created_at DESC LIMIT 5');
        $stmt->execute([$projet_id]);
        $scoreHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get top 3 matched mentors
        $mentors = $this->decodeJsonList($projet['mentor_suggestions'] ?? null);
        $topMentors = array_slice($mentors, 0, 3);

        // Get interested mentors count
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM mentor_interests WHERE projet_id = ? AND is_interested = 1');
        $stmt->execute([$projet_id]);
        $interestedMentorsCount = $stmt->fetchColumn() ?: 0;

        // Get recommendations
        $bp = $this->composeScoreBlueprint($projet);
        $recommendations = iv_build_next_step_recommendations(
            $bp['market_score'],
            $bp['innovation_score'], 
            $bp['sustainability_score'],
            $bp['risk_score']
        );

        require_once __DIR__ . '/../views/front/Advanced/founder_dashboard.php';
    }

    // ─── MENTOR INTEREST ───────────────────────────────────────────────
    public function mentorInterest() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
            return;
        }

        $mentorSlug = trim($_POST['mentor_slug'] ?? '');
        $projetId = (int)($_POST['projet_id'] ?? 0);

        if (empty($mentorSlug) || $projetId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
            return;
        }

        $this->ensureAdvancedTables();

        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO mentor_interests (mentor_slug, projet_id, is_interested, noted_at) 
                VALUES (?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE is_interested = 1, noted_at = NOW()
            ');
            $stmt->execute([$mentorSlug, $projetId]);

            echo json_encode(['success' => true, 'message' => 'Intérêt enregistré avec succès.']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
        }
    }

    // ─── VALIDATION ──────────────────────────────────────────────────
    private function valider($p) {
        $errors=[];
        if(empty(trim($p['titre']??''))) $errors[]="Le titre est obligatoire.";
        if(empty(trim($p['description']??''))) $errors[]="La description est obligatoire.";
        if(empty($p['id_fiche_entreprise']??'')) $errors[]="Veuillez sélectionner une entreprise.";
        return $errors;
    }
}
?>
