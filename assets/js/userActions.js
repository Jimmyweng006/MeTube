subscribe = (userTo, userFrom, button) => {
    // this is not gona happened(?
    if (userTo == userFrom) {
        alert("You can't subscribe to yourself");
        return;
    }

    $.post("ajax/subscribe.php", {userTo: userTo, userFrom: userFrom})
    .done((count) => {

        if (count != null) {
            $(button).toggleClass("subscribe unsubscribe");
            var buttonText = $(button).hasClass("subscribe") ? "SUBSCRIBE" : "SUBSCRIBED";
            $(button).text(buttonText + " " + count);
        } else {
            alert("Something went wrong");
        }        
    });
}
