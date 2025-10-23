<?php
session_start();

if (isset($_POST['workshop_id'])) {
    $workshop_id = (int) $_POST['workshop_id'];

    if (!isset($_SESSION['workoutCart'])) {
        $_SESSION['workoutCart'] = [];
    }

    if (!in_array($workshop_id, $_SESSION['workoutCart'])) {
        $_SESSION['workoutCart'][] = $workshop_id;
        echo "✅ Workshop added to your cart!";
    } else {
        echo "⚠ You already booked this workshop!";
    }
} else {
    echo "❌ Invalid request!";
}
