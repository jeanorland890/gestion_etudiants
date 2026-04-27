<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    
    if (!$id || $id < 1) {
        header("Location: index.php?error=invalid_id");
        exit();
    }
    
    try {
        // Vérifier si l'étudiant existe avant de le supprimer
        $check_student = $pdo->prepare("SELECT id FROM etudiants WHERE id = ?");
        $check_student->execute([$id]);
        
        if ($check_student->rowCount() === 0) {
            header("Location: index.php?error=student_not_found");
            exit();
        }
        
        // Suppression avec requête préparée
        $req = $pdo->prepare("DELETE FROM etudiants WHERE id = ?");
        $success = $req->execute([$id]);
        
        if ($success) {
            header("Location: index.php?success=deleted");
            exit();
        } else {
            header("Location: index.php?error=delete_failed");
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
?>
