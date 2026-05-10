-- ============================================================
--  ImpactVenture – Module 02  (Projet + FicheEntreprise)
--  Import via phpMyAdmin > SQL > Exécuter
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS viability_history;
DROP TABLE IF EXISTS user_badges;
DROP TABLE IF EXISTS badge;
DROP TABLE IF EXISTS pitch_deck;
DROP TABLE IF EXISTS business_plan;
DROP TABLE IF EXISTS chatbot_history;
DROP TABLE IF EXISTS co2_calculation;
DROP TABLE IF EXISTS sdg;
DROP TABLE IF EXISTS mentor;
DROP TABLE IF EXISTS investisseur;
DROP TABLE IF EXISTS projet;
DROP TABLE IF EXISTS fiche_entreprise;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE fiche_entreprise (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(150) NOT NULL,
    description  TEXT         NOT NULL,
    logo         VARCHAR(2048) DEFAULT NULL,
    categorie    VARCHAR(100) NOT NULL,
    mots_cles    VARCHAR(255) DEFAULT '',
    score_green  INT          DEFAULT 0,
    badges       TEXT DEFAULT NULL,
    ville        VARCHAR(100) DEFAULT 'Tunis',
    latitude     DECIMAL(10,7) DEFAULT NULL,
    longitude    DECIMAL(10,7) DEFAULT NULL,
    budget_disponible DECIMAL(12,2) DEFAULT 0,
    co2_total_kg FLOAT DEFAULT 0,
    created_at   DATETIME     DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE projet (
    id_projet            INT AUTO_INCREMENT PRIMARY KEY,
    titre                VARCHAR(200) NOT NULL,
    description          TEXT         NOT NULL,
    id_fiche_entreprise  INT          NOT NULL,
    id_user              INT          DEFAULT 1,
    statut               ENUM('soumis','en_evaluation','accepté','rejeté') DEFAULT 'soumis',
    score_ia             FLOAT        DEFAULT NULL,
    budget_demande       DECIMAL(12,2) DEFAULT 0,
    taille_marche        VARCHAR(50) DEFAULT 'moyen',
    cout_initial         DECIMAL(12,2) DEFAULT 0,
    niveau_concurrence   VARCHAR(50) DEFAULT 'moyen',
    taille_equipe        INT DEFAULT 1,
    modele_economique    VARCHAR(100) DEFAULT '',
    viability_score      FLOAT DEFAULT NULL,
    pitch_score          FLOAT DEFAULT NULL,
    sentiment_data       JSON DEFAULT NULL,
    sdg_tags             VARCHAR(500) DEFAULT NULL,
    business_plan_text   LONGTEXT DEFAULT NULL,
    pitch_deck_json      JSON DEFAULT NULL,
    mentor_suggestions   JSON DEFAULT NULL,
    investor_suggestions JSON DEFAULT NULL,
    chatbot_history      LONGTEXT DEFAULT NULL,
    date_soumission      DATETIME     DEFAULT NOW(),
    FOREIGN KEY (id_fiche_entreprise) REFERENCES fiche_entreprise(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE project_scores (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_projet       INT          NOT NULL,
    final_score     FLOAT        NOT NULL,
    delta_pts       FLOAT        DEFAULT 0,
    change_reason   VARCHAR(255) DEFAULT NULL,
    market_score    FLOAT        DEFAULT NULL,
    innovation_score FLOAT       DEFAULT NULL,
    sustainability_score FLOAT   DEFAULT NULL,
    risk_score      FLOAT        DEFAULT NULL,
    snapshot_json   JSON         DEFAULT NULL,
    created_at      DATETIME     DEFAULT NOW(),
    KEY idx_proj_created (id_projet, created_at),
    CONSTRAINT fk_proj_scores_proj FOREIGN KEY (id_projet) REFERENCES projet(id_projet) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE mentor_interests (
    mentor_slug VARCHAR(128) NOT NULL,
    projet_id   INT NOT NULL,
    is_interested TINYINT(1) NOT NULL DEFAULT 0,
    noted_at    DATETIME DEFAULT NULL,
    PRIMARY KEY (mentor_slug, projet_id),
    KEY idx_mi_projet_interest (projet_id, is_interested),
    CONSTRAINT fk_mi_projet FOREIGN KEY (projet_id) REFERENCES projet(id_projet) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de test
INSERT INTO fiche_entreprise (nom, description, logo, categorie, mots_cles, score_green, badges, ville, latitude, longitude, budget_disponible, co2_total_kg) VALUES
('Intelligence Artificielle TN',  'Projets autour de l IA et du machine learning en Tunisie', NULL, 'tech',                  'ia,ml,nlp',         70, NULL, 'Tunis', 36.8065, 10.1815, 120000, 0),
('Green Energy Africa',           'Énergie solaire, éolienne et renouvelable pour l Afrique',  NULL, 'energie renouvelable',  'solaire,co2,vert',  90, 'Eco Warrior', 'Sfax', 34.7406, 10.7603, 180000, 240.5),
('AgriTech Green',                'Agriculture intelligente et durable',                        NULL, 'agriculture durable',   'bio,capteurs,iot',  88, NULL, 'Sousse', 35.8256, 10.6084, 95000, 160.3),
('E-Commerce & Fintech',          'Commerce en ligne et paiement digital',                      NULL, 'digital',               'ecommerce,paiement',55, NULL, 'Tunis', 36.8190, 10.1658, 70000, 420.7),
('Futur du Travail',              'Remote work, freelance et emploi digital en Tunisie',        NULL, 'entrepreneuriat',       'remote,freelance',  50, NULL, 'Tunis', 36.7948, 10.1827, 50000, 0);

INSERT INTO projet (titre, description, id_fiche_entreprise, id_user, statut, score_ia) VALUES
('EcoDeliver',    'Livraison à vélo électrique à Tunis',         2, 1, 'accepté',      87.5),
('AgriSmart TN',  'Capteurs IoT pour agriculteurs tunisiens',    3, 1, 'en_evaluation', 72.0),
('PayEasy TN',    'Paiement mobile pour PME tunisiennes',        4, 1, 'accepté',      91.3);
