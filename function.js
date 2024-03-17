//Check if Jquery is installed properly, will remove once it works for everyone.
window.onload = function() {
    if (window.jQuery) {  
        // jQuery is loaded  
        alert("Yeah! my jQuery is loaded!");
    } else {
        // jQuery is not loaded
        alert("My jQuery Doesn't Work");
    }
}

// Vanila JS:
// function redFunction() {
//     document.getElementById("blueParag").style.color = "red";
// };

//jQuery JS:
$(document).ready(function(){
    $("#redBtn").on("click", function(){
        $("#blueParag").css("color", "red");
        $("#blueParag").text("Now I'm red")
    });
});

