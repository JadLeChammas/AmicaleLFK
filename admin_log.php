<?php
// admin_log.php
// ⚠️ CE FICHIER NE DOIT PAS CONTENIR DE HTML

function logAdminAction(mysqli $conn, int $adminId, string $action, ?string $target = null): void
{
    $stmt = $conn->prepare("
        INSERT INTO admin_logs (admin_id, action, target, created_at)
        VALUES (?, ?, ?, NOW())
    ");

    if (!$stmt) {
        error_log("Admin log error: " . $conn->error);
        return;
    }

    $stmt->bind_param("iss", $adminId, $action, $target);
    $stmt->execute();
    $stmt->close();
}
