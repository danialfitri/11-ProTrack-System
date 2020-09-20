<?php
    session_start();
    include("connection.php");

    $upError = "";
    $inError = "";

    if(isset($_SESSION['ProTrackAccessToken'])){
        header("Location: projects.php");
        exit();
    }else if(isset($_COOKIE['ProTrackAccessToken'])){
        echo $_COOKIE['ProTrackAccessToken'];
        header("Location: projects.php");
        exit();
    }else{
        if(isset($_POST['signup']) && isset($_POST['username']) && isset($_POST['organization']) && isset($_POST['occupation']) 
			&& isset($_POST['password']) && isset($_POST['email'])){
            if(userExists($conn) != false){
                $upError = "User already exists !!";
            }else{
                $stmt = $conn->prepare("INSERT INTO users (username, organization, occupation, email, password, token) VALUES (?, ?, ?, ?, ?, ?)");
                $token = generateRandomString(256);
                $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt->bind_param("ssssss", $_POST['username'], $_POST['organization'], $_POST['occupation'], $_POST['email'], $pass, $token);
                $stmt->execute();
                $_SESSION['ProTrackAccessToken'] = $token;
                setcookie("ProTrackAccessToken", $token, time() + (86400 * 30 * 12));
                header("Location: projects.php");
            }
        }else if(isset($_POST['signin']) && isset($_POST['username']) && isset($_POST['password'])){
            $user = userExists($conn);
            if($user == false){
                $inError = "User doesn't exists !";
            }else{
                $row = $user->fetch_assoc();
                $verify = password_verify($_POST['password'], $row['password']);
                if($verify){
                    $_SESSION['ProTrackAccessToken'] = $row['token'];
                    setcookie("ProTrackAccessToken", $row['token'], time() + (86400 * 30 * 12));
                    header("Location: projects.php");
                }else{
                    $inError = "Password isn't correct !";
                }
            }
        }
    }


    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function userExists($conn){
        $query = "SELECT username, password, token FROM users WHERE (username = ? OR email = ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $_POST['username'], $_POST['username']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $num_rows = mysqli_num_rows($result);

        if($num_rows == 0){
            return false;
        }else{
            return $result;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>ProTrack</title>
        
        <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
        <link rel = "stylesheet" href = "assets/style.css">
    </head>
    <body>
        <h1 class = "title">Welcome to Pro-Track</h1>
        <div class="container" style="height:550px;">
            <div class = "top-bar">
                <div id = "signin_button" class = "sign-button left">Signin</div>
                <div id = "signup_button" class = "sign-button right">Signup</div>
            </div>

            <p class = "error"><?php echo $inError ?></p>
            <p class = "error"><?php echo $upError ?></p>

            <form id = "in_container" class = "sign-container" method="POST">
                <input class = "input" type = "text" name = "username" placeholder="Your Username..">
                <input class = "input"  type = "password" name = "password" placeholder="Your Password">
                <input type = "submit" id = "signin" name = "signin" class = "submit-button" value = "Signin">
            </form>

            <form id = "up_container" class = "sign-container" method="POST">
                <input class = "input" type = "text" name = "username" placeholder="Your Username..">
				<input class = "input" type = "text" name = "organization" placeholder="Your Organization (University,Company,etc)..">
				<input class = "input" type = "text" name = "occupation" placeholder="Your Occupation..">
                <input class = "input" type = "email" name = "email" placeholder="Your Email..">
                <input class = "input"  type = "password" name = "password" placeholder="Your Password">
                <input type = "submit" id = "signup" name = "signup" class = "submit-button" value = "Signup">
            </form>
        </div>

        <script src = "assets/app.js"></script>
    </body>
</html>
