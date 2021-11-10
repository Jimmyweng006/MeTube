<?php 
    require_once("includes/header.php");
    require_once("includes/classes/VideoPlayer.php");
    require_once("includes/classes/VideoInfoSection.php");
?>
<script src="assets/js/videoPlayerAction.js"></script>

    <?php
        if (!isset($_GET["id"])) {
            echo "url not found";
            exit();
        }

        $video = new Video($con, $_GET["id"], $userLoggedInObj);
        $video->incrementViews();
    ?>

    <div class="watchLeftColumn">
        <?php
            $videoPlayer = new VideoPlayer($video);
            echo $videoPlayer->create(true);

            $videoInfoSection = new VideoInfoSection($con, $video, $userLoggedInObj);
            echo $videoInfoSection->create();
        ?>
    </div>

    <div class="suggestions">

    </div>



<?php require_once("includes/footer.php"); ?>