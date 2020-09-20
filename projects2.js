
$("h4").click(function(){
    var el = $(this).parent().children(".goal-body");
    if(el.css("display") == "none"){
        el.css("display", "block");
        
    }else{
        el.css("display", "none");
    }
});


var modal = document.getElementById('newGoalModal');
var modal2 = document.getElementById('newReminderModal');
var modal3 = document.getElementById('newProjectModal');
var closeModal = document.getElementById("closeModal");
var closeModal2 = document.getElementById("closeModal2");
var closeModal3 = document.getElementById("closeModal3");

closeModal.onclick = function() {
    modal.style.display = "none";
}

closeModal2.onclick = function() {
    modal2.style.display = "none";
}

closeModal3.onclick = function() {
    modal3.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal || event.target == modal2 || event.target == modal3) {
        modal.style.display = "none";
        modal2.style.display = "none";
        modal3.style.display = "none";
    }
  }

function AddGoalModal(projectID){
    document.getElementById("hiddengoalid").value = projectID;
    modal.style.display = "block";
}

function AddReminderModal(goalID){
    document.getElementById("hiddenreminderid").value = goalID;
    modal2.style.display = "block";
}

function AddProjectModal(){
    modal3.style.display = "block";
}