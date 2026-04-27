<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $filiere_id = filter_var($_POST['filiere_id'] ?? '', FILTER_VALIDATE_INT);
    
    // Validation côté serveur
    if (empty($nom) || empty($prenom)) {
        header("Location: index.php?error=empty_fields");
        exit();
    }
    
    if (strlen($nom) < 2 || strlen($prenom) < 2) {
        header("Location: index.php?error=too_short");
        exit();
    }
    
    if (!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $nom) || !preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', $prenom)) {
        header("Location: index.php?error=invalid_chars");
        exit();
    }
    
    // Génération automatique de la référence
    function generateReference($pdo) {
        $year = date('Y');
        
        // Obtenir le dernier numéro de référence pour cette année
        $stmt = $pdo->prepare("SELECT ref FROM etudiants WHERE ref LIKE ? ORDER BY ref DESC LIMIT 1");
        $stmt->execute(["REF{$year}%"]);
        $last_ref = $stmt->fetchColumn();
        
        if ($last_ref) {
            // Extraire le numéro et l'incrémenter
            $last_num = intval(substr($last_ref, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return "REF" . $year . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    $ref = generateReference($pdo);
    
    if (!$filiere_id || $filiere_id < 1) {
        header("Location: index.php?error=invalid_filiere");
        exit();
    }
    
    try {
        // Vérifier si la filière existe
        $check_filiere = $pdo->prepare("SELECT id FROM filieres WHERE id = ?");
        $check_filiere->execute([$filiere_id]);
        
        if ($check_filiere->rowCount() === 0) {
            header("Location: index.php?error=filiere_not_found");
            exit();
        }
        
        // La référence est générée automatiquement, donc pas besoin de vérifier l'unicité ici
        
        // Insertion avec requête préparée
        $req = $pdo->prepare("INSERT INTO etudiants (nom, prenom, ref, filiere_id) VALUES (?, ?, ?, ?)");
        $success = $req->execute([$nom, $prenom, $ref, $filiere_id]);
        
        if ($success) {
            header("Location: index.php?success=added");
            exit();
        } else {
            header("Location: index.php?error=insert_failed");
            exit();
        }
        
    } catch (PDOException $e) {
        // En cas d'erreur de base de données
        error_log("Erreur base de données: " . $e->getMessage());
        header("Location: index.php?error=db_error");
        exit();
    }
} else {
    // Rediriger si la méthode n'est pas POST
    header("Location: index.php");
    exit();
}
?>
