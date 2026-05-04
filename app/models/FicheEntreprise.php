<?php

class FicheEntreprise {
    private $id;
    private $nom;
    private $description;
    private $logo;
    private $categorie;
    private $mots_cles;
    private $score_green;
    private $badges;
    private $created_at;

    // Additional fields for JOINS
    private $nb_projets;
    private $latitude;
    private $longitude;

    public function __construct(
        $nom = "", 
        $description = "", 
        $logo = "", 
        $categorie = "", 
        $mots_cles = "", 
        $score_green = 0, 
        $badges = "", 
        $created_at = null
    ) {
        $this->nom = $nom;
        $this->description = $description;
        $this->logo = $logo;
        $this->categorie = $categorie;
        $this->mots_cles = $mots_cles;
        $this->score_green = $score_green;
        $this->badges = $badges;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getLogo() { return $this->logo; }
    public function getCategorie() { return $this->categorie; }
    public function getMotsCles() { return $this->mots_cles; }
    public function getScoreGreen() { return $this->score_green; }
    public function getBadges() { return $this->badges; }
    public function getCreatedAt() { return $this->created_at; }
    public function getNbProjets() { return $this->nb_projets; }
    public function getLatitude() { return $this->latitude; }
    public function getLongitude() { return $this->longitude; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setDescription($description) { $this->description = $description; }
    public function setLogo($logo) { $this->logo = $logo; }
    public function setCategorie($categorie) { $this->categorie = $categorie; }
    public function setMotsCles($mots_cles) { $this->mots_cles = $mots_cles; }
    public function setScoreGreen($score_green) { $this->score_green = $score_green; }
    public function setBadges($badges) { $this->badges = $badges; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setNbProjets($nb_projets) { $this->nb_projets = $nb_projets; }
    public function setLatitude($lat) { $this->latitude = $lat; }
    public function setLongitude($lng) { $this->longitude = $lng; }
}
?>