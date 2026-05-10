-- ============================================================
--  ImpactVenture – INTEGRATION SQL
--  Projet (Module 2) + Financement (Investisseurs)
--  ➜ Run this in phpMyAdmin > SQL > Execute
--  ➜ Run AFTER your existing module2.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ─── Drop financement tables if they exist (clean start) ───
DROP TABLE IF EXISTS demandes_financement;
DROP TABLE IF EXISTS investisseurs;

SET FOREIGN_KEY_CHECKS = 1;

-- ─── INVESTISSEURS TABLE ────────────────────────────────────
CREATE TABLE investisseurs (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(150) NOT NULL,
    organisation    VARCHAR(200) NOT NULL,
    secteur_focus   VARCHAR(150) DEFAULT 'Général',
    montant_min     DECIMAL(12,2) DEFAULT 0,
    montant_max     DECIMAL(12,2) DEFAULT 0,
    description     TEXT DEFAULT NULL,
    photo           VARCHAR(500) DEFAULT NULL,
    linkedin        VARCHAR(500) DEFAULT NULL,
    created_at      DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── DEMANDES FINANCEMENT TABLE ─────────────────────────────
-- Links fiche_entreprise (Projet module) → investisseurs (Financement module)
CREATE TABLE demandes_financement (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    fiche_entreprise_id INT NOT NULL,
    investisseur_id     INT NOT NULL,
    message             TEXT NOT NULL,
    montant_demande     DECIMAL(12,2) DEFAULT NULL,
    montant_investi     DECIMAL(12,2) DEFAULT NULL,
    currency            VARCHAR(10) DEFAULT 'TND',
    phone               VARCHAR(30) DEFAULT NULL,
    statut              ENUM('en_attente','accepte','refuse') DEFAULT 'en_attente',
    motif_refus         TEXT DEFAULT NULL,
    date_demande        DATETIME DEFAULT NOW(),
    date_financement    DATETIME DEFAULT NULL,
    FOREIGN KEY (fiche_entreprise_id) REFERENCES fiche_entreprise(id) ON DELETE CASCADE,
    FOREIGN KEY (investisseur_id) REFERENCES investisseurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── SAMPLE INVESTISSEURS DATA ──────────────────────────────
INSERT INTO investisseurs (nom, organisation, secteur_focus, montant_min, montant_max, description, photo, linkedin) VALUES
('Karim Mansouri',    'GreenFund MENA',        'energie renouvelable',  10000,  500000, 'Investisseur spécialisé dans les projets verts en Afrique du Nord.',       NULL, 'https://linkedin.com'),
('Sonia Belhadj',     'TechAngels Tunisia',    'tech',                   5000,  200000, 'Business angel focalisée sur les startups tech tunisiennes.',              NULL, 'https://linkedin.com'),
('Ahmed Trabelsi',    'AgriInvest Africa',     'agriculture durable',   20000,  750000, 'Fonds dédié à l''agriculture intelligente et durable en Afrique.',         NULL, 'https://linkedin.com'),
('Lina Khelifi',      'Digital Ventures TN',   'digital',               15000,  400000, 'Investissements dans le e-commerce et le fintech tunisien.',               NULL, 'https://linkedin.com'),
('Omar Chaabane',     'Future Work Capital',   'entrepreneuriat',        8000,  300000, 'Soutien aux projets de freelance et remote work en Tunisie.',              NULL, 'https://linkedin.com'),
('Yasmine Fourati',   'ImpactFirst Fund',      'Général',               25000, 1000000, 'Fonds d''impact investing ciblant les ODD en Afrique.',                    NULL, 'https://linkedin.com');

-- ─── SAMPLE DEMANDES DATA ───────────────────────────────────
-- Links existing fiche_entreprise IDs to investors
INSERT INTO demandes_financement (fiche_entreprise_id, investisseur_id, message, montant_demande, currency, statut) VALUES
(2, 1, 'Notre projet EcoDeliver s''inscrit parfaitement dans votre stratégie green. Nous cherchons 150 000 TND pour l''expansion.', 150000, 'TND', 'en_attente'),
(3, 3, 'AgriSmart TN développe des capteurs IoT révolutionnaires pour l''agriculture. Votre expertise serait un atout majeur.', 200000, 'TND', 'accepte'),
(4, 4, 'PayEasy TN veut démocratiser le paiement mobile pour les PME tunisiennes. Nous avons besoin de 80 000 TND.', 80000, 'TND', 'en_attente');

-- ─── VIEW: Projet + Financement combined ────────────────────
-- This view shows each project with its funding request status
CREATE OR REPLACE VIEW v_projets_financement AS
SELECT
    p.id_projet,
    p.titre,
    p.description,
    p.statut AS statut_projet,
    p.score_ia,
    p.viability_score,
    f.id AS fiche_id,
    f.nom AS entreprise_nom,
    f.categorie AS entreprise_categorie,
    f.budget_disponible,
    df.id AS demande_id,
    df.montant_demande,
    df.montant_investi,
    df.statut AS statut_financement,
    df.date_demande,
    inv.nom AS investisseur_nom,
    inv.organisation AS investisseur_organisation
FROM projet p
LEFT JOIN fiche_entreprise f ON f.id = p.id_fiche_entreprise
LEFT JOIN demandes_financement df ON df.fiche_entreprise_id = f.id
LEFT JOIN investisseurs inv ON inv.id = df.investisseur_id;

-- ============================================================
-- DONE! Your Projet and Financement modules are now linked.
-- Access financement at: localhost:8081/ImpactVenture/index.php?action=financement
-- ============================================================
