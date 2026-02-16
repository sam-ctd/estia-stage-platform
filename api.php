<?php
header('Content-Type: application/json');
session_start();
require 'db.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

// 1. AUTHENTIFICATION
if ($action === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();

    if ($user && ($input['password'] === $user['password'] || password_verify($input['password'], $user['password']))) {
        
        if ($user['status'] === 'pending' || $user['status'] === 'inactive') {
            echo json_encode(['success' => false, 'message' => 'Votre compte est en attente de validation ou inactif. Contactez l\'administrateur.']);
            exit;
        }

        $_SESSION['user'] = $user;
        echo json_encode(['success' => true, 'role' => $user['role'], 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
    }
    exit;
}

if ($action === 'signup') {
    $hash = password_hash($input['password'], PASSWORD_DEFAULT);
    
    // MODIFICATION ICI : On ajoute le champ 'company'
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, company, status) VALUES (?, ?, ?, ?, ?, 'active')");
    
    // On récupère le nom de l'entreprise si c'est un recruteur, sinon NULL
    $company = ($input['role'] === 'recruiter' && !empty($input['company'])) ? $input['company'] : null;

    try {
        $stmt->execute([$input['name'], $input['email'], $hash, $input['role'], $company]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé.']);
    }
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'check_session') {
    if (isset($_SESSION['user'])) {
        // On recharge les infos fraîches depuis la BDD au cas où
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user']['id']]);
        $freshUser = $stmt->fetch();
        $_SESSION['user'] = $freshUser; // Mise à jour session

        echo json_encode(['logged_in' => true, 'user' => $freshUser]);
    } else {
        echo json_encode(['logged_in' => false]);
    }
    exit;
}

// 2. GESTION DES OFFRES
if ($action === 'get_jobs') {
    if (isset($_GET['my_jobs']) && isset($_SESSION['user'])) {
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE recruiter_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user']['id']]);
    } elseif (isset($_GET['admin'])) {
        $stmt = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC");
    } else {
        $stmt = $pdo->query("SELECT * FROM jobs WHERE status = 'published' ORDER BY created_at DESC");
    }
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'save_job') {
    if (!isset($_SESSION['user'])) exit;
    
    if (!empty($input['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE jobs SET title=?, type=?, location=?, sector=? WHERE id=?");
        $stmt->execute([$input['title'], $input['type'], $input['location'], $input['sector'], $input['id']]);
    } else {
        // Insert
        // MODIFICATION ICI : On utilise le nom d'entreprise stocké dans la session de l'utilisateur
        $companyName = $_SESSION['user']['company'] ?? 'Entreprise'; 

        $stmt = $pdo->prepare("INSERT INTO jobs (recruiter_id, title, company, type, location, sector, status) VALUES (?, ?, ?, ?, ?, ?, 'published')");
        $stmt->execute([$_SESSION['user']['id'], $input['title'], $companyName, $input['type'], $input['location'], $input['sector']]);
    }
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'delete_job') {
    // 1. D'abord supprimer les candidatures liées à cette offre
    $stmtApps = $pdo->prepare("DELETE FROM applications WHERE job_id = ?");
    $stmtApps->execute([$_GET['id']]);

    // 2. Ensuite supprimer l'offre elle-même
    $stmtJob = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    $stmtJob->execute([$_GET['id']]);
    
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'validate_job') {
    $stmt = $pdo->prepare("UPDATE jobs SET status = 'published' WHERE id = ?");
    $stmt->execute([$input['id']]);
    echo json_encode(['success' => true]);
    exit;
}

// 3. CANDIDATURES
if ($action === 'apply') {
    if (!isset($_SESSION['user'])) { echo json_encode(['success'=>false, 'message'=>'Non connecté']); exit; }
    
    $check = $pdo->prepare("SELECT id FROM applications WHERE user_id=? AND job_id=?");
    $check->execute([$_SESSION['user']['id'], $input['job_id']]);
    if($check->fetch()) {
        echo json_encode(['success'=>false, 'message'=>'Déjà postulé']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO applications (user_id, job_id, status) VALUES (?, ?, 'en_attente')");
    $stmt->execute([$_SESSION['user']['id'], $input['job_id']]);
    echo json_encode(['success' => true]);
    exit;
}

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
        // Ajout des infos recruteur pour l'étudiant (pour l'onglet contact)
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

if ($action === 'update_status') {
    // 1. Mise à jour du statut
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$input['status'], $input['id']]);

    // 2. Récupérer les infos pour la notification (Qui est l'étudiant ? Quel poste ?)
    $stmtInfo = $pdo->prepare("
        SELECT a.user_id, j.title as job_title 
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.id = ?
    ");
    $stmtInfo->execute([$input['id']]);
    $info = $stmtInfo->fetch();

    // 3. Créer la notification
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

// 4. ADMIN USERS
if ($action === 'get_users') {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'delete_user') {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'update_user') {
    // On permet la mise à jour de 'company' si fournie (pour le recruteur)
    // On vérifie d'abord si le champ 'company' est présent dans l'input
    if (isset($input['company'])) {
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, status=?, phone=?, company=? WHERE id=?");
        $stmt->execute([$input['name'], $input['email'], $input['role'], $input['status'], $input['phone'] ?? '', $input['company'], $input['id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, status=?, phone=? WHERE id=?");
        $stmt->execute([$input['name'], $input['email'], $input['role'], $input['status'], $input['phone'] ?? '', $input['id']]);
    }
    echo json_encode(['success' => true]);
    exit;
}

// 5. MESSAGERIE (CHAT PAR OFFRE)
if ($action === 'send_message') {
    if (!isset($_SESSION['user'])) { echo json_encode(['success'=>false, 'message'=>'Non connecté']); exit; }
    
    $senderId = $_SESSION['user']['id'];
    $receiverId = $input['receiver_id'];
    $message = $input['message'];
    $jobId = $input['job_id'];

    // 1. Insérer le message
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, job_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$senderId, $receiverId, $message, $jobId]);
    
    // 2. Récupérer les infos de l'expéditeur (Recruteur) et du Job pour la notif
    // On suppose que c'est un recruteur qui écrit à un étudiant
    $stmtInfo = $pdo->prepare("SELECT name, company FROM users WHERE id = ?");
    $stmtInfo->execute([$senderId]);
    $senderInfo = $stmtInfo->fetch();

    if ($senderInfo) {
        // Préparation des données pour le bouton d'action JS
        $actionData = [
            'recruiter_id' => $senderId,
            'name' => $senderInfo['name'],
            'company' => $senderInfo['company'] ?? 'Entreprise',
            'job_id' => $jobId
        ];

        // Création de la notif avec les DATA
        createNotification($pdo, $receiverId, 'message', "Nouveau message", "{$senderInfo['name']} vous a envoyé un message.", $actionData);
    }

    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'get_messages') {
    if (!isset($_SESSION['user'])) { echo json_encode([]); exit; }
    
    $myId = $_SESSION['user']['id'];
    $otherId = $_GET['contact_id'];
    $jobId = $_GET['job_id']; // Nouveau paramètre

    // On filtre MAINTENANT aussi par job_id
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

function createNotification($pdo, $userId, $type, $title, $message, $data = null) {
    // On encode les données en JSON pour les stocker
    $dataJson = $data ? json_encode($data) : null;
    
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $type, $title, $message, $dataJson]);
}
if ($action === 'check_notifications') {
    if (!isset($_SESSION['user'])) { echo json_encode([]); exit; }
    
    $userId = $_SESSION['user']['id'];
    
    // Récupérer les notifs non lues
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at ASC");
    $stmt->execute([$userId]);
    $notifs = $stmt->fetchAll();

    // Marquer comme lues immédiatement après récupération
    if (count($notifs) > 0) {
        $stmtUpdate = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmtUpdate->execute([$userId]);
    }

    echo json_encode($notifs);
    exit;
}

if ($action === 'report_job') {
    if (!isset($_SESSION['user'])) { echo json_encode(['success'=>false, 'message'=>'Non connecté']); exit; }
    
    $stmt = $pdo->prepare("INSERT INTO reports (user_id, job_id, reason) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user']['id'], $input['job_id'], $input['reason']]);
    
    echo json_encode(['success' => true]);
    exit;
}

// 6. GESTION DES SIGNALEMENTS (ADMIN)
if ($action === 'get_reports') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { echo json_encode([]); exit; }
    
    // On récupère le signalement + info offre + info signaleur
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

if ($action === 'delete_report') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') exit;
    
    $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode(['success' => true]);
    exit;
}
?>