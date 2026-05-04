# Formation Module (ImpactVenture)

This folder contains the complete and functional CRUD for the `Formation` entity for the ImpactVenture school project.

## Constraints Respected
- **Pure PHP & MVC Pattern:** No external wrappers or frameworks. Clean Models, Views, Controllers.
- **PDO Only:** Strictly uses safe Prepared Statements for every query (`$stmt->bindParam`). There is no raw SQL variables injection.
- **Server-Side Validation:** `novalidate` tag implemented across all HTML forms. All validation is managed inside `FormationController` and directly pumps explicit errors via invalid-feedback divs into forms.
- **BackOffice & FrontOffice Structure:** Clean folder structures ensuring front and admin parts don't mix templates.
- **Compliance to UML:** Database table only carries `id`, `title`, `content`.

## File Organization Strategy
- `/config/database.php` - Secure PDO connection singleton.
- `/models/Formation.php` - Class handling db queries and representing the entity.
- `/controllers/FormationController.php` - Action dispatcher methods. Handles requests and orchestrates logic.
- `/views/backoffice/` - Forms, grids, and operations templates.
- `/views/frontoffice/` - Display endpoints.
- `/includes/` - Template wrappers for HTML.

## Setup Instructions
1. Run `formation.sql` into your phpMyAdmin / MySQL environment to produce the valid table with some dummy records.
2. Edit `config/database.php` with your root password or custom impactVenture database name if required (It assumes `dbname=impactventure`, `user=root`, `pass=''` by default). 
3. Direct your web-server / XAMPP to run the module root path, where `index.php` is located. It will handle the routing out of the box!
