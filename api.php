<?php
header('Content-Type: application/json');
session_start();
require 'db.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

// Connexion
if ($action === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();

    if ($user && ($input['password'] === $user['password'] || password_verify($input['password'], $user['password']))) {
        if ($user['status'] === 'pending' || $user['status'] === 'inactive') {
            echo json_encode(['success' => false, 'message' => 'Compte inactif ou en attente.']);
            exit;
        }

        // On ne stocke pas le mdp en session
        unset($user['password']);
        
        $_SESSION['user'] = $user;
        echo json_encode(['success' => true, 'role' => $user['role'], 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects.']);
    }
    exit;
}

// Inscription
if ($action === 'signup') {
    $hash = password_hash($input['password'], PASSWORD_DEFAULT);
    $company = ($input['role'] === 'recruiter' && !empty($input['company'])) ? $input['company'] : null;

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, company, status) VALUES (?, ?, ?, ?, ?, 'active')");
    
    try {
        $stmt->execute([$input['name'], $input['email'], $hash, $input['role'], $company]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Email déjà utilisé.']);
    }
    exit;
}

// Déconnexion
if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Vérifier si session active
if ($action === 'check_session') {
    if (isset($_SESSION['user'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user']['id']]);
        $freshUser = $stmt->fetch();
        
        if($freshUser) {
            unset($freshUser['password']);
            $_SESSION['user'] = $freshUser; 
            echo json_encode(['logged_in' => true, 'user' => $freshUser]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
    } else {
        echo json_encode(['logged_in' => false]);
    }
    exit;
}

// Liste des offres
if ($action === 'get_jobs') {
    if (isset($_GET['my_jobs']) && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'recruiter') {
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE recruiter_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user']['id']]);
    } elseif (isset($_GET['admin']) && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
        $stmt = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC");
    } else {
        $stmt = $pdo->query("SELECT * FROM jobs WHERE status = 'published' ORDER BY created_at DESC");
    }
    echo json_encode($stmt->fetchAll());
    exit;
}

// Créer ou modifier une offre
if ($action === 'save_job') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'student') exit;
    
    if (!empty($input['id'])) {
        $stmt = $pdo->prepare("UPDATE jobs SET title=?, type=?, location=?, sector=? WHERE id=?");
        $stmt->execute([$input['title'], $input['type'], $input['location'], $input['sector'], $input['id']]);
    } else {
        $companyName = $_SESSION['user']['company'] ?? 'Entreprise'; 
        $stmt = $pdo->prepare("INSERT INTO jobs (recruiter_id, title, company, type, location, sector, status) VALUES (?, ?, ?, ?, ?, ?, 'published')");
        $stmt->execute([$_SESSION['user']['id'], $input['title'], $companyName, $input['type'], $input['location'], $input['sector']]);
    }
    echo json_encode(['success' => true]);
    exit;
}

// Supprimer une offre et ses candidatures
if ($action === 'delete_job') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'student') exit;

    $stmtApps = $pdo->prepare("DELETE FROM applications WHERE job_id = ?");
    $stmtApps->execute([$_GET['id']]);

    $stmtJob = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    $stmtJob->execute([$_GET['id']]);
    
    echo json_encode(['success' => true]);
    exit;
}

// Valider offre admin
if ($action === 'validate_job') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') exit;

    $stmt = $pdo->prepare("UPDATE jobs SET status = 'published' WHERE id = ?");
    $stmt->execute([$input['id']]);
    echo json_encode(['success' => true]);
    exit;
}

// Ajouter une candidature
if ($action === 'apply') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') { 
        echo json_encode(['success'=>false, 'message'=>'Accès non autorisé']); exit; 
    }
    
    $check = $pdo->prepare("SELECT id FROM applications WHERE user_id=? AND job_id=?");
    $check->execute([$_SESSION['user']['id'], $input['job_id']]);
    
    if($check->fetch()) {
        echo json_encode(['success'=>false, 'message'=>'Candidature déjà existante']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO applications (user_id, job_id, status) VALUES (?, ?, 'en_attente')");
    $stmt->execute([$_SESSION['user']['id'], $input['job_id']]);
    echo json_encode(['success' => true]);
    exit;
}

// Liste des candidatures
if ($action === 'get_applications') {
    if (!isset($_SESSION['user'])) exit;

    if ($_SESSION['user']['role'] === 'admin') {
        $stmt = $pdo->query("SELECT a.*, j.company, j.title as position FROM applications a JOIN jobs j ON a.job_id = j.id ORDER BY a.date DESC");
    } 
    elseif ($_SESSION['user']['role'] === 'recruiter') {
        $sql = "SELECT a.*, u.id as candidate_user_id, u.name as candidate_name, u.email, u.phone, j.title as position, a.date as date
                FROM applications a 
                JOIN users u ON a.user_id = u.id 
                JOIN jobs j ON a.job_id = j.id 
                WHERE j.recruiter_id = ? ORDER BY a.date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user']['id']]);
    } 
    else {
        $sql = "SELECT a.*, 
                    u.id as recruiter_id, j.company, j.title as position, j.type, j.sector, 
                    a.date as date,
                    u.name as recruiter_name, u.email as recruiter_email, u.phone as recruiter_phone
                FROM applications a 
                JOIN jobs j ON a.job_id = j.id 
                JOIN users u ON j.recruiter_id = u.id 
                WHERE a.user_id = ? ORDER BY a.date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user']['id']]);
    }
    echo json_encode($stmt->fetchAll());
    exit;
}

// Modifier statut candidature
if ($action === 'update_status') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] === 'student') exit;

    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$input['status'], $input['id']]);

    $stmtInfo = $pdo->prepare("
        SELECT a.user_id, j.title as job_title 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.id = ?
    ");
    $stmtInfo->execute([$input['id']]);
    $info = $stmtInfo->fetch();

    // Envoi de la notif à l'étudiant
    if ($info) {
        $statusLabels = [
            'entretien' => 'Entretien proposé',
            'accepte' => 'Candidature acceptée !',
            'refuse' => 'Candidature refusée',
            'en_attente' => 'Statut mis à jour'
        ];
        $titre = $statusLabels[$input['status']] ?? 'Mise à jour candidature';
        $msg = "Le statut de votre candidature pour le poste '{$info['job_title']}' a changé.";
        
        createNotification($pdo, $info['user_id'], 'status', $titre, $msg);
    }

    echo json_encode(['success' => true]);
    exit;
}

