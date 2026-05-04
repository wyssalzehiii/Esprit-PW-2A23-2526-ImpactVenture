-- ============================================================
--  ImpactVenture – Module 02 ADVANCED (10 fonctionnalités)
--  Import via phpMyAdmin > SQL > Exécuter
-- ============================================================

-- ─── 1. Colonnes supplémentaires pour fiche_entreprise ───
ALTER TABLE fiche_entreprise 
  ADD COLUMN IF NOT EXISTS logo VARCHAR(500) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS badges TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS ville VARCHAR(100) DEFAULT 'Tunis',
  ADD COLUMN IF NOT EXISTS latitude DECIMAL(10,7) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS longitude DECIMAL(10,7) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS budget_disponible DECIMAL(12,2) DEFAULT 0;

-- ─── 2. Colonnes supplémentaires pour projet ───
ALTER TABLE projet
  ADD COLUMN IF NOT EXISTS budget_demande DECIMAL(12,2) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS taille_marche VARCHAR(50) DEFAULT 'moyen',
  ADD COLUMN IF NOT EXISTS cout_initial DECIMAL(12,2) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS niveau_concurrence VARCHAR(50) DEFAULT 'moyen',
  ADD COLUMN IF NOT EXISTS taille_equipe INT DEFAULT 1,
  ADD COLUMN IF NOT EXISTS modele_economique VARCHAR(100) DEFAULT '',
  ADD COLUMN IF NOT EXISTS viability_score FLOAT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS pitch_score FLOAT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS sentiment_data JSON DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS sdg_tags VARCHAR(500) DEFAULT NULL;

-- ─── 3. Table mentors ───
DROP TABLE IF EXISTS mentor;
CREATE TABLE mentor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    email VARCHAR(200) DEFAULT '',
    specialite VARCHAR(200) NOT NULL,
    secteurs VARCHAR(500) DEFAULT '',
    mots_cles VARCHAR(500) DEFAULT '',
    ville VARCHAR(100) DEFAULT 'Tunis',
    experience_annees INT DEFAULT 5,
    bio TEXT DEFAULT NULL,
    disponible TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 4. Table investisseurs ───
DROP TABLE IF EXISTS investisseur;
CREATE TABLE investisseur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    email VARCHAR(200) DEFAULT '',
    type_investissement VARCHAR(100) DEFAULT 'seed',
    secteurs VARCHAR(500) DEFAULT '',
    mots_cles VARCHAR(500) DEFAULT '',
    ville VARCHAR(100) DEFAULT 'Tunis',
    budget_min DECIMAL(12,2) DEFAULT 0,
    budget_max DECIMAL(12,2) DEFAULT 100000,
    bio TEXT DEFAULT NULL,
    actif TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 5. Table badges ───
DROP TABLE IF EXISTS user_badges;
DROP TABLE IF EXISTS badge;
CREATE TABLE badge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(300) DEFAULT '',
    icone VARCHAR(10) DEFAULT '🏆',
    condition_type VARCHAR(50) NOT NULL,
    condition_value INT DEFAULT 0,
    couleur VARCHAR(20) DEFAULT '#1D9E75',
    created_at DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_fiche_entreprise INT NOT NULL,
    id_badge INT NOT NULL,
    awarded_at DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_fiche_entreprise) REFERENCES fiche_entreprise(id) ON DELETE CASCADE,
    FOREIGN KEY (id_badge) REFERENCES badge(id) ON DELETE CASCADE,
    UNIQUE KEY unique_badge (id_fiche_entreprise, id_badge)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 6. Table historique chatbot ───
DROP TABLE IF EXISTS chatbot_history;
CREATE TABLE chatbot_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT DEFAULT 1,
    id_projet INT DEFAULT NULL,
    role ENUM('user','assistant') NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 7. Table business plans générés ───
DROP TABLE IF EXISTS business_plan;
CREATE TABLE business_plan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT NOT NULL,
    contenu TEXT NOT NULL,
    fichier_pdf VARCHAR(300) DEFAULT NULL,
    created_at DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_projet) REFERENCES projet(id_projet) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 8. Table pitch decks générés ───
DROP TABLE IF EXISTS pitch_deck;
CREATE TABLE pitch_deck (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT NOT NULL,
    slides JSON NOT NULL,
    fichier_pdf VARCHAR(300) DEFAULT NULL,
    created_at DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_projet) REFERENCES projet(id_projet) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 9. Table ODD (SDG) ───
