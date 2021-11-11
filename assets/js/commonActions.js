$(document).ready(() => {
    $(".navShowHide").on("click", () => {        
        let nav = $("#sideNavContainer");
        let main = $("#mainSectionContainer");

        if (main.hasClass("LeftPadding")) {
            nav.hide();
        } else {
            nav.show();
        }

        main.toggleClass("LeftPadding");
    })
});

notSignedIn = () => {
    alert("You must be signed in to perform this action")
}