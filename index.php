<?php require_once("includes/header.php"); ?>

    <?php
        if (isset($_SESSION["userLoggedIn"])) {
            echo "user logged in as " . $userLoggedInObj->getFirstName();
        } else {
            echo "user not logged in";
        }
    
    ?>

<?php require_once("includes/footer.php"); ?>