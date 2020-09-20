
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
var modal4 = document.getElementById('newShowHelpModal');
var modal5 = document.getElementById('newShowHelpModal1');
var modal6 = document.getElementById('newShowHelpModal2');
var modal7 = document.getElementById('newShowHelpModal3');
var modal8 = document.getElementById('newShowHelpModal4');
var closeModal = document.getElementById("closeModal");
var closeModal2 = document.getElementById("closeModal2");
var closeModal3 = document.getElementById("closeModal3");
var closeModal4 = document.getElementById("closeModal4");
var closeModal5 = document.getElementById("closeModal5");
var closeModal6 = document.getElementById("closeModal6");
var closeModal7 = document.getElementById("closeModal7");
var closeModal8 = document.getElementById("closeModal8");

closeModal.onclick = function() {
    modal.style.display = "none";
}

closeModal2.onclick = function() {
    modal2.style.display = "none";
}

closeModal3.onclick = function() {
    modal3.style.display = "none";
}

closeModal4.onclick = function() {
    modal4.style.display = "none";
}

closeModal5.onclick = function() {
    modal5.style.display = "none";
}

closeModal6.onclick = function() {
    modal6.style.display = "none";
}

closeModal7.onclick = function() {
    modal7.style.display = "none";
}

closeModal8.onclick = function() {
    modal8.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal || event.target == modal2 || event.target == modal3 || event.target == modal4
	|| event.target == modal5 || event.target == modal6 || event.target == modal7 || event.target == modal8) {
        modal.style.display = "none";
        modal2.style.display = "none";
        modal3.style.display = "none";
		modal4.style.display = "none";
		modal5.style.display = "none";
		modal6.style.display = "none";
		modal7.style.display = "none";
		modal8.style.display = "none";
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

function ShowHelpModal(){
	modal6.style.display = "none";
    modal5.style.display = "none";
	modal7.style.display = "none";
	modal8.style.display = "none";
    modal4.style.display = "block";
}

function ShowHelpModal1(){
	modal4.style.display = "none";
	modal6.style.display = "none";
	modal7.style.display = "none";
	modal8.style.display = "none";
    modal5.style.display = "block";
}

function ShowHelpModal2(){
	modal4.style.display = "none";
    modal5.style.display = "none";
	modal7.style.display = "none";
	modal8.style.display = "none";
	modal6.style.display = "block";
}

function ShowHelpModal3(){
	modal4.style.display = "none";
    modal5.style.display = "none";
	modal6.style.display = "none";
	modal8.style.display = "none";
	modal7.style.display = "block";
}

function ShowHelpModal4(){
	modal4.style.display = "none";
    modal5.style.display = "none";
	modal6.style.display = "none";
	modal7.style.display = "none";
	modal8.style.display = "block";
}


