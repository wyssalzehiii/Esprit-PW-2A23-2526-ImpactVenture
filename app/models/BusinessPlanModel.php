<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../../config/ai_config.php';

class BusinessPlanModel extends BaseModel {

    /**
     * Générer un business plan pour un projet
     */
    public function generate($projet) {
        $prompt = "Génère un business plan structuré en JSON pour le projet: \"{$projet['titre']}\". Description: \"{$projet['description']}\". Catégorie: \"{$projet['entreprise_categorie']}\".";
        
        $result = callAI($prompt);
        $data = json_decode($result, true);
        
        if (!$data) {
            $data = ['erreur' => 'Impossible de générer le business plan.'];
        }

        // Sauvegarder en DB
        $this->query("
            INSERT INTO business_plan (id_projet, contenu, created_at) VALUES (?, ?, NOW())
        ", [$projet['id_projet'], $result]);

        return $data;
    }

    /**
     * Récupérer le dernier business plan d'un projet
     */
    public function getByProjet($id_projet) {
        $bp = $this->query("
            SELECT * FROM business_plan WHERE id_projet = ? ORDER BY created_at DESC LIMIT 1
        ", [$id_projet])->fetch();
        
        if ($bp) {
            $bp['data'] = json_decode($bp['contenu'], true);
        }
        return $bp;
    }

    /**
     * Générer le PDF du business plan (HTML → téléchargement)
     */
    public function generatePDF($data, $projet) {
        $html = $this->buildPDFHtml($data, $projet);
        return $html; // Retourner le HTML pour affichage/print
    }

    private function buildPDFHtml($data, $projet) {
        $titre = htmlspecialchars($projet['titre'] ?? 'Projet');
        $html = "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Business Plan - $titre</title>";
        $html .= "<style>
            body{font-family:Arial,sans-serif;margin:40px;color:#333;line-height:1.6;}
            h1{color:#1D9E75;border-bottom:3px solid #1D9E75;padding-bottom:10px;}
            h2{color:#534AB7;margin-top:30px;}
            .header{text-align:center;margin-bottom:40px;}
            .section{margin-bottom:25px;padding:15px;background:#f9fafb;border-radius:8px;}
            .swot-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;}
            .swot-box{padding:15px;border-radius:8px;}
            .forces{background:#d1fae5;} .faiblesses{background:#fde8e8;}
            .opportunites{background:#dbeafe;} .menaces{background:#fef3c7;}
            table{width:100%;border-collapse:collapse;margin:10px 0;}
            td,th{padding:8px 12px;border:1px solid #e5e7eb;text-align:left;}
            th{background:#1D9E75;color:white;}
            @media print{body{margin:20px;} .no-print{display:none;}}
        </style></head><body>";
        
        $html .= "<div class='header'><h1>📋 Business Plan</h1><h2 style='color:#1D9E75'>$titre</h2>";
        $html .= "<p>Généré par ImpactVenture IA — " . date('d/m/Y') . "</p></div>";

        if (isset($data['resume_executif'])) {
            $html .= "<div class='section'><h2>1. Résumé Exécutif</h2><p>" . nl2br(htmlspecialchars($data['resume_executif'])) . "</p></div>";
        }
        if (isset($data['probleme'])) {
            $html .= "<div class='section'><h2>2. Problème Identifié</h2><p>" . nl2br(htmlspecialchars($data['probleme'])) . "</p></div>";
        }
        if (isset($data['solution'])) {
            $html .= "<div class='section'><h2>3. Solution Proposée</h2><p>" . nl2br(htmlspecialchars($data['solution'])) . "</p></div>";
        }
        if (isset($data['marche'])) {
            $html .= "<div class='section'><h2>4. Analyse de Marché</h2><p>" . nl2br(htmlspecialchars($data['marche'])) . "</p></div>";
        }
        if (isset($data['modele_economique'])) {
            $html .= "<div class='section'><h2>5. Modèle Économique</h2><p>" . nl2br(htmlspecialchars($data['modele_economique'])) . "</p></div>";
        }
        if (isset($data['swot'])) {
            $html .= "<div class='section'><h2>6. Analyse SWOT</h2><div class='swot-grid'>";
            $html .= "<div class='swot-box forces'><strong>💪 Forces</strong><ul>";
            foreach ($data['swot']['forces'] ?? [] as $item) $html .= "<li>" . htmlspecialchars($item) . "</li>";
            $html .= "</ul></div>";
            $html .= "<div class='swot-box faiblesses'><strong>⚠️ Faiblesses</strong><ul>";
            foreach ($data['swot']['faiblesses'] ?? [] as $item) $html .= "<li>" . htmlspecialchars($item) . "</li>";
            $html .= "</ul></div>";
            $html .= "<div class='swot-box opportunites'><strong>🚀 Opportunités</strong><ul>";
            foreach ($data['swot']['opportunites'] ?? [] as $item) $html .= "<li>" . htmlspecialchars($item) . "</li>";
            $html .= "</ul></div>";
            $html .= "<div class='swot-box menaces'><strong>🔴 Menaces</strong><ul>";
            foreach ($data['swot']['menaces'] ?? [] as $item) $html .= "<li>" . htmlspecialchars($item) . "</li>";
            $html .= "</ul></div></div></div>";
        }
        if (isset($data['plan_financier'])) {
            $html .= "<div class='section'><h2>7. Plan Financier</h2><table>";
            $html .= "<tr><th>Indicateur</th><th>Valeur</th></tr>";
            foreach ($data['plan_financier'] as $k => $v) {
                $label = ucfirst(str_replace('_', ' ', $k));
                $html .= "<tr><td>$label</td><td><strong>" . htmlspecialchars($v) . "</strong></td></tr>";
            }
            $html .= "</table></div>";
        }
        if (isset($data['equipe'])) {
            $html .= "<div class='section'><h2>8. Équipe</h2><p>" . nl2br(htmlspecialchars($data['equipe'])) . "</p></div>";
        }
        if (isset($data['timeline'])) {
            $html .= "<div class='section'><h2>9. Timeline</h2><table>";
            foreach ($data['timeline'] as $period => $task) {
                $html .= "<tr><td><strong>" . htmlspecialchars($period) . "</strong></td><td>" . htmlspecialchars($task) . "</td></tr>";
            }
            $html .= "</table></div>";
        }

        $html .= "<div style='text-align:center;margin-top:40px;color:#999;font-size:12px;'>ImpactVenture — Plateforme d'Impact Entrepreneurial — " . date('Y') . "</div>";
        $html .= "</body></html>";
        return $html;
    }

    public function getHistory($id_projet) {
        return $this->query("
            SELECT * FROM business_plan WHERE id_projet = ? ORDER BY created_at DESC
        ", [$id_projet])->fetchAll();
    }
}
