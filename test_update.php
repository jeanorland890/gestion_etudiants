<?php
include 'db_connect.php';

echo "<h2>Test de la base de données</h2>";

// Test 1: Vérifier si la table etudiants existe
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'etudiants'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Table 'etudiants' trouvée</p>";
    } else {
        echo "<p>❌ Table 'etudiants' non trouvée</p>";
    }
} catch (PDOException $e) {
    echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
}

// Test 2: Vérifier la structure de la table
try {
    $stmt = $pdo->query("DESCRIBE etudiants");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>📋 Colonnes trouvées: " . implode(", ", $columns) . "</p>";
    
    if (in_array('id', $columns) && in_array('nom', $columns) && in_array('prenom', $columns) && in_array('filiere_id', $columns)) {
        echo "<p>✅ Colonnes essentielles présentes</p>";
    } else {
        echo "<p>❌ Colonnes essentielles manquantes</p>";
    }
} catch (PDOException $e) {
    echo "<p>❌ Erreur structure: " . $e->getMessage() . "</p>";
}

// Test 3: Vérifier s'il y a des étudiants
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM etudiants");
    $count = $stmt->fetchColumn();
    echo "<p>👥 Nombre d'étudiants: $count</p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT id, nom, prenom, filiere_id FROM etudiants LIMIT 3");
        $students = $stmt->fetchAll();
        echo "<p>📝 Exemples d'étudiants:</p>";
        echo "<ul>";
        foreach ($students as $student) {
            echo "<li>ID: {$student['id']}, Nom: {$student['nom']}, Prénom: {$student['prenom']}, Filière: {$student['filiere_id']}</li>";
        }
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "<p>❌ Erreur lecture: " . $e->getMessage() . "</p>";
}

// Test 4: Vérifier la table filieres
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM filieres");
    $count = $stmt->fetchColumn();
    echo "<p>📚 Nombre de filières: $count</p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT id, nom FROM filieres LIMIT 5");
        $filieres = $stmt->fetchAll();
        echo "<p>📝 Exemples de filières:</p>";
        echo "<ul>";
        foreach ($filieres as $filiere) {
            echo "<li>ID: {$filiere['id']}, Nom: {$filiere['nom']}</li>";
        }
        echo "</ul>";
    }
} catch (PDOException $e) {
    echo "<p>❌ Erreur filières: " . $e->getMessage() . "</p>";
}

// Test 5: Test de mise à jour
if (isset($_GET['test_id']) && is_numeric($_GET['test_id'])) {
    $test_id = (int)$_GET['test_id'];
    
    try {
        // Récupérer l'étudiant
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
        $stmt->execute([$test_id]);
        $student = $stmt->fetch();
        
        if ($student) {
            echo "<p>🔄 Test de mise à jour pour l'étudiant ID: $test_id</p>";
            echo "<p>Données actuelles: {$student['nom']} {$student['prenom']}</p>";
            
            // Test de mise à jour
            $new_nom = $student['nom'] . "_TEST";
            $stmt = $pdo->prepare("UPDATE etudiants SET nom = ? WHERE id = ?");
            $result = $stmt->execute([$new_nom, $test_id]);
            
            if ($result) {
                echo "<p>✅ Mise à jour réussie!</p>";
                
                // Vérifier la mise à jour
                $stmt = $pdo->prepare("SELECT nom FROM etudiants WHERE id = ?");
                $stmt->execute([$test_id]);
                $updated_name = $stmt->fetchColumn();
                echo "<p>Nouveau nom: $updated_name</p>";
                
                // Restaurer le nom original
                $stmt = $pdo->prepare("UPDATE etudiants SET nom = ? WHERE id = ?");
                $stmt->execute([$student['nom'], $test_id]);
                echo "<p>🔄 Nom original restauré</p>";
            } else {
                echo "<p>❌ Échec de la mise à jour</p>";
            }
        } else {
            echo "<p>❌ Étudiant ID: $test_id non trouvé</p>";
        }
    } catch (PDOException $e) {
        echo "<p>❌ Erreur test mise à jour: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>Retour à l'application</a></p>";
if (!isset($_GET['test_id'])) {
    echo "<p><a href='test_update.php?test_id=1'>Tester la mise à jour pour l'étudiant ID 1</a></p>";
}
?>