DROP TABLE IF EXISTS sdg;
CREATE TABLE sdg (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    nom VARCHAR(200) NOT NULL,
    description_fr VARCHAR(500) DEFAULT '',
    mots_cles TEXT NOT NULL,
    icone VARCHAR(10) DEFAULT '🌍',
    couleur VARCHAR(20) DEFAULT '#1D9E75'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 10. Table CO2 (si pas existante) ───
CREATE TABLE IF NOT EXISTS co2_calculation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT DEFAULT 1,
    id_fiche_entreprise INT DEFAULT NULL,
    transport_km FLOAT DEFAULT 0,
    energie_kwh FLOAT DEFAULT 0,
    dechets_kg FLOAT DEFAULT 0,
    eau_m3 FLOAT DEFAULT 0,
    total_co2_kg FLOAT DEFAULT 0,
    created_at DATETIME DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 11. Table viability scores history ───
DROP TABLE IF EXISTS viability_history;
CREATE TABLE viability_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_projet INT NOT NULL,
    score FLOAT NOT NULL,
    details JSON DEFAULT NULL,
    created_at DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_projet) REFERENCES projet(id_projet) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  DONNÉES DE TEST
-- ============================================================

-- Mentors
INSERT INTO mentor (nom, email, specialite, secteurs, mots_cles, ville, experience_annees, bio) VALUES
('Dr. Ahmed Ben Ali',      'ahmed@mentor.tn',   'Intelligence Artificielle', 'tech,digital',              'ia,ml,deep learning,data',           'Tunis',  15, 'Expert IA avec 15 ans d''expérience dans le secteur tech tunisien.'),
('Sonia Gharbi',           'sonia@mentor.tn',   'Énergie Renouvelable',     'energie renouvelable',       'solaire,éolien,green,durable',       'Sfax',   10, 'Consultante en transition énergétique pour l''Afrique du Nord.'),
('Karim Trabelsi',         'karim@mentor.tn',   'Finance & Startup',        'digital,entrepreneuriat',    'fintech,levée de fonds,business',    'Tunis',   8, 'Serial entrepreneur et mentor de startups fintech.'),
('Fatma Mhiri',            'fatma@mentor.tn',   'Agriculture Durable',      'agriculture durable',        'bio,agritech,capteurs,iot',          'Sousse', 12, 'Ingénieur agronome spécialisée en agriculture de précision.'),
('Mohamed Jebali',         'mohamed@mentor.tn', 'Marketing Digital',        'digital,entrepreneuriat',    'marketing,growth,seo,branding',      'Tunis',   7, 'Expert en growth hacking et stratégie digitale.');

-- Investisseurs
INSERT INTO investisseur (nom, email, type_investissement, secteurs, mots_cles, ville, budget_min, budget_max, bio) VALUES
('Tunisian Angels Fund',   'contact@taf.tn',    'seed',         'tech,digital,entrepreneuriat',       'startup,innovation,seed',              'Tunis',   5000,   50000,  'Fonds d''investissement early-stage pour startups tunisiennes.'),
('Green Africa Capital',   'invest@gac.tn',     'serie_a',      'energie renouvelable,agriculture durable', 'green,impact,durable,environnement', 'Tunis',  50000,  500000,  'Capital risque spécialisé projets à impact environnemental.'),
('Impact Ventures MENA',   'hello@ivm.tn',      'seed',         'tech,sante,education',               'impact,social,innovation',             'Sfax',   10000,  100000,  'Investisseur à impact social pour la région MENA.'),
('Digital Bridge Fund',    'info@dbf.tn',       'serie_a',      'digital,tech',                        'ecommerce,fintech,saas,platform',     'Tunis',  25000,  250000,  'Fonds spécialisé dans la transformation digitale en Tunisie.'),
('AgriTech Investors TN',  'agri@invest.tn',    'seed',         'agriculture durable',                 'bio,agritech,food,durable',           'Sousse', 10000,  150000,  'Investisseurs passionnés par l''agriculture innovante.');

-- Badges
INSERT INTO badge (nom, description, icone, condition_type, condition_value, couleur) VALUES
('Eco Warrior',        'Score Green supérieur à 80%',          '🌿', 'green_score',    80, '#1D9E75'),
('Pitch Master',       'Pitch Quality Score supérieur à 70',   '🎯', 'pitch_score',    70, '#534AB7'),
('Consistency King',   '5 projets ou plus publiés',            '👑', 'project_count',   5, '#EF9F27'),
('Impact Pioneer',     'Projet accepté',                       '🚀', 'project_accepted', 1, '#3B82F6'),
('Green Innovator',    'Score IA supérieur à 85',              '💡', 'score_ia',       85, '#10B981'),
('SDG Champion',       '3 ODD détectés ou plus',               '🌍', 'sdg_count',       3, '#F59E0B'),
('Viable Startup',     'Score de viabilité supérieur à 70',    '📊', 'viability_score', 70, '#8B5CF6'),
('CO2 Reducer',        'Impact CO2 calculé',                   '♻️', 'co2_calculated',  1, '#06B6D4');

