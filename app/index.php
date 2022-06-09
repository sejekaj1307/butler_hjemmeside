<?php 
    session_start(); 
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/web/styles.css">
    <title>web app</title>
</head>

<body class="login_page">

    <!------------------------------
            Login form
    ------------------------------->
    <form class="loginForm" method="post">
        <?php
            $error_message = "";
            if($_SERVER['REQUEST_METHOD'] === 'POST') //array, webserver
            {
                //Login, if login button is pressed
                //Request to database, to see if playername and password match and exists in database
                if($_REQUEST['knap'] == "Login") //$_REQUEST — HTTP Request variables, $_request samler data efter en form er submitted. Det er et associative array som indeholder metoderne $_post, $_get og $_cookie. Så i array ligger "knap" som er et input field længere nede, med navnet "knap", hvis den knap er lig "login" køres if sætningen
                {
                    $useremail = $_REQUEST['email']; 
                    $passwordInput = $_REQUEST['password']; 
                    if($useremail != "") 
                    {   //If there's input in the input field, check if password matches
                        $sql = $conn->prepare("select * from employees where email = ?"); 
                        $sql->bind_param("s", $useremail);  
                        $sql->execute();
                        $result = $sql->get_result(); 
                        if($result->num_rows > 0) 
                        {
                            $row = $result->fetch_assoc(); 
                            $password = $row['password']; 
                            if($passwordInput == $password) 
                            {
                                $_SESSION['logged_in_user_global'] = array("id"=>$row["id"],"email"=>$useremail, "first_name"=>$row['first_name'], "last_name"=>$row['last_name'], "initials"=>$row['initials']);
                                //redicret user to time Registration page - when logged in
                                echo "<script> window.location.href = 'Time_registration/time_registration.php'; </script>"; //HEADER
                                
                            }
                            else { 
                                $error_message = "Forkert email eller password";
                                $error_message_color = "#ff0000";
                            }
                        }
                        else //if it doesn't match, tell user. 
                        {
                            $error_message = "Forkert email eller password";
                            $error_message_color = "#ff0000";
                        }
                    } 
                    else //If user clicks login without input
                    {
                        $error_message = "Venligst indtast dine loginoplysninger";
                        $error_message_color = "#ff0000";
                    }
                }
            }
        ?>

        <!------------------------------
                Actual login form
        ------------------------------->
        <div class="login_container">
            <h2 class="login_header">Login</h2>
            <input class="login_inputs" type="text" name="email" placeholder="Email addresse" value="<?php echo isset($username) ? $username : '' ?>">
            <input class="login_inputs" type="password" name="password" placeholder="Password" value="<?php echo isset($passwordInput) ? $passwordInput : '' ?>">
            <p>Glemt password? <a href="#">Klik her</a></p>

            <div class="loginErrorMessage"></div>
            

            <p class="error_message" style="color: <?php echo $error_message_color ?>"><?php echo $error_message ?> </p>
            <input class="login_btn" type="submit" name="knap" value="Login" >
        </div>
    </form>



    <?php 
        //connection to database closed
        $conn->close();
    ?>



</body>

</html>