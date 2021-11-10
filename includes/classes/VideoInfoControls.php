<?php
require_once("includes/classes/ButtonProvider.php");
class VideoInfoControls {
    
    private $video, $userLoggedInObj;

    public function __construct($video, $userLoggedInObj) {
        $this->video = $video;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create() {
        $likeButton = $this->createLikeButton();
        $dislikeButton = $this->createDislikeButton();
        
        return "<div class='controls'>
                    $likeButton
                    $dislikeButton
                </div>";
    }

    private function createLikeButton() {
        $text = $this->video->getLikes();
        $imageSrc = "assets/images/icons/thumb-up.png";
        $videoId = $this->video->getId();
        $action = "likeVideo(this, $videoId)";
        $class = "likeButton";

        // change button img if video has been liked/disliked

        return ButtonProvider::createButton($text, $imageSrc, $action, $class);
    }

    private function createDislikeButton() {
        $text = $this->video->getDislikes();
        $imageSrc = "assets/images/icons/thumb-down.png";
        $videoId = $this->video->getId();
        $action = "dislikeVideo(this, $videoId)";
        $class = "dislikeButton";

        // change button img if video has been liked/disliked

        return ButtonProvider::createButton($text, $imageSrc, $action, $class);
    }
}
?>