-- ODD (17 objectifs de développement durable)
INSERT INTO sdg (numero, nom, description_fr, mots_cles, icone, couleur) VALUES
(1,  'Pas de pauvreté',            'Éliminer la pauvreté sous toutes ses formes',                'pauvreté,revenu,social,inclusion,microfinance,emploi',                       '🏚️', '#E5243B'),
(2,  'Faim zéro',                  'Éliminer la faim et promouvoir l''agriculture durable',       'faim,alimentation,agriculture,nourriture,bio,food,sécurité alimentaire',    '🍽️', '#DDA63A'),
(3,  'Bonne santé',                'Assurer une vie saine pour tous',                             'santé,médical,hôpital,maladie,bien-être,pharma,télémédecine',               '❤️', '#4C9F38'),
(4,  'Éducation de qualité',       'Assurer l''accès à une éducation de qualité',                 'éducation,école,formation,apprentissage,e-learning,université,tutoring',     '📚', '#C5192D'),
(5,  'Égalité des sexes',          'Parvenir à l''égalité des sexes',                             'genre,femme,égalité,inclusion,diversité,empowerment',                       '⚧️', '#FF3A21'),
(6,  'Eau propre',                 'Garantir l''accès à l''eau et assainissement',                 'eau,assainissement,irrigation,potable,hydrique,filtre',                     '💧', '#26BDE2'),
(7,  'Énergie propre',             'Garantir l''accès à une énergie durable',                     'énergie,solaire,éolien,renouvelable,électricité,green,batterie,photovoltaïque', '⚡', '#FCC30B'),
(8,  'Travail décent',             'Promouvoir une croissance économique durable',                'emploi,travail,économie,startup,entrepreneuriat,PME,croissance',            '💼', '#A21942'),
(9,  'Industrie et innovation',    'Bâtir une infrastructure résiliente',                         'innovation,industrie,technologie,infrastructure,R&D,digital,numérique',     '🏭', '#FD6925'),
(10, 'Inégalités réduites',        'Réduire les inégalités',                                      'inégalité,inclusion,accessibilité,handicap,social,équité',                  '⚖️', '#DD1367'),
(11, 'Villes durables',            'Rendre les villes inclusives et durables',                    'ville,urbain,transport,mobilité,smart city,logement,infrastructure',        '🏙️', '#FD9D24'),
(12, 'Consommation responsable',   'Établir des modes de consommation durables',                  'recyclage,déchet,consommation,circulaire,zéro déchet,durable,réutilisation', '♻️', '#BF8B2E'),
(13, 'Action climatique',          'Lutter contre les changements climatiques',                   'climat,co2,carbone,émission,réchauffement,environnement,green',            '🌡️', '#3F7E44'),
(14, 'Vie aquatique',              'Conserver les océans et ressources marines',                  'océan,mer,poisson,aquaculture,maritime,marine,pollution marine',            '🐟', '#0A97D9'),
(15, 'Vie terrestre',              'Préserver les écosystèmes terrestres',                        'forêt,biodiversité,terre,écosystème,faune,flore,reforestation',             '🌳', '#56C02B'),
(16, 'Paix et justice',            'Promouvoir la paix et la justice',                            'paix,justice,gouvernance,transparence,corruption,droits,démocratie',        '🕊️', '#00689D'),
(17, 'Partenariats',               'Renforcer les partenariats mondiaux',                         'partenariat,coopération,international,réseau,collaboration,ONG',            '🤝', '#19486A');

-- Mise à jour des coordonnées GPS des entreprises existantes
UPDATE fiche_entreprise SET ville = 'Tunis',  latitude = 36.8065, longitude = 10.1815 WHERE id = 1;
UPDATE fiche_entreprise SET ville = 'Sfax',   latitude = 34.7406, longitude = 10.7603 WHERE id = 2;
UPDATE fiche_entreprise SET ville = 'Sousse', latitude = 35.8256, longitude = 10.6084 WHERE id = 3;
UPDATE fiche_entreprise SET ville = 'Tunis',  latitude = 36.8190, longitude = 10.1658 WHERE id = 4;
UPDATE fiche_entreprise SET ville = 'Tunis',  latitude = 36.7948, longitude = 10.1827 WHERE id = 5;
