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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Referee Projects</title>

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
                <button type = "submit" name = "logout" class = "logout-button" ><i class="fa fa-sign-out" style="font-size:18px"></i></button>
            </form>
            <input onclick = "RefereeProjects(1)" id = "new_project" type = "submit" class = "logout-button ref-button" value = "My Projects">
        </div>

        <?php
            $query = "SELECT * FROM referees WHERE (name = ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 's', $_SESSION['uname']);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $num_rows = mysqli_num_rows($result);

            if($num_rows == 0){
                echo "<h1 class = 'notification'>You are not a referee in any project</h1>";
                echo "<h1 class = 'notification'>You need to be assigned as a referee in order to view other people's projects</h1>";
            }else{
                for($i = 0; $i < $num_rows; $i++){
                    $row1 = $result->fetch_assoc();
                    
                    $qa = "SELECT * FROM projects WHERE (id = ?)";
                    $st = mysqli_prepare($conn, $qa);
                    mysqli_stmt_bind_param($st, 'i', $row1['project_id']);
                    mysqli_stmt_execute($st);

                    $ra = mysqli_stmt_get_result($st);
                    $project = mysqli_num_rows($ra);

                    $row = $ra->fetch_assoc();

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
                <div class = "project-data">
                    <h2 class = "project-title">Project: <?php echo $row['name'];?></h2>
                    <p class = "project-desc">Description: <?php echo $row['description']; ?></p>
                    <svg width="120" height="120" viewBox="0 0 120 120" class = "progress">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e6e6e6" stroke-width="12" />
                        <circle transform = "rotate(-90 60 60)" cx="60" cy="60" r="54" fill="none" stroke="#f77a52" stroke-width="13"stroke-dasharray="<?php echo $progress_percentage;?> 339.292" stroke-linecap="round" />
                        <text x="35" y="65" font-family="Raleway" font-size="25" fill="white"><?php echo (int)(($progress_percentage/339.292) * 100); ?>%</text>
                    </svg>
                </div>

                <div class = "project-goals">
                    <div class = "project-working">
                        <h3>In Progress Goals</h1>
                        
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
                            <h4 class = "goal-title">Goal: <?php echo $goal['name']; ?></h4>
                            <div class = "goal-body">
                                <div style="color:white;" class = "date">Date created: (<?php
                                    $phpdate = strtotime($goal['starting_date']);
                                    $mysqldate = date( 'Y-m-d', $phpdate);
                                    echo $mysqldate;
                                ?>)</div>
                                <h4> Goal Description: <?php echo $goal['description']; ?></h4>

                                <p style="color:green;" class = "reminders-text">- REMINDERS -</p>

                                <?php 
                                    for($s = 0; $s < $reminders_num; $s++){ $reminder = $r4->fetch_assoc(); ?>
                                        <div class = "reminders">
                                            <?php 
                                                echo "<span class = 'reminder-name'>".$reminder['name']."</span>";
                                                
                                                echo "<script>GetDate('".$reminder['reminder_date']."', '".$reminder['name']."')</script>";
                                            ?>
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
                            <h4 class = "goal-title">Goal: <?php echo $cgoal['name']; ?></h4>
                            <div class = "goal-body">
                                <div  style="color:white;" class = "date">Date created: (<?php 
                                    $phpdate = strtotime($cgoal['starting_date']);
                                    $mysqldate = date( 'Y-m-d', $phpdate);
                                    echo $mysqldate;
                                ?>)</div>
                                <h4> Goal Description: <?php echo $cgoal['description']; ?></h4>

                                <p  style="color:green;" class = "reminders-text">- REMINDERS -</p>
                                <?php 
                                    for($s = 0; $s < $reminders_num; $s++){ $reminder = $r5->fetch_assoc(); ?>
                                        <div class = "reminders">
                                            <?php echo "<span class = 'reminder-name'>".$reminder['name']."</span>";
                                                echo "<script>GetDate('".$reminder['reminder_date']."', '".$reminder['name']."')</script>";
                                            ?>
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
        <?php 
            } 
            
        ?>
        <script src = "assets/referee.js"></script>
    </body>
</html>