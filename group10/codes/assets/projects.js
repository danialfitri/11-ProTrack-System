var currentTimes = Array();


function GetDate(timestamp, name){
    var t = timestamp.split(/[- :]/);
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var date = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));

    currentTimes.push({
        date: date,
        name: name,
    });
}

function DeleteReminder(reminderID){
    var url = window.location.href;
    if(url.indexOf("?") > 0) {
    url = url.substring(0, url.indexOf("?"));
    } 
    url += "?delReminder=" + reminderID;
    window.location.replace(url);
}

function CompleteGoal(goalID){
    var url = window.location.href;
    if(url.indexOf("?") > 0) {
    url = url.substring(0, url.indexOf("?"));
    } 
    url += "?checkGoal=" + goalID;
    window.location.replace(url);
}

function DeleteGoal(goalID){
    var url = window.location.href;
    if(url.indexOf("?") > 0) {
    url = url.substring(0, url.indexOf("?"));
    } 
    url += "?delGoal=" + goalID;
    window.location.replace(url);
}

function DeleteProject(projectID){
    var url = window.location.href;
    if(url.indexOf("?") > 0) {
    url = url.substring(0, url.indexOf("?"));
    } 
    url += "?delProject=" + projectID;
    window.location.replace(url);
}

function RefereeProjects(index){
    if(index == 0){
        window.location.assign("referees.php");
    }else{
        window.location.assign("projects.php");
    }
}