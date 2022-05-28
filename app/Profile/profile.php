<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    //If the user is trying go around the log in process, redirect the user back to the index.php 
    if($_SESSION['logged_in_user_global']['last_name'] == ""){
        echo "<script> window.location.href = '../index.php'; </script>";
    }


    $sql = $conn->prepare("select * from employees where email = ?");

    $sql->bind_param("s", $_SESSION['logged_in_user_global']['email']);
    $sql->execute();
    $result = $sql->get_result();
    
    $daily_report_data = array();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
                

    include("../Data/data.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Sager</title>
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

    <!-- Masthead -->
    <div class="site_container">
        <div class="sec-navbar-mobile">
            <div class="logged_in">
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name'];?> </div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Sager liste<div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Profile/profile.php" class="active_site_dropdown">Profil</a></li>
                <li><a href="../Profile/notifications.php">Notifikationer</a>
                <li><a href="../Profile/video_guides.php">Hjælpevideoer</a>
                </li>
            </ul>
        </div>

        <?php
            function findes($id, $c)
            {
                $sql = $c->prepare("select * from cases where id = ?");
                $sql->bind_param("i", $id);
                $sql->execute();
                $result = $sql->get_result();
                if($result->num_rows > 0)
                {
                    return true;
                }
                else 
                {
                    return false;
                }
            }
            //variables to show or hide pop-up modals
            $display_edit_profile_pop_up = "none";


                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //read, koden køres hvis "read button" bliver requested 
                    if(str_contains($_REQUEST['knap'] , "Rediger profil"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_numeric(0 + $id))
                        {
                            $sql = $conn->prepare( "select * from cases where id = ?");
                            $sql->bind_param("i", $id); 
                            $sql->execute();
                            $result = $sql->get_result();
                            if($result->num_rows > 0) 
                            {
                                $row = $result->fetch_assoc();
                                $id = $row['id'];
                                $_SESSION["selected_task"] = $id;
                                $first_name = $row['first_name'];
                                $last_name = $row['last_name'];
                                $email = $row['email'];
                                $email_private = $row['email_private'];
                                $phone = $row['phone'];
                                $phone_private = $row['phone_private'];
                                $emergency_name = $row['emergency_name'];
                                $emergency_nr = $row['emergency_nr'];

                                $display_edit_profile_pop_up = "flex";
                            }
                        }
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater") 
                    {
                        $id = $_SESSION["selected_task"];
                            $first_name = $row['first_name'];
                            $last_name = $row['last_name'];
                            $email = $row['email'];
                            $email_private = $row['email_private'];
                            $phone = $row['phone'];
                            $phone_private = $row['phone_private'];
                            $emergency_name = $row['emergency_name'];
                            $emergency_nr = $row['emergency_nr'];

                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                            {
                                $sql = $conn->prepare("update cases set first_name = ?, last_name = ?, email = ?, email_private = ?, phone = ?, phone_private = ?, emergency_name = ?, emergency_nr = ? where id = ?");
                                $sql->bind_param("ssssssssi", $first_name, $last_name, $email, $email_private, $phone, $phone_private, $emergency_name, $emergency_nr, $id);
                                $sql->execute();    
                            }
                        }
                    }
                    //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $id = "";
                        $case_nr = "";
                        $case_responsible = "";
                        $status = "";
                        $location = "";
                        $est_start_date = "";
                        $est_end_date = "";

                        $display_edit_profile_pop_up = "none";
                    }
                }
            ?>


        <div class="profile_page">

            <div class="pic_and_info_container"> <!-- container til billede, farve og personlige oplysninger-->
                <div class="profile_pic_container">
                    <img class="profile_pic" src="profile_img/tester.jpg" alt="Profil billede">
                    <div class="profile_color">Din farve</div>
                </div>
                <div class="profile_info">
                    <div>
                        <h1><?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name']; ?></h1>
                        <p><b>Navn : </b><?php echo $row['last_name'] . ', ' . $row['first_name']; ?></p>
                        <p><b>Email : </b><?php echo $row['email']; ?></p>
                        <p><b>Privat email : </b><?php echo $row['email_private']; ?></p>
                        <p><b>Telefon nr. : </b><?php echo $row['phone']; ?></p>
                        <p><b>Privat telefon nr. : </b><?php echo $row['phone_private']; ?></p>
                        <p><b>Kontaktperson : </b><?php echo $row['emergency_name']; ?></p>
                        <p><b>Kontaktperson nr. : </b><?php echo $row['emergency_phone']; ?></p>
                    </div>
                    <div class="input_container"><input type="submit" name="knap" value="Rediger profil"></div>
                </div>
            </div>
            <div class="profile_calender">
                Kalender
            </div>
            <div class="profile_weeklies">
                Ugeseddler
            </div>
        </div>


        <?php 
        //Man skal huske at slukke for forbindelsen. Det er ikke så vigtigt i små programmer, men vi gør det for en god ordens skyld
            $conn->close();
        ?>

        

        <!----------------------------
                Edit profile pop-op
        ----------------------------->
        <div class="pop_up_modal_container" style="display: <?php echo $display_edit_profile_pop_up ?>">
            <div class="pop_up_modal">
                <h3>Opdater sag</h3>
                <div class="pop-up-row"><p>Privat email : </p><input autocomplete="off" type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div>
                <div class="pop-up-row"><p>Privat telefon nr. : </p><input autocomplete="off" type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div>
                <div class="pop-up-row"><p>Kontaktperson : </p><input autocomplete="off" type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div>
                <div class="pop-up-row"><p>Kontaktperson nr. : </p><input autocomplete="off" type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div>
                <div class="pop-up-row"><p>Password : </p><input autocomplete="off" type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div           </div>
        </div>

      
    </form>



    </div>
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>