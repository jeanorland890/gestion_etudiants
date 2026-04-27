<?php
// Connexion simple à la base de données
$host = 'localhost';
$dbname = 'gestion_etudiants';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Récupération de l'étudiant
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$etudiant = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
    $stmt->execute([$id]);
    $etudiant = $stmt->fetch();
}

// Traitement du formulaire
if ($_POST && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $filiere_id = (int)$_POST['filiere_id'];
    
    if (!empty($nom) && !empty($prenom) && $id > 0 && $filiere_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE etudiants SET nom = ?, prenom = ?, filiere_id = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $filiere_id, $id]);
            header("Location: index.php?success=updated");
            exit();
        } catch (PDOException $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs";
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
        
        <?php if (isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($etudiant): ?>
        <form method="POST">
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
                    $selected = ($f['id'] == $etudiant['filiere_id']) ? 'selected' : '';
                    echo "<option value='{$f['id']}' $selected>{$f['nom']}</option>";
                }
                ?>
            </select>

            <button type="submit">Mettre à jour</button>
            <a href="index.php" class="btn-cancel">Annuler</a>
        </form>
        <?php else: ?>
            <div class="message error">Étudiant non trouvé</div>
            <a href="index.php">Retour</a>
        <?php endif; ?>
    </div>
</body>
</html>
