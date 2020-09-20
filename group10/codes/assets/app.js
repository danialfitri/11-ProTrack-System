var signin_button = document.getElementById("signin_button");
var signup_button = document.getElementById("signup_button");

signin_button.addEventListener("click", function(){
    document.getElementById("in_container").style.display = "block";
    document.getElementById("up_container").style.display = "none";
});

signup_button.addEventListener("click", function(){
    document.getElementById("in_container").style.display = "none";
    document.getElementById("up_container").style.display = "block";
});