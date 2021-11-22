<?php
class SubscriptionProvider {

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos() {
        $videos = array();
        if (!User::isLoggedIn()) return $videos;
        $subscriptions = $this->userLoggedInObj->getSubscriptions();
        
        if (sizeof($subscriptions) > 0) {

            $condition = "";
            for ($i = 0; $i < sizeof($subscriptions); $i++) {
                if ($i != sizeof($subscriptions) - 1) {
                    $condition .= "uploadedBy = ? OR ";
                } else {
                    $condition .= "uploadedBy = ?";
                }
            }
            
            $videoSql = "SELECT * FROM videos WHERE $condition ORDER BY uploadDate DESC";
            $videoQuery = $this->con->prepare($videoSql);

            $i = 1;
            foreach ($subscriptions as $sub) {
                $videoQuery->bindParam($i, $subUsername);
                $subUsername = $sub->getUsername();
                $i++;
            }
            $videoQuery->execute();

            while ($row = $videoQuery->fetch(PDO::FETCH_ASSOC)) {
                $video = new Video($this->con, $row, $this->userLoggedInObj);
                array_push($videos, $video);
            }
        }

        return $videos;
    }
}
?>