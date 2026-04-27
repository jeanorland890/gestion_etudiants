<?php 
include 'db_connect.php'; 

// 1. Récupération de l'étudiant à modifier
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    if (!$id || $id < 1) {
        header("Location: index.php?error=invalid_id");
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
        $stmt->execute([$id]);
        $etudiant = $stmt->fetch();

        if (!$etudiant) {
            header("Location: index.php?error=student_not_found");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erreur base de données: " . $e->getMessage());
        header("Location: index.php?error=db_error");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// 2. Logique de mise à jour
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $filiere_id = filter_var($_POST['filiere_id'] ?? '', FILTER_VALIDATE_INT);
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    
    // Validation côté serveur
    if (empty($nom) || empty($prenom)) {
        header("Location: update.php?id=$id&error=empty_fields");
        exit();
    }
    
    if (strlen($nom) < 2 || strlen($prenom) < 2) {
        header("Location: update.php?id=$id&error=too_short");
        exit();
    }
    
    if (!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $nom) || !preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $prenom)) {
        header("Location: update.php?id=$id&error=invalid_chars");
        exit();
    }
    
    if (!$filiere_id || $filiere_id < 1) {
        header("Location: update.php?id=$id&error=invalid_filiere");
        exit();
    }
    
    if (!$id || $id < 1) {
        header("Location: index.php?error=invalid_id");
        exit();
    }
    
    try {
        // Vérifier si la filière existe
        $check_filiere = $pdo->prepare("SELECT id FROM filieres WHERE id = ?");
        $check_filiere->execute([$filiere_id]);
        
        if ($check_filiere->rowCount() === 0) {
            header("Location: update.php?id=$id&error=filiere_not_found");
            exit();
        }
        
        // Mise à jour simple sans toucher à REF
        $req = $pdo->prepare("UPDATE etudiants SET nom = ?, prenom = ?, filiere_id = ? WHERE id = ?");
        $success = $req->execute([$nom, $prenom, $filiere_id, $id]);
        
        if ($success) {
            header("Location: index.php?success=updated");
            exit();
        } else {
            header("Location: update.php?id=$id&error=update_failed");
            exit();
        }
        
    } catch (PDOException $e) {
        error_log("Erreur base de données: " . $e->getMessage());
        header("Location: update.php?id=$id&error=db_error");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Modifier l'Étudiant</title>
</head>
<body>
    <div class="container">
        <h2>Modifier les informations de l'étudiant</h2>
        
        <?php
        // Afficher les messages d'erreur pour la page update
        if (isset($_GET['error'])) {
            $message = '';
            switch($_GET['error']) {
                case 'empty_fields':
                    $message = 'Veuillez remplir tous les champs obligatoires.';
                    break;
                case 'too_short':
                    $message = 'Le nom et le prénom doivent contenir au moins 2 caractères.';
                    break;
                case 'invalid_chars':
                    $message = 'Le nom et le prénom ne doivent contenir que des lettres.';
                    break;
                case 'invalid_filiere':
                    $message = 'Veuillez sélectionner une filière valide.';
                    break;
                case 'filiere_not_found':
                    $message = 'La filière sélectionnée n\'existe pas.';
                    break;
                case 'update_failed':
                    $message = 'Une erreur est survenue lors de la modification.';
                    break;
                case 'db_error':
                    $message = 'Une erreur de base de données est survenue.';
                    break;
                default:
                    $message = 'Une erreur est survenue.';
            }
            echo '<div class="message error">' . htmlspecialchars($message) . '</div>';
        }
        ?>
        
        <form id="studentForm" action="update_backup.php" method="POST" onsubmit="return validateForm()">
            <input type="hidden" name="id" value="<?= $etudiant['id'] ?>">

            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($etudiant['nom']) ?>" required>

            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" required>

            <label for="filiere_id">Filière :</label>
            <select name="filiere_id" required>
                <?php
                $stmt_f = $pdo->query("SELECT * FROM filieres");
                while($f = $stmt_f->fetch()) {
                    // Sélection automatique de la filière actuelle
                    $selected = ($f['id'] == $etudiant['filiere_id']) ? 'selected' : '';
                    echo "<option value='{$f['id']}' $selected>{$f['nom']}</option>";
                }
                ?>
            </select>

            <button type="submit">Mettre à jour</button>
            <a href="index.php" class="btn-cancel">Annuler</a>
        </form>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
