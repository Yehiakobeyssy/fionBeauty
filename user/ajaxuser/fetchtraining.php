<?php
include '../../settings/connect.php';
session_start();

// Get user ID (from session or cookie)
if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = (int) $_COOKIE['user_id'];
} else {
    $user_id = 0;
}

// If user not logged in, return empty response or error
if ($user_id === 0) {
    echo json_encode([]);
    exit;
}

try {
    // Prepare SQL
    $stmt = $con->prepare("
        SELECT
            w.id ,
            w.title AS workshop,
            w.workshop_date AS Date,
            w.start_time AS Time,
            w.duration_hours AS Duration,
            w.cost AS Cost,
            w.is_online AS `Is Online`,
            b.booking_date AS `Booking Date`
        FROM workshop_bookings b
        INNER JOIN workshops w ON b.workshop_id = w.id
        WHERE b.user_id = :user_id
        ORDER BY b.booking_date DESC
    ");

    // Execute with parameter binding
    $stmt->execute(['user_id' => $user_id]);

    // Fetch all results
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($data);

} catch (PDOException $e) {
    // Handle errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>
