<?php
include '../settings/connect.php';


$limit = 5;
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$itemID = isset($_GET['itemID']) ? (int) $_GET['itemID'] : 0;

$sql = $con->prepare("
    SELECT r.*, c.clientFname, c.clientLname
    FROM tblrating r
    JOIN tblclient c ON r.clientID = c.clientID
    WHERE r.itemID = ?
    ORDER BY r.dateRate DESC
    LIMIT $limit OFFSET $offset
");
$sql->execute([$itemID]); // فقط $itemID كـ parameter
$reviews = $sql->fetchAll(PDO::FETCH_ASSOC);

foreach($reviews as $rev){
    echo '<div class="review">
        <div class="review-header" style="display:flex; justify-content:space-between;">
            <span class="client-name">'.htmlspecialchars($rev['clientFname'].' '.$rev['clientLname']).'</span>
            <span class="review-time">'.timeAgo($rev['dateRate']).'</span>
        </div>
        <div class="review-rate">'.str_repeat("★",$rev['rateScore']).str_repeat("☆",5-$rev['rateScore']).'</div>
        <div class="review-comment">'.nl2br(htmlspecialchars($rev['commentClient'])).'</div>
    </div>';
}

function timeAgo($datetime) {
                $time = strtotime($datetime);
                $diff = time() - $time;

                if ($diff < 60) return $diff . " seconds ago";
                $diff = round($diff / 60);
                if ($diff < 60) return $diff . " minutes ago";
                $diff = round($diff / 60);
                if ($diff < 24) return $diff . " hours ago";
                $diff = round($diff / 24);
                if ($diff < 30) return $diff . " days ago";
                return date("d M Y", $time);
            }
