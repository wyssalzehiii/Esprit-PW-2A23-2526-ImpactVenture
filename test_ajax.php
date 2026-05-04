<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['description'] = 'Ceci est un projet innovant avec beaucoup de technologies.';
$_POST['titre'] = 'Test Projet';
$_GET['action'] = 'analyze_projet';

try {
    require 'index.php';
} catch (Throwable $e) {
    echo "ERROR CAUGHT: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
}
