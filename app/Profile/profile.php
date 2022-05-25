<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    //If the user is trying go around the log in process, redirect the user back to the index.php 
    if($_SESSION['logged_in_user_global']['last_name'] == ""){
        echo "<script> window.location.href = '../index.php'; </script>";
    }

    //Fetch user data from global user email
    $sql = $conn->prepare( "select * from employees where email = ?");
    $sql->bind_param("s", $_SESSION['logged_in_user_global']['email']); 
    $sql->execute();
    $result = $sql->get_result();
    if($result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $initials = $row['initials'];
        $email_private = $row['email_private'];
        $phone = $row['email_private'];
        $phone_private = $row['email_private'];
        $emergency_name = $row['email_private'];
        $emergency_phone = $row['email_private'];
        $picture = $row['email_private'];
        $colour = $row['email_private'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Profil</title>
</head>

<body>

    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
        <ul class="navbar_ul">
            <li><a href="../Profile/profile.php" class="active-main-site">Profil</a></li>
            <li><a href="../Employees/employees.php">Medarbejder</a></li>
            <li><a href="../Calender/machines_calender.php">Kalender</a></li>
            <li><a href="../Cases/cases.php">Sager</a></li>
            <li><a href="../Time_registration/time_registration.php">Tidsregistrering</a></li>
            <li><a href="../Tasks/tasks.php">Opgaver</a></li>
            <li><a href="../Storage/storage.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../Data/log_out.php">Log ud</a></div>
    </div>

    <div class="site_container">
        <!-- Masthead -->
        <div class="sec-navbar-mobile">
            <div class="logged_in">
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name'];?> </div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Profile <div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Profile/profile.php" class="active_site_dropdown">Profil</a>
                <li><a href="../Profile/notifications.php">Notifikationer</a>
                <li><a href="../Profile/video_guides.php">Hjælpevideoer</a></li>
                </li>
            </ul>
        </div>


    <!-------------------------------
            My profile form
    -------------------------------->    
    <form action="profile.php" method="post" class="myProfileForm"> 
        <?php
            $fejltekst = "";

            /*------------------------------------
                Request to database on submit
            -----------------------------------*/
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                if($_REQUEST['knap'] == "Update Profile")
                {
                    $sql = $conn->prepare("select * from employees where email = ?");
                    $sql->bind_param("s", $playernameGlobal); 
                    $sql->execute();
                    $result = $sql->get_result();
                    if($result->num_rows > 0) //If there's data, read the variables
                    {
                        $row = $result->fetch_assoc();
                        echo "noget data?";
                    }
                    else //If the player has reached the site, but not logged in correctly
                    {
                        $fejltekst = "Player nummer $playernameGlobal findes ikke";
                        $tekstfarve = "#ff0000";
                    } 
                }
            }        
        ?>

        
    <!--------------------------
            My Profile page
    --------------------------->
    <div class="myProfilePage">
        <div class="myProfile">
            <h3>Your profile</h3>
            <p><i>Your highscore: </p>
            <p><i>Your email:</i></p>
            <p><i>Your player name:</i></p>
            <input type="submit" value="Update Profile" name="knap" id="updateProfileBtn" class="grayButton"><!-- Isset i php tjekker om følgende har en værdi -->
            <p style="color: <?php echo $tekstfarve ?>"><?php echo $fejltekst ?> </p> 
        </div>



        
        <?php 
            $conn->close();
        ?>
    </form>























    </div>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>