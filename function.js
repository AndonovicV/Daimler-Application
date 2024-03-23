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


// Login Form Vanila JS
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const errorDisplay = document.getElementById("errorDisplay");

    loginForm.addEventListener("submit", function (event) {
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        // Basic validation
        if (!name || !email || !username || !password) {
            errorDisplay.textContent = "Please fill in all fields";
            event.preventDefault(); // Prevent form submission
        } else if (!validateEmail(email)) {
            errorDisplay.textContent = "Please enter a valid email address";
            event.preventDefault(); // Prevent form submission
        }
    });

    function validateEmail(email) {
        // Regular expression for email validation
        const re = /\S+@\S+\.\S+/;
        return re.test(email);
    }
});

