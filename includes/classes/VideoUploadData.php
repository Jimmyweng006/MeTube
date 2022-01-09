<?php
class VideoUploadData {

    private $videoDataArray, $title, $description, $privacy, $category, $uploadedBy;

    public function __construct($videoDataArray, $title, $description, $privacy, $category, $uploadedBy) {
        $this->videoDataArray = $videoDataArray;
        $this->title = $title;
        $this->description = $description;
        $this->privacy = $privacy;
        $this->category = $category;
        $this->uploadedBy = $uploadedBy;
    }

    public function getVideoDataArray() {
        return $this->videoDataArray;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getPrivacy() {
        return $this->privacy;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getUploadedBy() {
        return $this->uploadedBy;
    }

    public function updateDetails($con, $videoId) {
        $query = $con->prepare("UPDATE videos SET title = :title, description = :description, privacy = :privacy, category = :category 
        WHERE id = :videoId");
        $title = $this->getTitle();
        $description = $this->getDescription();
        $privacy = $this->getPrivacy();
        $category = $this->getCategory();

        $query->bindParam(":title", $title);
        $query->bindParam(":description", $description);
        $query->bindParam(":privacy", $privacy);
        $query->bindParam(":category", $category);
        $query->bindParam(":videoId", $videoId);
        
        return $query->execute();
    }
}
?>