// Liste utilisateurs admin
if ($action === 'get_users') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') exit;
    
    $stmt = $pdo->query("SELECT id, name, email, role, company, phone, status, created_at FROM users ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

// Supprimer utilisateur
if ($action === 'delete_user') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') exit;

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$_GET['id']]);
    echo json_encode(['success' => true]);
    exit;
}

// Mettre à jour profil
if ($action === 'update_user') {
    if (!isset($_SESSION['user'])) exit;

    $idToUpdate = $input['id'];
    
    if ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['id'] != $idToUpdate) exit;

    if (isset($input['company'])) {
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, status=?, phone=?, company=? WHERE id=?");
        $stmt->execute([$input['name'], $input['email'], $input['role'], $input['status'], $input['phone'] ?? '', $input['company'], $idToUpdate]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, status=?, phone=? WHERE id=?");
        $stmt->execute([$input['name'], $input['email'], $input['role'], $input['status'], $input['phone'] ?? '', $idToUpdate]);
    }
    echo json_encode(['success' => true]);
    exit;
}

// Envoyer message chat
if ($action === 'send_message') {
    if (!isset($_SESSION['user'])) exit;
    
    $senderId = $_SESSION['user']['id'];
    $receiverId = $input['receiver_id'];
    $message = $input['message'];
    $jobId = $input['job_id'];

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, job_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$senderId, $receiverId, $message, $jobId]);
    
    $stmtInfo = $pdo->prepare("SELECT name, company FROM users WHERE id = ?");
    $stmtInfo->execute([$senderId]);
    $senderInfo = $stmtInfo->fetch();

    if ($senderInfo) {
        $actionData = [
            'recruiter_id' => $senderId,
            'name' => $senderInfo['name'],
            'company' => $senderInfo['company'] ?? 'Entreprise',
            'job_id' => $jobId
        ];

        createNotification($pdo, $receiverId, 'message', "Nouveau message", "{$senderInfo['name']} vous a envoyé un message.", $actionData);
    }

    echo json_encode(['success' => true]);
    exit;
}

// Charger messages chat
if ($action === 'get_messages') {
    if (!isset($_SESSION['user'])) { echo json_encode([]); exit; }
    
    $myId = $_SESSION['user']['id'];
    $otherId = $_GET['contact_id'];
    $jobId = $_GET['job_id'];

    $stmt = $pdo->prepare("
        SELECT * FROM messages 
        WHERE ((sender_id = ? AND receiver_id = ?) 
        OR (sender_id = ? AND receiver_id = ?))
        AND job_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$myId, $otherId, $otherId, $myId, $jobId]);
    
    echo json_encode($stmt->fetchAll());
    exit;
}

// Fonction utilitaire pour notifs
function createNotification($pdo, $userId, $type, $title, $message, $data = null) {
    $dataJson = $data ? json_encode($data) : null;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $type, $title, $message, $dataJson]);
}

// Vérifier nouvelles notifs
if ($action === 'check_notifications') {
    if (!isset($_SESSION['user'])) { echo json_encode([]); exit; }
    
    $userId = $_SESSION['user']['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at ASC");
    $stmt->execute([$userId]);
    $notifs = $stmt->fetchAll();

    if (count($notifs) > 0) {
        $stmtUpdate = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmtUpdate->execute([$userId]);
    }

    echo json_encode($notifs);
    exit;
}

// Signaler une offre
if ($action === 'report_job') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') exit;
    
    $stmt = $pdo->prepare("INSERT INTO reports (user_id, job_id, reason) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user']['id'], $input['job_id'], $input['reason']]);
    
    echo json_encode(['success' => true]);
    exit;
}

// Liste des signalements admin
if ($action === 'get_reports') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { echo json_encode([]); exit; }
    
    $stmt = $pdo->query("
        SELECT r.*, 
            j.title as job_title, j.company, 
            u.name as reporter_name
        FROM reports r
        JOIN jobs j ON r.job_id = j.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

// Supprimer un signalement
if ($action === 'delete_report') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') exit;
    
    $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode(['success' => true]);
    exit;
}
?>