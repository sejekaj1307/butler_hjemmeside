<?php 
    session_start(); 
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/stylesheet.css">
    <title>web app</title>
</head>

<body>

    <!------------------------------
            Login form
    ------------------------------->
    <form class="loginForm" method="post">
        <?php
            $fejltekst = "";
            if($_SERVER['REQUEST_METHOD'] === 'POST') //array, webserver
            {
                //Login, if login button is pressed
                //Request to database, to see if playername and password match and exists in database
                if($_REQUEST['knap'] == "login") //$_REQUEST — HTTP Request variables, $_request samler data efter en form er submitted. Det er et associative array som indeholder metoderne $_post, $_get og $_cookie. Så i array ligger "knap" som er et input field længere nede, med navnet "knap", hvis den knap er lig "login" køres if sætningen
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
                                // echo "<script> window.location.href = 'Medarbejdere/ansatte.php'; </script>"; //HEADER
                                echo "succes";
                            }
                            else { 
                                $fejltekst = "Forkert email eller password";
                                $tekstfarve = "#000000";
                            }
                        }
                        else //if it doesn't match, tell user. 
                        {
                            $fejltekst = "Brugernavn findes ikke";
                            $tekstfarve = "#ff0000";
                        }
                    } 
                    else //If user clicks login without input
                    {
                        $fejltekst = "Venligst indtast dine loginoplysninger";
                        $tekstfarve = "#ff0000";
                    }
                }
            }
        ?>

        <!------------------------------
                Actual login form
        ------------------------------->
        <input type="text" name="email" placeholder="Player name" value="<?php echo isset($username) ? $username : '' ?>">
        <input type="password" name="password" placeholder="Password" value="<?php echo isset($passwordInput) ? $passwordInput : '' ?>">
        <div class="loginErrorMessage"></div>

        <span style="color: <?php echo $tekstfarve ?>"><?php echo $fejltekst ?> </span>
        <input class="loginForm__buttonLogin" type="submit" name="knap" value="login" >
    </form>



    <?php 
        //connection to database closed
        $conn->close();
    ?>


















    <!-- <script src="index.js"></script> -->
</body>

</html>