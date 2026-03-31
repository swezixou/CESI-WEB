<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration BDD
$host = 'localhost';
$dbname = 'stageconnect';
$user = 'root';
$pass = 'Root1234@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🔧 Correction des mots de passe</h1>";
    
    // Mot de passe simple
    $newPassword = 'password';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    echo "<p>Nouveau hash généré : <code>$hashedPassword</code></p>";
    
    // Mettre à jour TOUS les utilisateurs
    $stmt = $pdo->prepare("UPDATE users SET password = ?");
    $result = $stmt->execute([$hashedPassword]);
    
    if ($result) {
        $count = $stmt->rowCount();
        echo "<p style='color:green; font-size:18px;'>✅ $count utilisateurs mis à jour !</p>";
        
        // Vérifier avec admin
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['admin@cesi.fr']);
        $admin = $stmt->fetch();
        
        $test = password_verify('password', $admin['password']);
        echo "<p>Test avec 'password' : " . ($test ? "✅ OK" : "❌ KO") . "</p>";
        
        echo "<h2>👉 Connectez-vous avec :</h2>";
        echo "<ul>";
        echo "<li>Email: <strong>admin@cesi.fr</strong></li>";
        echo "<li>Mot de passe: <strong>password</strong></li>";
        echo "</ul>";
        
        echo "<p><a href='/login' style='background:blue;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Aller à la page de connexion</a></p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color:red'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>
