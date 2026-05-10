-- À exécuter sur une base déjà créée avant ce module (ajout sécuritaire).
CREATE TABLE IF NOT EXISTS project_scores (
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
    CONSTRAINT fk_proj_scores_proj_upgrade FOREIGN KEY (id_projet) REFERENCES projet(id_projet) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mentor_interests (
    mentor_slug VARCHAR(128) NOT NULL,
    projet_id   INT NOT NULL,
    is_interested TINYINT(1) NOT NULL DEFAULT 0,
    noted_at    DATETIME DEFAULT NULL,
    PRIMARY KEY (mentor_slug, projet_id),
    KEY idx_mi_projet_interest (projet_id, is_interested),
    CONSTRAINT fk_mi_projet_upgrade FOREIGN KEY (projet_id) REFERENCES projet(id_projet) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
