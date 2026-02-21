<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

/* Filtres */
$actionFilter = $_GET['action'] ?? '';
$adminFilter  = $_GET['admin'] ?? '';

$where = [];
$params = [];
$types  = '';

if ($actionFilter !== '') {
    $where[] = "l.action LIKE ?";
    $params[] = "%$actionFilter%";
    $types .= 's';
}

if ($adminFilter !== '') {
    $where[] = "m.email LIKE ?";
    $params[] = "%$adminFilter%";
    $types .= 's';
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

/* Pagination */
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

/* Total */
$countSql = "
    SELECT COUNT(*) 
    FROM admin_logs l
    JOIN membres m ON m.id = l.admin_id
    $whereSql
";
$stmt = $conn->prepare($countSql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_row()[0];
$stmt->close();

/* Logs */
$sql = "
    SELECT 
        l.id,
        l.action,
        l.target,
        l.created_at,
        m.nom,
        m.prenom,
        m.email
    FROM admin_logs l
    JOIN membres m ON m.id = l.admin_id
    $whereSql
    ORDER BY l.created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$logs = $stmt->get_result();
$stmt->close();

$totalPages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Historique des actions admin</title>
<link rel="stylesheet" href="css/admin_logs.css">
<link rel="icon" href="/img/lfk.png">
</head>

<body>

<div class="admin-container">
<a href="admin_dashboard.php" class="back-home">← Retour au dashboard</a>

<h1>📜 Historique des actions administrateur</h1>

<form method="GET" class="filters">
    <input type="text" name="action" placeholder="Action"
           value="<?= htmlspecialchars($actionFilter) ?>">
    <input type="text" name="admin" placeholder="Email admin"
           value="<?= htmlspecialchars($adminFilter) ?>">
    <button type="submit">Filtrer</button>
</form>

<table>
<thead>
<tr>
    <th>Date</th>
    <th>Admin</th>
    <th>Action</th>
    <th>Cible</th>
</tr>
</thead>
<tbody>
<?php while ($log = $logs->fetch_assoc()): ?>
<tr>
    <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
    <td>
        <?= htmlspecialchars($log['prenom'].' '.$log['nom']) ?><br>
        <small><?= htmlspecialchars($log['email']) ?></small>
    </td>
    <td><?= htmlspecialchars($log['action']) ?></td>
    <td><?= htmlspecialchars($log['target'] ?? '-') ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<div class="pagination">
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a class="<?= $i === $page ? 'active' : '' ?>"
       href="?page=<?= $i ?>&action=<?= urlencode($actionFilter) ?>&admin=<?= urlencode($adminFilter) ?>">
        <?= $i ?>
    </a>
<?php endfor; ?>
</div>

</div>

</body>
</html>
