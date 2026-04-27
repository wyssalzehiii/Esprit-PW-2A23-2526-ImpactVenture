-- Coller dans phpMyAdmin > SQL > Exécuter
-- Supprime et recrée les tables proprement

DROP TABLE IF EXISTS projet;
DROP TABLE IF EXISTS FicheEntreprise;

CREATE TABLE FicheEntreprise (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    categorie   VARCHAR(100) NOT NULL,
    mots_cles   VARCHAR(255) DEFAULT '',
    score_green INT DEFAULT 0,
    created_at  DATETIME DEFAULT NOW()
);

CREATE TABLE projet (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    theme_id    INT NOT NULL,
    statut      ENUM('en_attente','approuve','rejete') DEFAULT 'en_attente',
    created_at  DATETIME DEFAULT NOW(),
    FOREIGN KEY (theme_id) REFERENCES theme(id) ON DELETE CASCADE
);

INSERT INTO FicheEntreprise (nom, description, categorie, mots_cles, score_green) VALUES
('Intelligence Artificielle', 'Projets autour de l IA et du machine learning', 'tech', 'ia,ml,nlp', 70),
('Green Energy', 'Energie solaire, eolienne et renouvelable', 'energie renouvelable', 'solaire,co2,vert', 90),
('AgriTech Green', 'Agriculture intelligente et durable', 'agriculture durable', 'bio,capteurs,iot', 88),
('E-Commerce & Fintech', 'Commerce en ligne et paiement digital', 'digital', 'ecommerce,paiement', 55),
('Futur du Travail', 'Remote work, freelance et emploi digital', 'entrepreneuriat', 'remote,freelance', 50);

INSERT INTO projet (nom, description, theme_id, statut) VALUES
('EcoDeliver', 'Livraison a velo electrique a Tunis', 2, 'approuve'),
('AgriSmart TN', 'Capteurs IoT pour agriculteurs', 3, 'en_attente'),
('PayEasy TN', 'Paiement mobile pour PME', 4, 'approuve');
