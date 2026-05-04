-- ============================================================
--  ImpactVenture – Module 02  (Projet + FicheEntreprise)
--  Import via phpMyAdmin > SQL > Exécuter
-- ============================================================

DROP TABLE IF EXISTS projet;
DROP TABLE IF EXISTS fiche_entreprise;

CREATE TABLE fiche_entreprise (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nom          VARCHAR(150) NOT NULL,
    description  TEXT         NOT NULL,
    categorie    VARCHAR(100) NOT NULL,
    mots_cles    VARCHAR(255) DEFAULT '',
    score_green  INT          DEFAULT 0,
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
    date_soumission      DATETIME     DEFAULT NOW(),
    FOREIGN KEY (id_fiche_entreprise) REFERENCES fiche_entreprise(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données de test
INSERT INTO fiche_entreprise (nom, description, categorie, mots_cles, score_green) VALUES
('Intelligence Artificielle TN',  'Projets autour de l IA et du machine learning en Tunisie', 'tech',                  'ia,ml,nlp',         70),
('Green Energy Africa',           'Énergie solaire, éolienne et renouvelable pour l Afrique',  'energie renouvelable',  'solaire,co2,vert',  90),
('AgriTech Green',                'Agriculture intelligente et durable',                        'agriculture durable',   'bio,capteurs,iot',  88),
('E-Commerce & Fintech',          'Commerce en ligne et paiement digital',                      'digital',               'ecommerce,paiement',55),
('Futur du Travail',              'Remote work, freelance et emploi digital en Tunisie',        'entrepreneuriat',       'remote,freelance',  50);

INSERT INTO projet (titre, description, id_fiche_entreprise, id_user, statut, score_ia) VALUES
('EcoDeliver',    'Livraison à vélo électrique à Tunis',         2, 1, 'accepté',      87.5),
('AgriSmart TN',  'Capteurs IoT pour agriculteurs tunisiens',    3, 1, 'en_evaluation', 72.0),
('PayEasy TN',    'Paiement mobile pour PME tunisiennes',        4, 1, 'accepté',      91.3);
