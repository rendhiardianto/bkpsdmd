<?php

// Include database connection
require_once __DIR__ . '/../../db.php';

/**
 * Get counts of submissions by status
 * Returns associative array:
 * [
 *   'pending' => 5,
 *   'approved' => 12,
 *   'rejected' => 3,
 *   'all' => 20
 * ]
 */
function getSubmissionCounts($conn) {
    $counts = [
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'all' => 0
    ];

    // Query to count by status
    $sql = "
        SELECT status, COUNT(*) AS total
        FROM service_submissions
        GROUP BY status
    ";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $status = strtolower($row['status']);
            if (isset($counts[$status])) {
                $counts[$status] = (int)$row['total'];
            }
            $counts['all'] += (int)$row['total'];
        }
    }

    return $counts;
}

/**
 * Optional: Get count for a specific status only
 */
function getCountByStatus($conn, $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM service_submissions WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return (int)$result['total'];
}
?>
