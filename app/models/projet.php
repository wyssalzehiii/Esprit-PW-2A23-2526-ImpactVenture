<?php
class Projet {
    private $id_projet;
    private $id_fiche_entreprise;
    private $id_user;
    private $titre;
    private $description;
    private $budget_demande;
    private $statut;
    private $score_ia;
    private $pitch_score;
    private $viability_score;
    private $sdg_tags;
    private $sentiment_data;
    private $created_at;
    private $date_soumission;

    // Fields from JOIN
    private $entreprise_nom;
    private $entreprise_categorie;
    private $score_green;
    private $mots_cles;
    private $ville;

    // Constructor
    public function __construct(
        $id_fiche_entreprise = null,
        $titre = "",
        $description = "",
        $budget_demande = 0,
        $statut = "soumis"
    ) {
        $this->id_fiche_entreprise = $id_fiche_entreprise;
        $this->titre = $titre;
        $this->description = $description;
        $this->budget_demande = $budget_demande;
        $this->statut = $statut;
    }

    // Getters
    public function getIdProjet()           { return $this->id_projet; }
    public function getIdFicheEntreprise()  { return $this->id_fiche_entreprise; }
    public function getIdUser()             { return $this->id_user; }
    public function getTitre()              { return $this->titre; }
    public function getDescription()        { return $this->description; }
    public function getBudgetDemande()       { return $this->budget_demande; }
    public function getStatut()             { return $this->statut; }
    public function getScoreIa()            { return $this->score_ia; }
    public function getPitchScore()         { return $this->pitch_score; }
    public function getViabilityScore()     { return $this->viability_score; }
    public function getSdgTags()            { return $this->sdg_tags; }
    public function getSentimentData()      { return $this->sentiment_data; }
    public function getCreatedAt()          { return $this->created_at; }
    public function getDateSoumission()     { return $this->date_soumission; }
    public function getEntrepriseNom()      { return $this->entreprise_nom; }
    public function getEntrepriseCategorie(){ return $this->entreprise_categorie; }
    public function getScoreGreen()         { return $this->score_green; }
    public function getMotsCles()           { return $this->mots_cles; }
    public function getVille()              { return $this->ville; }

    // Setters
    public function setIdProjet($id)                    { $this->id_projet = $id; }
    public function setIdFicheEntreprise($id)           { $this->id_fiche_entreprise = $id; }
    public function setIdUser($id)                      { $this->id_user = $id; }
    public function setTitre($titre)                    { $this->titre = $titre; }
    public function setDescription($description)        { $this->description = $description; }
    public function setBudgetDemande($budget)            { $this->budget_demande = $budget; }
    public function setStatut($statut)                  { $this->statut = $statut; }
    public function setScoreIa($score)                  { $this->score_ia = $score; }
    public function setPitchScore($score)               { $this->pitch_score = $score; }
    public function setViabilityScore($score)           { $this->viability_score = $score; }
    public function setSdgTags($tags)                   { $this->sdg_tags = $tags; }
    public function setSentimentData($data)             { $this->sentiment_data = $data; }
    public function setCreatedAt($date)                 { $this->created_at = $date; }
    public function setDateSoumission($date)            { $this->date_soumission = $date; }
    public function setEntrepriseNom($nom)              { $this->entreprise_nom = $nom; }
    public function setEntrepriseCategorie($cat)        { $this->entreprise_categorie = $cat; }
    public function setScoreGreen($score)               { $this->score_green = $score; }
    public function setMotsCles($mc)                    { $this->mots_cles = $mc; }
    public function setVille($ville)                    { $this->ville = $ville; }
}
?>