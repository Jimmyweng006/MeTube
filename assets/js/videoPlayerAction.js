likeVideo = (button, videoId) => {
    $.post("ajax/likeVideo.php", {videoId: videoId})
    .done((data) => {
        alert(data);
    });
}