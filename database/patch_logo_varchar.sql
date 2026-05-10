-- À exécuter une fois si la base existante a encore logo VARCHAR(500)
ALTER TABLE fiche_entreprise MODIFY logo VARCHAR(2048) DEFAULT NULL;
