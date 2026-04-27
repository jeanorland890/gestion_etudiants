<?php
include 'db_connect.php';

echo "<h2>🔍 Debug de la modification</h2>";

// Récupérer l'ID depuis l'URL
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
echo "<p>ID récupéré: $id</p>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>📝 Données POST reçues:</h3>";
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";
    
    // Récupération et validation des données
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $filiere_id = filter_var($_POST['filiere_id'] ?? 0, FILTER_VALIDATE_INT);
    
    echo "<h3>✅ Données validées:</h3>";
    echo "<p>ID: $id</p>";
    echo "<p>Nom: '$nom'</p>";
    echo "<p>Prénom: '$prenom'</p>";
    echo "<p>Filière ID: $filiere_id</p>";
    
    // Validation
    if (!$id || $id < 1) {
        echo "<p>❌ ID invalide</p>";
        exit();
    }
    
    if (empty($nom) || empty($prenom)) {
        echo "<p>❌ Champs vides</p>";
        exit();
    }
    
    if (strlen($nom) < 2 || strlen($prenom) < 2) {
        echo "<p>❌ Champs trop courts</p>";
        exit();
    }
    
    if (!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $nom) || !preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $prenom)) {
        echo "<p>❌ Caractères invalides</p>";
        exit();
    }
    
    if (!$filiere_id || $filiere_id < 1) {
        echo "<p>❌ Filière invalide</p>";
        exit();
    }
    
    echo "<p>✅ Validation réussie</p>";
    
    try {
        // Afficher la requête SQL
        $sql = "UPDATE etudiants SET nom = ?, prenom = ?, filiere_id = ? WHERE id = ?";
        echo "<h3>🔧 Requête SQL:</h3>";
        echo "<p>$sql</p>";
        echo "<p>Paramètres: ['$nom', '$prenom', $filiere_id, $id]</p>";
        
        // Mise à jour
        $req = $pdo->prepare($sql);
        $success = $req->execute([$nom, $prenom, $filiere_id, $id]);
        
        echo "<h3>📊 Résultat:</h3>";
        echo "<p>Execute retourné: " . ($success ? 'true' : 'false') . "</p>";
        echo "<p>Nombre de lignes affectées: " . $req->rowCount() . "</p>";
        
        if ($success && $req->rowCount() > 0) {
            echo "<p>✅ Mise à jour réussie!</p>";
            
            // Vérifier la mise à jour
            $stmt = $pdo->prepare("SELECT nom, prenom, filiere_id FROM etudiants WHERE id = ?");
            $stmt->execute([$id]);
            $updated = $stmt->fetch();
            
            echo "<h3>🔍 Vérification après mise à jour:</h3>";
            echo "<p>Nouveau nom: {$updated['nom']}</p>";
            echo "<p>Nouveau prénom: {$updated['prenom']}</p>";
            echo "<p>Nouvelle filière: {$updated['filiere_id']}</p>";
            
        } else {
            echo "<p>❌ Mise à jour échouée ou aucune ligne modifiée</p>";
            
            // Vérifier pourquoi
            $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            
            if ($current) {
                echo "<h3>📋 Données actuelles en base:</h3>";
                echo "<p>Nom: {$current['nom']}</p>";
                echo "<p>Prénom: {$current['prenom']}</p>";
                echo "<p>Filière: {$current['filiere_id']}</p>";
            } else {
                echo "<p>❌ Étudiant non trouvé en base!</p>";
            }
        }
        
    } catch (PDOException $e) {
        echo "<h3>❌ Erreur PDO:</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<p>Code: " . $e->getCode() . "</p>";
    }
} else {
    echo "<p>❌ Méthode non POST (méthode: " . $_SERVER['REQUEST_METHOD'] . ")</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Retour à l'application</a></p>";
?>

<!-- Formulaire de test -->
<h3>🧪 Formulaire de test</h3>
<form method="POST" action="debug_update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">
    <p>Nom: <input type="text" name="nom" value="TEST NOM"></p>
    <p>Prénom: <input type="text" name="prenom" value="TEST PRENOM"></p>
    <p>Filière: <input type="number" name="filiere_id" value="1"></p>
    <button type="submit">Tester la mise à jour</button>
</form>
