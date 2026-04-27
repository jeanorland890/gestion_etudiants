<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css" />
    <title>Gestion Étudiants</title>
</head>
<body>
    <div class="container">
        <h2>Ajouter un Étudiant</h2>
        
        <?php
        // Afficher les messages de succès ou d'erreur
        if (isset($_GET['success'])) {
            $message = '';
            switch($_GET['success']) {
                case 'added':
                    $message = 'Étudiant ajouté avec succès !';
                    break;
                case 'updated':
                    $message = 'Étudiant modifié avec succès !';
                    break;
                case 'deleted':
                    $message = 'Étudiant supprimé avec succès !';
                    break;
            }
            echo '<div class="message success">' . htmlspecialchars($message) . '</div>';
        }
        
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
                case 'insert_failed':
                    $message = 'Une erreur est survenue lors de l\'ajout de l\'étudiant.';
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
        <form id="studentForm" action="traitement.php" method="POST" onsubmit="return validateForm()">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" placeholder="Entrez le nom de l'étudiant" required>
            
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" placeholder="Entrez le prénom de l'étudiant" required>
            
            <label for="filiere_id">Filière :</label>
            <select name="filiere_id"> <?php
                $stmt = $pdo->query("SELECT * FROM filieres");
                while($f = $stmt->fetch()) {
                    echo "<option value='{$f['id']}'>{$f['nom']}</option>";
                }
                ?>
            </select>
            <button type="submit">Enregistrer</button>
        </form>

        <h2>Liste des Étudiants</h2>
        <?php
                $sql = "SELECT e.*, f.nom AS f_nom FROM etudiants e JOIN filieres f ON e.filiere_id = f.id";
                $students = $pdo->query($sql)->fetchAll();
                
                if (count($students) > 0): ?>
                <table>
                    <thead>
                        <tr><th>Référence</th><th>Nom</th><th>Prénom</th><th>Filière</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $row): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['ref']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nom']) ?></td>
                            <td><?= htmlspecialchars($row['prenom']) ?></td>
                            <td><?= htmlspecialchars($row['f_nom']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="update_simple.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn-edit">Modifier</a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?')">Supprimer</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <h3>Aucun étudiant enregistré</h3>
                    <p>Commencez par ajouter un étudiant en utilisant le formulaire ci-dessus.</p>
                </div>
                <?php endif; ?>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>
