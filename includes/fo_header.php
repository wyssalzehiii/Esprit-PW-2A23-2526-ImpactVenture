<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpactVenture - Formations</title>
    <!-- Public modern Bootstrap style -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Open Sans', system-ui, -apple-system, sans-serif; background-color: #fcfcfc; }
        .navbar { background-color: #0d6efd; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: bold; font-size: 1.4rem; }
        .navbar-brand, .nav-link { color: #fff !important; }
        .nav-link:hover { opacity: 0.8; }
        .hero { background: linear-gradient(135deg, #0d6efd 0%, #0044b5 100%); color: white; padding: 60px 0; text-align: center; margin-bottom: 40px; border-radius: 0 0 20px 20px; }
        .card-transition { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-transition:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg border-bottom border-light">
    <div class="container">
        <a class="navbar-brand" href="index.php?action=list_formations_front">ImpactVenture</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navcol">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navcol">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=list_formations_front">Formations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=list_badges_front">Badges</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=list_projects_front">Projects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=recommended_formations">My Path</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=list_formations">Admin Portal</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content Wrappers inside container down below -->

<!-- Flash Messages (centralized here so no view touches $_SESSION) -->
<div class="container mt-3">
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
</div>
