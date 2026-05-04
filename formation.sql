-- Create Table for Formation entity
-- Exact schema matching UML: 
-- - id (int PK auto-increment)
-- - title (varchar)
-- - content (text)
-- - categorie (varchar)

CREATE TABLE IF NOT EXISTS `formations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `categorie` varchar(100) NOT NULL DEFAULT 'General',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alter table to add categorie if not exists (for existing databases)
-- ALTER TABLE formations ADD COLUMN categorie VARCHAR(100) NOT NULL DEFAULT 'General';

-- Create badges table
CREATE TABLE IF NOT EXISTS badges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  points INT NOT NULL DEFAULT 0,
  formation_id INT NOT NULL,
  FOREIGN KEY (formation_id) REFERENCES formations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create user_badges table
CREATE TABLE IF NOT EXISTS user_badges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  badge_id INT NOT NULL,
  awarded_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create projects table
CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  description TEXT,
  linked_to_path TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alter table to add linked_to_path if not exists (for existing databases)
ALTER TABLE projects ADD COLUMN IF NOT EXISTS linked_to_path TINYINT(1) DEFAULT 0;

-- Insert some dummy data to showcase the application out of the box
INSERT INTO `formations` (`title`, `content`, `categorie`) VALUES
('Web Development Basics', 'Introduction to HTML, CSS, and pure PHP MVC without frameworks. You will understand how everything works deeply under the hood without relying on black-box external wrappers.', 'General'),
('Advanced Object-Oriented PHP', 'Learn how to construct solid applications using PDO, interfaces, and architecture design patterns like pure MVC, singletons, and basic clean architecture implementations over relational databases.', 'AI'),
('Impactful Social Ventures', 'This is a formation explaining how to launch a strong social business and measure its success. Understand the mechanisms of real world impact.', 'General');
