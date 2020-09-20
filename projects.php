<?php
    session_start();
    include("connection.php");

    if(isset($_SESSION['ProTrackAccessToken']) || isset($_COOKIE['ProTrackAccessToken'])){
        ValidateToken($conn);
    }
    else if(isset($_COOKIE['ProTrackAccessToken'])){
        $_SESSION['ProTrackAccessToken'] = $_COOKIE['ProTrackAccessToken'];
        ValidateToken($conn);
    }else{
        header("Location: index.php");
        exit();
    }

    function ValidateToken($conn){
        $query = "SELECT id, username, token FROM users WHERE (token = ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['ProTrackAccessToken']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $num_rows = mysqli_num_rows($result);

        if($num_rows == 0){
            Logout();
        }else{
            $row = $result->fetch_assoc();
            if($_SESSION['ProTrackAccessToken'] !== $row['token']){
                Logout();
            }else{
                $_SESSION['uname'] = $row['username'];
                $_SESSION['user_id'] = $row['id'];
            }
        }
    }

    function Logout(){
        session_unset();
        session_destroy();
        $_SESSION = array();
        unset($_SESSION['ProTrackAccessToken']);
        unset($_COOKIE['ProTrackAccessToken']);
        setcookie("ProTrackAccessToken", "", time()-3600);
        header("Location: index.php");
        exit();
    }
?>

<?php
    if(isset($_GET['logout'])){
        Logout();
    }

    if(isset($_GET['delReminder'])){
        if(CheckReminderOwnerShip($conn, $_GET['delReminder'])){
            $deleteQuery = "DELETE FROM reminders WHERE (id = ?)";
            $delstatement = mysqli_prepare($conn, $deleteQuery);
            mysqli_stmt_bind_param($delstatement, 'i', $_GET['delReminder']);
            mysqli_stmt_execute($delstatement);
        }
        
    }

    if(isset($_GET['delGoal'])){
        if(CheckGoalOwnerShip($conn, $_GET['delGoal'])){
            $deleteQuery = "DELETE FROM goals WHERE (id = ?)";
            $delstatement = mysqli_prepare($conn, $deleteQuery);
            mysqli_stmt_bind_param($delstatement, 'i', $_GET['delGoal']);
            mysqli_stmt_execute($delstatement);
        }
        
    }

    if(isset($_GET['checkGoal'])){
        if(CheckGoalOwnerShip($conn, $_GET['checkGoal'])){
            $updateQuery = "UPDATE goals SET completed=? WHERE (id = ?)";
            $updatestatement = mysqli_prepare($conn, $updateQuery);
            $completed = 1;
            mysqli_stmt_bind_param($updatestatement, 'ii', $completed, $_GET['checkGoal']);
            mysqli_stmt_execute($updatestatement);
        }
        
    }

    if(isset($_GET['delProject'])){
        if(CheckProjectOwnerShip($conn, $_GET['delProject'])){
            $deleteQuery = "DELETE FROM projects WHERE (id = ?)";
            $delstatement = mysqli_prepare($conn, $deleteQuery);
            mysqli_stmt_bind_param($delstatement, 'i', $_GET['delProject']);
            mysqli_stmt_execute($delstatement);
        }
        
    }

    function CheckReminderOwnerShip($conn, $delid){
        $query = "SELECT goal_id FROM reminders WHERE (id = ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $delid);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if($result){
            $reminder = $result->fetch_assoc();
            return CheckGoalOwnerShip($conn, $reminder['goal_id']);
        }

        return false;
    }

    function CheckGoalOwnerShip($conn, $goalid){
        $query = "SELECT project_id FROM goals WHERE (id = ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $goalid);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if($result){
            $goal = $result->fetch_assoc();
            return CheckProjectOwnerShip($conn, $goal['project_id']);
        }

        return false;
    }

    function CheckProjectOwnerShip($conn, $projectid){
        $query = "SELECT owner_id FROM projects WHERE (id = ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $projectid);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if($result){
            $project = $result->fetch_assoc();
            if($project['owner_id'] == $_SESSION['user_id']){
                return true;
            }
        }

        return false;
    }
?>

