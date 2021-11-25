<?php
class VideoProcessor {
    
    private $con;
    private $sizeLimit = 500000000;
    private $allowedTypes = array("mp4", "flv", "webm", "mkv", "vob", "ogv", "ogg", "avi", "wmv", "mov", "mpeg", "mpg");
    private $ffmpegPath = "/usr/bin/ffmpeg";
    private $ffprobePath = "/usr/bin/ffprobe";

    public function __construct($con) {
        $this->con = $con;
    }

    public function upload($videoUploadData) {

        $targetDir = "uploads/videos/";
        $videoData = $videoUploadData->getVideoDataArray();

        $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);
        $tempFilePath = str_replace(" ", "_", $tempFilePath);

        $isValidData = $this->processData($videoData, $tempFilePath);

        if (!$isValidData) {
            echo "File upload process fail!";
            return false;
        }

        if (move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            
            $finalFilePath = $targetDir . uniqid() . ".mp4";

            if (!$this->insertVideoData($videoUploadData, $finalFilePath)) {
                echo "Insert query failed.\n";
                return false;
            }

            if (!$this->convertVideoToMp4($tempFilePath, $finalFilePath)) {
                echo "Video convert failed.\n";
                return false;
            }

            if (!$this->deleteFile($tempFilePath)) {
                echo "Delete temp file failed.\n";
                return false;
            }

            if (!$this->generateThumbnails($finalFilePath)) {
                echo "Generate thumbnails failed.\n";
                return false;
            }
        }

        return true;
    }

    private function processData($videoData, $filePath) {
        $videoType = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!$this->isValidSize($videoData)) {
            echo "File to large. Can't exceed " . $this->sizeLimit . " bytes.";
            return false;
        } else if (!$this->isValidType($videoType)) {
            echo "Not supported file types.";
            return false;
        } else if ($this->hasError($videoData)) {
            echo "Error code: " . $videoData["error"];
            return false;
        }

        return true;
    }

    private function isValidSize($data) {
        return $data["size"] <= $this->sizeLimit;
    }

    private function isValidType($type) {
        $lowercased = strtolower($type);
        return in_array($type, $this->allowedTypes);
    }

    private function hasError($data) {
        return $data["error"] != 0;
    }

    private function insertVideoData($uploadData, $filePath) {
        $query = $this->con->prepare("INSERT INTO videos(title, uploadedBy, description, privacy, category, filePath, duration)
                                    VALUES(:title, :uploadedBy, :description, :privacy, :category, :filePath, :duration)");
        
        $title = $uploadData->getTitle();
        $uploadedBy = $uploadData->getUploadedBy();
        $description = $uploadData->getDescription();
        $privacy = $uploadData->getPrivacy();
        $category = $uploadData->getCategory();
        $zero = 0;

        $query->bindParam(":title", $title);
        $query->bindParam(":uploadedBy", $uploadedBy);
        $query->bindParam(":description", $description);
        $query->bindParam(":privacy", $privacy);
        $query->bindParam(":category", $category);
        $query->bindParam(":filePath", $filePath);
        $query->bindParam(":duration", $zero);
 
        return $query->execute();
    }

    public function convertVideoToMp4($tempFilePath, $finalFilePath) {
        $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1";

        $outputLog = array();
        exec($cmd, $outputLog, $returnCode);

        if ($returnCode != 0) {
            foreach($outputLog as $line) {
                echo $line . "<br>";
            }
            return false;
        }

        return true;
    }

    private function deleteFile($filePath) {
        if (!unlink($filePath)) {
            echo "Could not delete file.\n";
            return false;
        }

        return true;
    }

    public function generateThumbnails($filePath) {
        $thumbnailSize = "210*118";
        $numThumbnails = 3;
        $pathToThumbnail = "uploads/videos/thumbnails";

        $duration = $this->getVideoDuration($filePath);

        $videoId = $this->con->lastInsertId();
        $this->updateDuration($duration, $videoId);

        for ($num = 1; $num <= $numThumbnails; $num++) {
            $imageName = uniqid() . ".jpg";
            $interval = ($duration * 0.8) / $numThumbnails * $num;
            $fullThumbnailPath = "$pathToThumbnail/$videoId-$imageName";

            $cmd = "$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";

            $outputLog = array();
            exec($cmd, $outputLog, $returnCode);

            if ($returnCode != 0) {
                foreach($outputLog as $line) {
                    echo $line . "<br>";
                }
            }

            $query = $this->con->prepare("INSERT INTO thumbnails (videoId, filePath, selected)
                                        VALUES(:videoId, :filePath, :selected)");

            $selected = $num == 1 ? 1 : 0;
            $query->bindParam(":videoId", $videoId);
            $query->bindParam(":filePath", $fullThumbnailPath);
            $query->bindParam(":selected", $selected);

            $success = $query->execute();

            if (!$success) {
                echo "Error inserting thunmnail!\n";
                return false;
            }
        }

        return true;
    }

    private function getVideoDuration($filePath) {
        return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }

    private function updateDuration($duration, $videoId) {
        $hours = floor($duration / 3600);
        $duration %= 3600;

        $minutes = floor($duration / 60);
        $duration %= 60;

        $seconds = $duration;
        
        $hours = $hours < 1 ? "" : $hours . ":";
        $minutes = $minutes < 10 ? "0$minutes" . ":" : "$minutes" . ":";
        $seconds = $seconds < 10 ? "0$seconds" : "$seconds";

        $duration = $hours . $minutes . $seconds;

        $query = $this->con->prepare("UPDATE videos SET duration=:duration WHERE id = :videoId");
        $query->bindParam(":duration", $duration);
        $query->bindParam(":videoId", $videoId);

        $query->execute();
    }
}
?>