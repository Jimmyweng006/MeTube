likeVideo = (button, videoId) => {
    $.post("ajax/likeVideo.php", {videoId: videoId})
    .done((data) => {
        
        var likeButton = $(button);
        var dislikeButton = $(button).siblings(".dislikeButton");

        likeButton.addClass("active");
        dislikeButton.removeClass("active");

        result = JSON.parse(data);
        updateVideoLikesValue(likeButton.find(".text"), result.likes);
        updateVideoLikesValue(dislikeButton.find(".text"), result.dislikes);

        if (result.likes < 0) {
            likeButton.removeClass("active");
            likeButton.find("img:first").attr("src", "assets/images/icons/thumb-up.png");
        } else {
            likeButton.find("img:first").attr("src", "assets/images/icons/thumb-up-active.png");
        }

        dislikeButton.find("img:first").attr("src", "assets/images/icons/thumb-down.png");
    });
}

dislikeVideo = (button, videoId) => {
    $.post("ajax/dislikeVideo.php", {videoId: videoId})
    .done((data) => {
        
        var dislikeButton = $(button);
        var likeButton = $(button).siblings(".likeButton");

        dislikeButton.addClass("active");
        likeButton.removeClass("active");

        result = JSON.parse(data);
        updateVideoLikesValue(dislikeButton.find(".text"), result.dislikes);
        updateVideoLikesValue(likeButton.find(".text"), result.likes);

        if (result.dislikes < 0) {
            dislikeButton.removeClass("active");
            dislikeButton.find("img:first").attr("src", "assets/images/icons/thumb-down.png");
        } else {
            dislikeButton.find("img:first").attr("src", "assets/images/icons/thumb-down-active.png");
        }

        likeButton.find("img:first").attr("src", "assets/images/icons/thumb-up.png");
    });
}

updateVideoLikesValue = (element, num) => {
    var likesCountVal = element.text() || 0;
    element.text(parseInt(likesCountVal) + parseInt(num));
}