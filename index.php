<?php
session_start();
require_once 'classes/ADNManipulator.php';
require_once 'includes/carnet.php';

CarnetLaboratoire::init();

if (isset($_GET['clear_carnet'])) {
    CarnetLaboratoire::vider();
    $message = "Carnet vidé !";
}

$adn = new ADNManipulator();
$resultat = '';
$erreur = '';
$sequenceOriginale = '';
$visualisation = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sequence'])) {
    $sequenceOriginale = strtoupper(trim($_POST['sequence']));
    $action = $_POST['action'] ?? '';
    
    if (empty($sequenceOriginale)) {
        $erreur = "Veuillez entrer une séquence ADN";
    } elseif (!$adn->validerSequence($sequenceOriginale)) {
        $erreur = "Utilisez seulement A,T,G,C";
    } else {
        switch($action) {
            case 'complement':
                $resultat = $adn->complementaire($sequenceOriginale);
                $titre = "Synthèse - Brin complémentaire";
                $visualisation = $adn->visualiserComparaison($sequenceOriginale, $resultat, "Original", "Complémentaire");
                CarnetLaboratoire::ajouter($sequenceOriginale, 'complement', $resultat);
                break;
            case 'mutation':
                $mutation = $adn->mutation($sequenceOriginale);
                $resultat = $mutation['sequence'];
                $titre = "Mutation aléatoire";
                $visualisation = $adn->visualiserComparaison($sequenceOriginale, $resultat, "Original", "Muté", $mutation['position']);
                CarnetLaboratoire::ajouter($sequenceOriginale, 'mutation', $resultat);
                break;
            case 'transcription':
                $resultat = $adn->transcription($sequenceOriginale);
                $titre = "Transcription ADN → ARN";
                $visualisation = $adn->visualiserComparaison($sequenceOriginale, $resultat, "ADN", "ARN");
                CarnetLaboratoire::ajouter($sequenceOriginale, 'transcription', $resultat);
                break;
            case 'helice':
                $resultat = $sequenceOriginale;
                $titre = "Hélice 3D";
                $visualisation = $adn->visualiserHeliceTournante($sequenceOriginale);
                CarnetLaboratoire::ajouter($sequenceOriginale, 'helice', $sequenceOriginale);
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADN Manipulator - Laboratoire</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>

    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
        <img src="logo.png" alt="LAB GENIUS" style="height: 70px; width: auto; filter: drop-shadow(0 0 15px var(--neon-blue)); border-radius: 10px;">
        <div>
            <h1> LABGENUIS</h1>
            <div class="theme-switch">
                <span>🌙</span>
                <label class="switch">
                    <input type="checkbox" id="theme-toggle">
                    <span class="slider"></span>
                </label>
                <span>☀️</span>
            </div>
        </header>

        <form method="POST">
            <input type="text" name="sequence" placeholder="Ex: ATGC" value="<?= $sequenceOriginale ?>" required>
            
            <div class="actions">
                <label><input type="radio" name="action" value="complement" checked> 🧬 Synthèse</label>
                <label><input type="radio" name="action" value="mutation"> ⚡ Mutation</label>
                <label><input type="radio" name="action" value="transcription"> 📝 Transcription</label>
                <label><input type="radio" name="action" value="helice"> 🧿 Hélice 3D</label>
            </div>
            
            <button type="submit">Analyser</button>
        </form>
        
        <?php if ($erreur): ?>
            <div class="erreur"><?= $erreur ?></div>
        <?php endif; ?>
        
        <?php if ($visualisation): ?>
            <div class="resultat">
                <h2><?= $titre ?></h2>
                <?= $visualisation ?>
                <?php if ($resultat && $action != 'helice'): ?>
                    <p>GC: <?= $adn->pourcentageGC($sequenceOriginale) ?>%</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="carnet">
            <h2>📔 Carnet de laboratoire</h2>
            <?= CarnetLaboratoire::afficher() ?>
            <?php if (!empty($_SESSION['carnet'])): ?>
                <a href="?clear_carnet=1">Vider le carnet</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>