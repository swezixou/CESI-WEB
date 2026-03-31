<?php
/**
 * OUTIL DE DIAGNOSTIC — À SUPPRIMER APRÈS UTILISATION
 * URL : http://ton-site/debug_login.php
 *
 * Ce fichier t'explique EXACTEMENT pourquoi ton login échoue.
 */
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

// Démarrer session pour tester
session_name(SESSION_NAME);
session_start();

echo '<style>body{font-family:monospace;background:#0d0d14;color:#e0e0e0;padding:30px;line-height:1.8}
h2{color:#4ae68a;margin-top:28px;border-bottom:1px solid #333;padding-bottom:8px}
.ok{color:#4ae68a}.fail{color:#ff6b6b}.warn{color:#fbbf24}
code{background:#1c1c2b;padding:2px 8px;border-radius:5px;color:#3bd4ff}
.box{background:#1c1c2b;padding:16px 20px;border-radius:10px;margin:12px 0;border:1px solid #2a2a3c}
</style>';

echo '<h1 style="color:#fff">🔍 Debug StageConnect Login</h1>';

// ── 1. Test session
echo '<h2>1. Session PHP</h2>';
echo '<div class="box">';
echo 'Nom session : <code>' . SESSION_NAME . '</code><br>';
echo 'Status : ' . (session_status() === PHP_SESSION_ACTIVE ? '<span class="ok">✅ Active</span>' : '<span class="fail">❌ Inactive</span>') . '<br>';
echo 'ID session : <code>' . session_id() . '</code><br>';
echo 'Cookie httponly : <code>' . (ini_get('session.cookie_httponly') ? 'oui' : 'non') . '</code><br>';
echo '</div>';

// ── 2. Test connexion BDD
echo '<h2>2. Connexion Base de données</h2>';
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
    );
    echo '<div class="box"><span class="ok">✅ Connexion BDD réussie</span><br>';
    echo 'Host : <code>' . DB_HOST . '</code> | DB : <code>' . DB_NAME . '</code></div>';

    // ── 3. Utilisateurs en base
    echo '<h2>3. Utilisateurs en base + test hash</h2>';
    $users = $pdo->query("SELECT id, firstname, lastname, email, role, is_active, password FROM users")->fetchAll();

    if (empty($users)) {
        echo '<div class="box"><span class="fail">❌ AUCUN UTILISATEUR en base ! Tu dois importer le schema.sql.</span></div>';
    } else {
        $testPasswords = ['password', 'Password123!', '123456', 'admin', 'root'];
        foreach ($users as $u) {
            echo '<div class="box">';
            echo "<strong>{$u['firstname']} {$u['lastname']}</strong> ({$u['role']}) — ";
            echo $u['is_active'] ? '<span class="ok">actif</span>' : '<span class="fail">inactif</span>';
            echo '<br><code>' . $u['email'] . '</code><br>';
            echo 'Hash : <code>' . substr($u['password'],0,20) . '...</code><br>';

            $found = false;
            foreach ($testPasswords as $p) {
                if (password_verify($p, $u['password'])) {
                    echo '<span class="ok">✅ Mot de passe : <strong>' . $p . '</strong></span><br>';
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo '<span class="warn">⚠️ Aucun mot de passe standard ne correspond.</span><br>';
                echo '<small style="color:#888">Hash inconnu — le mot de passe a peut-être été changé.</small>';
            }
            echo '</div>';
        }
    }

    // ── 4. Test password_verify direct
    echo '<h2>4. Test du hash connu (diagnostic)</h2>';
    $knownHash = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    echo '<div class="box">';
    echo 'Hash en base (premiers 30 chars) : <code>' . substr($knownHash,0,30) . '...</code><br><br>';
    foreach (['password','Password123!','admin','123456'] as $p) {
        $r = password_verify($p, $knownHash);
        echo 'password_verify(<code>"'.$p.'"</code>, hash) = ' . ($r ? '<span class="ok">TRUE ← MOT DE PASSE CORRECT</span>' : '<span class="fail">false</span>') . '<br>';
    }
    echo '</div>';

} catch (Exception $e) {
    echo '<div class="box"><span class="fail">❌ Erreur BDD : ' . $e->getMessage() . '</span><br>';
    echo 'Vérifie <code>config/database.php</code></div>';
}

// ── 5. Cause probable du problème
echo '<h2>5. Diagnostic — Cause probable de ton problème</h2>';
echo '<div class="box">';
echo '<strong>Si tu vois "Email ou mot de passe incorrect" mais que ton amie peut se connecter :</strong><br><br>';
echo '→ Cause probable : <span class="warn">Cookie de session corrompu dans TON navigateur.</span><br><br>';
echo '<strong>Solution à essayer dans l\'ordre :</strong><br>';
echo '1. <span class="ok">Ouvre une fenêtre privée (Ctrl+Maj+N) et essaie de te connecter</span><br>';
echo '2. Vide le cache et les cookies pour ton site<br>';
echo '3. Essaie dans un autre navigateur<br>';
echo '4. Vérifie l\'erreur exacte dans <code>Outils développeur > Console > Network</code><br><br>';
echo '<span class="warn">⚠️ Rappel : le mot de passe est <code>password</code> (tout minuscule, sans !)</span>';
echo '</div>';

echo '<p style="color:#555;margin-top:40px;font-size:.8rem">⚠️ Supprime ce fichier après utilisation : <code>public/debug_login.php</code></p>';
