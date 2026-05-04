<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../../config/ai_config.php';

class ChatbotModel extends BaseModel {

    public function chat($message, $id_projet = null, $id_user = 1) {
        // Sauvegarder message utilisateur
        $this->query("INSERT INTO chatbot_history (id_user, id_projet, role, message) VALUES (?, ?, 'user', ?)",
            [$id_user, $id_projet, $message]);

        // Contexte du projet si disponible
        $context = '';
        if ($id_projet) {
            $projet = $this->query("SELECT p.*, f.nom as ent_nom, f.categorie, f.description as ent_desc 
                FROM projet p LEFT JOIN fiche_entreprise f ON p.id_fiche_entreprise = f.id 
                WHERE p.id_projet = ?", [$id_projet])->fetch();
            if ($projet) {
                $context = "Contexte: Projet '{$projet['titre']}' de l'entreprise '{$projet['ent_nom']}' (catégorie: {$projet['categorie']}). Description: {$projet['description']}. ";
            }
        }

        $fullPrompt = $context . $message;
        $response = callAI($fullPrompt);

        // Sauvegarder réponse
        $this->query("INSERT INTO chatbot_history (id_user, id_projet, role, message) VALUES (?, ?, 'assistant', ?)",
            [$id_user, $id_projet, $response]);

        return $response;
    }

    public function getHistory($id_user = 1, $id_projet = null, $limit = 20) {
        if ($id_projet) {
            return $this->query("SELECT * FROM chatbot_history WHERE id_user = ? AND id_projet = ? ORDER BY created_at ASC LIMIT ?",
                [$id_user, $id_projet, $limit])->fetchAll();
        }
        return $this->query("SELECT * FROM chatbot_history WHERE id_user = ? ORDER BY created_at DESC LIMIT ?",
            [$id_user, $limit])->fetchAll();
    }

    public function clearHistory($id_user = 1) {
        $this->query("DELETE FROM chatbot_history WHERE id_user = ?", [$id_user]);
    }
}
