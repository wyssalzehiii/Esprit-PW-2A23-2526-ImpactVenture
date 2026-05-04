# Esprit-PW-2A23-2526-ImpactVenture

# ImpactVenture – Digital Incubator IA + Green
ImpactVenture est une plateforme web d'incubation digitale IA + Green pour entrepreneurs tunisiens,
développée dans le cadre du module Projet Technologies Web 2A à Esprit School of Engineering (2025–2026).

## Description

ImpactVenture est une application web de gestion et d'incubation de startups.

**Objectif :**
Aider les entrepreneurs tunisiens à transformer leurs idées en startups viables grâce à
l'intelligence artificielle, un score d'impact environnemental et une mise en relation avec des mentors experts.

**Problème résolu :**
35% des jeunes diplômés tunisiens sont au chômage. 1/3 des startups meurent faute d'accompagnement.
Il n'existe aucun incubateur IA + Green en Tunisie actuellement.

**Principales fonctionnalités :**
- Évaluation IA des projets (score de viabilité, d'innovation et de marché)
- Score Green (impact CO2 et social de chaque projet)
- Smart Theme Engine : création de thèmes personnalisés + score de similarité entre projets
- Mise en relation entrepreneur ↔ mentor expert
- Connexion avec des investisseurs à impact
- Formations certifiantes et système de badges numériques

## Table des Matières

- [Installation](#installation)
- [Utilisation](#utilisation)
- [Contributions](#contributions)
- [Licence](#licence)

## Installation

1. Clonez le repository :
```bash
git clone https://github.com/Esprit-PW-2A23/Esprit-PW-2A23-2026-ImpactVenture.git
cd Esprit-PW-2A23-2026-ImpactVenture
```

2. Si vous utilisez WAMP ou XAMPP :

* Placez le projet dans le dossier www (WAMP) ou htdocs (XAMPP).

* Démarrez Apache et MySQL depuis l'interface de WAMP/XAMPP.

* Accédez au projet via http://localhost/ImpactVenture.

3. Importez la base de données :

* Ouvrez [phpMyAdmin](http://localhost/phpmyadmin).

* Créez une base de données nommée `impactventure`.

* Importez le fichier `database/impactventure.sql`.

4. Configurez la connexion à la base de données dans `config/database.php` :
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'impactventure');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Utilisation

### Installation de PHP 8

Pour utiliser ce projet, vous devez installer PHP 8. Voici les étapes :

1. Téléchargez PHP à partir du site officiel : [PHP – Téléchargement](https://www.php.net/downloads.php).

2. Installez PHP en suivant les instructions spécifiques à votre système d'exploitation :

   - Pour **Windows**, vous pouvez utiliser [XAMPP](https://www.apachefriends.org/fr/index.html) ou [WampServer](https://www.wampserver.com/).
   - Pour **macOS**, vous pouvez utiliser [Homebrew](https://brew.sh/), puis exécuter la commande suivante dans le terminal :
```bash
brew install php
```
   - Pour **Linux**, vous pouvez installer PHP via le gestionnaire de paquets. Par exemple, sur Ubuntu :
```bash
sudo apt update
sudo apt install php8.0
```

3. Vérifiez l'installation de PHP en exécutant la commande suivante dans votre terminal :
```bash
php -v
```

### Accès à l'application

- **Back Office (Admin)** → http://localhost/ImpactVenture/public/back/
- **Front Office (Entrepreneur)** → http://localhost/ImpactVenture/public/

### Technologies utilisées

- **PHP 8** (architecture MVC, PDO uniquement — MySQLi interdit)
- **HTML5 / JavaScript** (côté client)
- **Tailwind CSS** (Front Office — interface entrepreneur)
- **AdminLTE + Bootstrap** (Back Office — dashboard administrateur)
- **MySQL** (base de données relationnelle)

## Contributions

Nous remercions tous ceux qui ont contribué à ce projet !

### Contributeurs

Les personnes suivantes ont contribué à ce projet en ajoutant des fonctionnalités,
en corrigeant des bugs ou en améliorant la documentation :

- [Wyssal Zehi](https://github.com/wyssal-zehi) – Smart Theme Engine (CRUD Thèmes + Score Similarité IA + Trending Dashboard)
- [Moetez Chkoundali](https://github.com/moetez-chkoundali) – Authentification & Gestion Profil utilisateur
- [Kacem Amine Akremi](https://github.com/kacem-akremi) – Soumission Projet & Évaluation IA
- [Mohamed Omar Barhoumi](https://github.com/omar-barhoumi) – Sessions Mentoring
- [Farouk Ben Khalaf](https://github.com/farouk-benkhalaf) – Financement & Investissement

Si vous souhaitez contribuer, suivez les étapes ci-dessous pour faire un **fork**,
créer une nouvelle branche et soumettre une **pull request**.

### Comment contribuer ?

1. **Forkez le projet** : Allez sur la page GitHub du projet et cliquez sur le bouton **Fork**
dans le coin supérieur droit pour créer une copie du projet dans votre propre compte GitHub.

2. **Clonez votre fork** : Clonez le fork sur votre machine locale :
```bash
git clone https://github.com/votre-utilisateur/Esprit-PW-2A23-2026-ImpactVenture.git
cd Esprit-PW-2A23-2026-ImpactVenture
```

3. **Créez une nouvelle branche** pour votre fonctionnalité :
```bash
git checkout -b feature/nom-de-la-fonctionnalite
```

4. **Effectuez vos modifications**, puis validez et poussez :
```bash
git add .
git commit -m "Ajout : description de la fonctionnalité"
git push origin feature/nom-de-la-fonctionnalite
```

5. **Soumettez une Pull Request** depuis GitHub.

## Licence

Ce projet est sous la licence **MIT**.
Pour plus de détails, consultez le fichier [LICENSE](./LICENSE).

### Détails sur la licence MIT

La licence MIT est une licence de logiciel libre permissive qui autorise toute personne
à utiliser, copier, modifier, fusionner, publier, distribuer, sous-licencier et vendre
des copies du logiciel, sous réserve d'inclure la notice de copyright originale.

---

*Developed at **Esprit School of Engineering – Tunisia** | Projet Technologies Web – 2A23 | 2025–2026*
