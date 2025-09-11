<?php
    $sql=$con->prepare('SELECT Googlemap_KEY FROM tblsetting WHERE Setting_ID= 1');
    $sql->execute();
    $result=$sql->fetch();
    $googlemapKey = $result['Googlemap_KEY'];
?>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo $googlemapKey ?>&callback=initMap" async default></script>



