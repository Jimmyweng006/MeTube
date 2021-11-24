<?php 
    require_once("includes/config.php");
    require_once("includes/classes/ButtonProvider.php");
    require_once("includes/classes/User.php");
    require_once("includes/classes/Video.php");
    require_once("includes/classes/VideoGrid.php");
    require_once("includes/classes/VideoGridItem.php");
    require_once("includes/classes/SubscriptionProvider.php");

    $usernameLoggedIn = User::isLoggedIn() ? $_SESSION["userLoggedIn"] : "";
    $userLoggedInObj = new User($con, $usernameLoggedIn);
?>

<?php
    if ($usernameLoggedIn == "") {
        $link = ButtonProvider::$signInFunction;
        $uploadPic = "<a onclick='$link'>
                        <img class='upload' src='assets/images/icons/upload.png' alt='upload video'>
                    </a>";
    } else {
        $uploadPic = "<a href='upload.php'>
                        <img class='upload' src='assets/images/icons/upload.png' alt='upload video'>
                    </a>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MeTube</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="assets/js/commonActions.js"></script>
    <script src="assets/js/userActions.js"></script>
</head>
<body>

    <div id="pageContainer">

        <div id="mastHeadContainer">
            <button class="navShowHide">
                <img src="assets/images/icons/menu.png">
            </button>

            <a class="logoContainer" href="index.php">
                <img src="assets/images/icons/MeTubeLogo.png" title="logo" alt="site logo">
            </a>

            <div class="searchBarContainer">
                <form action="search.php" method="GET">
                    <input type="text" class="searchBar" name="term" placeholder="Search">
                    <button class="searchButton">
                        <img src="assets/images/icons/search.png" alt="search">
                    </button>
                </form>
            </div>

            <div class="rightIcons">
                <?php
                    echo $uploadPic
                ?>
                <a href="signIn.php">
                    <img class="upload" src="assets/images/profilePictures/default.png" alt="profile picture">
                </a>
            </div>

        </div>

        <div id="sideNavContainer" style="display: none">

        </div>

        <div id="mainSectionContainer">

            <div id="mainContentContainer">