<?php
    if(isset($_GET['addreminder'])){
        if(CheckGoalOwnerShip($conn, $_GET['goalid'])){
            $addq = "INSERT INTO reminders (name, goal_id, reminder_date) VALUES (?, ?, ?)";
            $sta = mysqli_prepare($conn, $addq);
            mysqli_stmt_bind_param($sta, 'sis', $_GET['name'], $_GET['goalid'], $_GET['date']);
            mysqli_stmt_execute($sta);
        }
    }

    if(isset($_GET['addgoal'])){
        if(CheckProjectOwnerShip($conn, $_GET['projectid'])){
            $addq = "INSERT INTO goals (name, description, project_id) VALUES (?, ?, ?)";
            $sta = mysqli_prepare($conn, $addq);
            mysqli_stmt_bind_param($sta, 'ssi', $_GET['name'], $_GET['description'], $_GET['projectid']);
            mysqli_stmt_execute($sta);
        }
    }

    if(isset($_GET['addproject'])){
        $addq = "INSERT INTO projects (name, description, owner_id) VALUES (?, ?, ?)";
        $sta = mysqli_prepare($conn, $addq);
        mysqli_stmt_bind_param($sta, 'ssi', $_GET['name'], $_GET['description'], $_SESSION['user_id']);
        mysqli_stmt_execute($sta);

        $newid = mysqli_insert_id($conn);

        if(isset($_GET['referees']) && !empty($_GET['referees'])){
            $referees = explode(",", $_GET['referees']);
            foreach($referees as $referee) {
                $addq = "INSERT INTO referees (name, project_id) VALUES (?, ?)";
                $sta = mysqli_prepare($conn, $addq);
                mysqli_stmt_bind_param($sta, 'si', $referee, $newid);
                mysqli_stmt_execute($sta);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>My Projects</title>

        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel = "stylesheet" href = "assets/style.css">
    </head>
    <body>
        <header>
            <div class="navbar">
                <span class = "nav-title">Welcome <?php echo  $_SESSION['uname']; ?></span>
            </div>
        </header>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src = "assets/projects.js"></script>

        <div class = "Links">
            <form>
                <input type = "submit" name = "logout" class = "logout-button" value = "Logout">
            </form>
            <input onclick = "AddProjectModal()" id = "new_project" type = "submit" class = "logout-button new-button" value = "New Project">
            <input onclick = "RefereeProjects(0)" id = "new_project" type = "submit" class = "logout-button ref-button" value = "Referee Projects">
        </div>


        <?php 
            $query = "SELECT * FROM projects WHERE (owner_id = ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
    
            $result = mysqli_stmt_get_result($stmt);
            $num_rows = mysqli_num_rows($result);
    
            for($i = 0; $i < $num_rows; $i++){
                $row = $result->fetch_assoc();

                $q = "SELECT * FROM goals WHERE (project_id = ? AND completed = ?)";
                $stmt2 = mysqli_prepare($conn, $q);
                $not_completed = 0;
                mysqli_stmt_bind_param($stmt2, 'ii', $row['id'], $not_completed);
                mysqli_stmt_execute($stmt2);
        
                $r2 = mysqli_stmt_get_result($stmt2);
                $goals_num = mysqli_num_rows($r2);


                $q3 = "SELECT * FROM goals WHERE (project_id = ? AND completed = ?)";
                $stmt3 = mysqli_prepare($conn, $q3);
                $completed = 1;
                mysqli_stmt_bind_param($stmt3, 'ii', $row['id'], $completed);
                mysqli_stmt_execute($stmt3);
        
                $r3 = mysqli_stmt_get_result($stmt3);
                $cgoals_num = mysqli_num_rows($r3);
                
                if($cgoals_num > 0)
                    $progress_percentage = 339.292 * (($cgoals_num / ($cgoals_num + $goals_num)));
                else $progress_percentage = 0;
        ?>
            <div class="projects-container">
                <span class = "fa fa-close project-close" onclick = "DeleteProject(<?php echo $row['id']; ?>)"></span>
                <div class = "project-data">
                    <h2 class = "project-title"><?php echo $row['name'];?></h2>
                    <p class = "project-desc"><?php echo $row['description']; ?></p>
                    <svg width="120" height="120" viewBox="0 0 120 120" class = "progress">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e6e6e6" stroke-width="12" />
                        <circle transform = "rotate(-90 60 60)" cx="60" cy="60" r="54" fill="none" stroke="#f77a52" stroke-width="13"stroke-dasharray="<?php echo $progress_percentage;?> 339.292" stroke-linecap="round" />
                        <text x="35" y="65" font-family="Raleway" font-size="25" fill="white"><?php echo (int)(($progress_percentage/339.292) * 100); ?>%</text>
                    </svg>
                </div>

                <div class = "project-goals">
                    <div class = "project-working">
                        <h3>In Progress Goals<span class = "fa fa-plus" onclick="AddGoalModal(<?php echo $row['id']; ?>)"></span></h1>
                        
                        <?php 
                            for($j = 0; $j < $goals_num; $j++){
                                $goal = $r2->fetch_assoc();
                                $q4 = "SELECT * FROM reminders WHERE (goal_id = ?)";
                                $stmt4 = mysqli_prepare($conn, $q4);
                                mysqli_stmt_bind_param($stmt4, 'i', $goal['id']);
                                mysqli_stmt_execute($stmt4);

                                $r4 = mysqli_stmt_get_result($stmt4);
                                $reminders_num = mysqli_num_rows($r4);
                        ?>
                        <div class = "goal">
                            <h4 class = "goal-title"><span class = "fa fa-plus add-reminder" onclick="AddReminderModal(<?php echo $goal['id']; ?>)"></span><?php echo $goal['name']; ?></h4>
                            <span class = "fa fa-check" onclick="CompleteGoal(<?php echo $goal['id']; ?>)"></span>
                            <div class = "goal-body">
                                <div class = "date">- <?php
                                    $phpdate = strtotime($goal['starting_date']);
                                    $mysqldate = date( 'Y-m-d', $phpdate);
                                    echo $mysqldate;
                                ?> -</div>
                                <?php echo $goal['description']; ?>

                                <p class = "reminders-text">- REMINDERS -</p>

                                <?php 
                                    for($s = 0; $s < $reminders_num; $s++){ $reminder = $r4->fetch_assoc(); ?>
                                        <div class = "reminders">
                                            <?php 
                                                echo "<span class = 'reminder-name'>".$reminder['name']."</span>";
                                                
                                                echo "<script>GetDate('".$reminder['reminder_date']."', '".$reminder['name']."')</script>";
                                            ?>
                                            <span class = "fa fa-close reminder-close" onclick="DeleteReminder(<?php echo $reminder['id'] ?>)"></span>
                                        </div>
                                    <?php }
                                    ?>
                            </div>
                        </div>
                        <?php } ?>
                
                    </div>

                    <div class = "project-completed">
                        <h3>Completed Goals</h1>
                        <?php
                            for($k = 0; $k < $cgoals_num; $k++){
                                $cgoal = $r3->fetch_assoc();
                                $q5 = "SELECT * FROM reminders WHERE (goal_id = ?)";
                                $stmt5 = mysqli_prepare($conn, $q5);
                                mysqli_stmt_bind_param($stmt5, 'i', $cgoal['id']);
                                mysqli_stmt_execute($stmt5);

                                $r5 = mysqli_stmt_get_result($stmt5);
                                $reminders_num = mysqli_num_rows($r5);
                        ?>
                        <div class = "goal">
                            <h4 class = "goal-title"><?php echo $cgoal['name']; ?></h4>
                            <span class = "fa fa-close" onclick="DeleteGoal(<?php echo $cgoal['id']; ?>)"></span>
                            <div class = "goal-body">
                                <div class = "date">- <?php 
                                    $phpdate = strtotime($cgoal['starting_date']);
                                    $mysqldate = date( 'Y-m-d', $phpdate);
                                    echo $mysqldate;
                                ?> -</div>
                                <?php echo $cgoal['description']; ?>

                                <p class = "reminders-text">- REMINDERS -</p>
                                <?php 
                                    for($s = 0; $s < $reminders_num; $s++){ $reminder = $r5->fetch_assoc(); ?>
                                        <div class = "reminders">
                                            <?php echo "<span class = 'reminder-name'>".$reminder['name']."</span>";
                                                echo "<script>GetDate('".$reminder['reminder_date']."', '".$reminder['name']."')</script>";
                                            ?>
                                            <span class = "fa fa-close reminder-close" onclick="DeleteReminder(<?php echo $reminder['id'] ?>)"></span>
                                        </div>
                                    <?php }
                                    ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div id="newReminderModal" class="modal">
            <div class="modal-content">
                <span class="close" id = "closeModal2">&times;</span>
                <form class = "sign-container" method="GET">
                    <h1 class = "modal-title">Add a new Reminder</h1>
                    <input id = "hiddenreminderid" style="display: none;" name = "goalid" value = "">
                    <input class = "input" type = "text" name = "name" placeholder="Name of the reminder" required>
                    <input class = "input" type="date" name="date" required>
                    <input type = "submit" name = "addreminder" class = "submit-button" value = "Add Reminder">
                </form>
            </div>
        </div>

        <div id="newGoalModal" class="modal">
            <div class="modal-content">
                <span class="close" id = "closeModal">&times;</span>
                <form class = "sign-container" method="GET">
                    <h1 class = "modal-title">Add a new Goal</h1>
                    <input id = "hiddengoalid" style="display: none;" name = "projectid" value = "">
                    <input class = "input" type = "text" name = "name" placeholder="Name of the goal" required>
                    <input class = "input" type = "text" name = "description" placeholder="Describe the goal" required>
                    <input type = "submit" name = "addgoal" class = "submit-button" value = "Add Goal">
                </form>
            </div>
        </div>

        <div id="newProjectModal" class="modal">
            <div class="modal-content">
                <span class="close" id = "closeModal3">&times;</span>
                <form class = "sign-container" method="GET">
                    <h1 class = "modal-title">Add a new Project</h1>
                    <input class = "input" type = "text" name = "name" placeholder="Name of the Project" required>
                    <input class = "input" type = "text" name = "description" placeholder="Describe the project" required>
                    <input class = "input" type = "text" name = "referees" placeholder="Add referees names seperated by a coma">
                    <input type = "submit" name = "addproject" class = "submit-button" value = "Add Project">
                </form>
            </div>
        </div>
                       
        <script src = "assets/projects2.js"></script>
    </body>
</html>