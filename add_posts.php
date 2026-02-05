
<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAnyRole(); // ✅ Tous les utilisateurs connectés

// 🔍 Debug PHP (à désactiver en prod)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Type de retour JSON
header('Content-Type: application/json');

// 📝 Récupération des données du formulaire
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];
$image_path = null;

// ✅ Vérification des champs obligatoires
if (empty($title) || empty($content)) {
    echo json_encode(["status" => "error", "message" => "Veuillez remplir tous les champs."]);
    exit();
}

// 🖼️ Gestion de l'upload d'image
if (!empty($_FILES['image']['name'])) {
    $upload_dir = __DIR__ . "/uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_name = time() . "_" . basename($_FILES['image']['name']);
    $target_file = $upload_dir . $image_name;
    $image_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($image_ext, $allowed_extensions)) {
        echo json_encode(["status" => "error", "message" => "Format d'image non autorisé. (jpg, jpeg, png, gif uniquement)"]);
        exit();
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_path = "uploads/" . $image_name;
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur lors du téléchargement de l'image."]);
        exit();
    }
}

// 🔗 Connexion à la base de données (mysqli)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ⚠️ Vérification de la connexion
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connexion échouée : " . $conn->connect_error]);
    exit();
}

// 📤 Insertion dans la base de données
$query = "INSERT INTO publications (user_id, title, content, image, created_at, status) 
          VALUES (?, ?, ?, ?, NOW(), 'pending')";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Erreur préparation SQL : " . $conn->error]);
    exit();
}

// ⚠️ Assure que $image_path est bien une string
if ($image_path === null) {
    $image_path = "";
}

$stmt->bind_param("isss", $user_id, $title, $content, $image_path);
$success = $stmt->execute();

if ($success) {
    echo json_encode(["status" => "success", "message" => "Publication soumise pour validation"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la publication : " . $stmt->error]);
}

// 🧹 Nettoyage
$stmt->close();
$conn->close();
?>
