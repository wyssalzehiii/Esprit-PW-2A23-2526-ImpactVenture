<?php

class Projet {
    private $id_projet;
    private $id_fiche_entreprise;
    private $titre;
    private $description;
    private $budget_demande;
    private $statut;
    private $created_at;
    
    // AI and Advanced Data Fields
    private $pitch_score;
    private $sentiment_data;

    // Join Fields
    private $entreprise_nom;
    private $entreprise_categorie;
    private $score_green;
    private $mots_cles;

    public function __construct(
        $id_fiche_entreprise = null, 
        $titre = "", 
        $description = "", 
        $budget_demande = 0, 
        $statut = "Soumis"
    ) {
        $this->id_fiche_entreprise = $id_fiche_entreprise;
        $this->titre = $titre;
        $this->description = $description;
        $this->budget_demande = $budget_demande;
        $this->statut = $statut;
    }

    // Getters
    public function getIdProjet() { return $this->id_projet; }
    public function getIdFicheEntreprise() { return $this->id_fiche_entreprise; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getBudgetDemande() { return $this->budget_demande; }
    public function getStatut() { return $this->statut; }
    public function getCreatedAt() { return $this->created_at; }
    public function getPitchScore() { return $this->pitch_score; }
    public function getSentimentData() { return $this->sentiment_data; }
    public function getEntrepriseNom() { return $this->entreprise_nom; }
    public function getEntrepriseCategorie() { return $this->entreprise_categorie; }
    public function getScoreGreen() { return $this->score_green; }
    public function getMotsCles() { return $this->mots_cles; }

    // Setters
    public function setIdProjet($id_projet) { $this->id_projet = $id_projet; }
    public function setIdFicheEntreprise($id_fiche_entreprise) { $this->id_fiche_entreprise = $id_fiche_entreprise; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setBudgetDemande($budget_demande) { $this->budget_demande = $budget_demande; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setPitchScore($pitch_score) { $this->pitch_score = $pitch_score; }
    public function setSentimentData($sentiment_data) { $this->sentiment_data = $sentiment_data; }
    public function setEntrepriseNom($entreprise_nom) { $this->entreprise_nom = $entreprise_nom; }
    public function setEntrepriseCategorie($entreprise_categorie) { $this->entreprise_categorie = $entreprise_categorie; }
    public function setScoreGreen($score_green) { $this->score_green = $score_green; }
    public function setMotsCles($mots_cles) { $this->mots_cles = $mots_cles; }
}
?>
