-- Create Table for Formation entity
-- Exact schema matching UML: 
-- - id (int PK auto-increment)
-- - title (varchar)
-- - content (text)

CREATE TABLE IF NOT EXISTS `formations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some dummy data to showcase the application out of the box
INSERT INTO `formations` (`title`, `content`) VALUES
('Web Development Basics', 'Introduction to HTML, CSS, and pure PHP MVC without frameworks. You will understand how everything works deeply under the hood without relying on black-box external wrappers.'),
('Advanced Object-Oriented PHP', 'Learn how to construct solid applications using PDO, interfaces, and architecture design patterns like pure MVC, singletons, and basic clean architecture implementations over relational databases.'),
('Impactful Social Ventures', 'This is a formation explaining how to launch a strong social business and measure its success. Understand the mechanisms of real world impact.');
