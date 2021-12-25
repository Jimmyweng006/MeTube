<?php require_once("includes/header.php"); ?>

    <div class='videoSection'>

        <?php
            $SubscriptionsProvider = new SubscriptionsProvider($con, $userLoggedInObj);
            $subscriptionVideos = $SubscriptionsProvider->getVideos();

            $videoGrid = new VideoGrid($con, $userLoggedInObj);

            if (User::isLoggedIn() && sizeof($subscriptionVideos) > 0) {
                echo $videoGrid->create($subscriptionVideos, "Subscriptions", false);
            }

            echo $videoGrid->create(null, "Recommended", false);
        ?>

    </div>

<?php require_once("includes/footer.php"); ?>