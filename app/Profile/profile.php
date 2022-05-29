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
        $phone = $row['phone'];
        $phone_private = $row['phone_private'];
        $email = $row['email'];
        $email_private = $row['email_private'];
        $emergency_name = $row['emergency_name'];
        $emergency_phone = $row['emergency_phone'];
        $colour = $row['colour'];
        $picture_path = $row['picture_path'];
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
<!-- ----------------------
Masthead er i formen grundet at session variablen måske bliver opdateret
------------------------ -->
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <?php
                function findes($id, $c)
                {
                    $sql = $c->prepare("select * from employees where id = ?");
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
                        if(($_REQUEST['knap'] == "Rediger profil"))
                        {
                            $id = $_SESSION['logged_in_user_global']['id'];
                            if(is_numeric($id) && is_numeric(0 + $id))
                            {
                                $sql = $conn->prepare( "select * from employees where id = ?");
                                $sql->bind_param("i", $id); 
                                $sql->execute();
                                $result = $sql->get_result();
                                if($result->num_rows > 0) 
                                {
                                    $row = $result->fetch_assoc();
                                    $id = $row['id'];
                                    $_SESSION["selected_employee"] = $id;
                                    $first_name = $row['first_name'];
                                    $last_name = $row['last_name'];
                                    $initials = $row['initials'];
                                    $phone = $row['phone'];
                                    $phone_private = $row['phone_private'];
                                    $email = $row['email'];
                                    $email_private = $row['email_private'];
                                    $emergency_name = $row['emergency_name'];
                                    $emergency_phone = $row['emergency_phone'];
                                    
                                    $display_edit_profile_pop_up = "flex";
                                }
                            }
                                    $display_edit_profile_pop_up = "flex";
                        }
                        //update
                        if($_REQUEST['knap'] == "Opdater") 
                        {
                            $id = $_SESSION["selected_employee"];
                            $first_name = $_REQUEST['first_name_u'];
                            $last_name = $_REQUEST['last_name_u'];
                            $initials = $_REQUEST['initials_u'];
                            $phone = $_REQUEST['phone_u'];
                            $phone_private = $_REQUEST['phone_private_u'];
                            $email = $_REQUEST['email_u'];
                            $email_private = $_REQUEST['email_private_u'];
                            $emergency_name = $_REQUEST['emergency_name_u'];
                            $emergency_phone = $_REQUEST['emergency_phone_u'];
                            $colour = $_REQUEST['html5colorpicker']; 

                            $hasImage = false;
                            if(!empty($_FILES["image"]["name"])){
                                $fileName = basename($_FILES["image"]["name"]);
                                $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
                                $target_file = "profile_img/" . $fileName;

                                $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
                                if(in_array($fileType, $allowTypes)){
                                    $file_tmp_name = $_FILES['image']['tmp_name'];
                                    
                                    $hasImage = true;
                                }
                                if (move_uploaded_file($file_tmp_name, $target_file))  {
                                    $msg = "Image uploaded successfully";
                                    $fileName = "profile_img/" . $fileName;
                
                                    $picture_path = $target_file;
                                }else{
                                    $msg = "Failed to upload image";
                                }
                            }
                           

                            if(is_numeric($id) && is_integer(0 + $id))
                            {
                                if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                                {  
                                    if($hasImage){
                                        $sql = $conn->prepare("update employees set first_name = ?, last_name = ?, initials = ?, phone = ?, phone_private = ?, email = ?, email_private = ?, emergency_name = ?, emergency_phone = ?, colour = ?, picture_path = ? where id = ?");
                                        $sql->bind_param("sssssssssssi", $first_name, $last_name, $initials, $phone, $phone_private, $email, $email_private, $emergency_name, $emergency_phone, $colour, $fileName, $id);
                                        $sql->execute();
                                    }    
                                    else {
                                        $sql = $conn->prepare("update employees set first_name = ?, last_name = ?, initials = ?, phone = ?, phone_private = ?, email = ?, email_private = ?, emergency_name = ?, emergency_phone = ?, colour = ? where id = ?");
                                        $sql->bind_param("ssssssssssi", $first_name, $last_name, $initials, $phone, $phone_private, $email, $email_private, $emergency_name, $emergency_phone, $colour, $id);
                                        $sql->execute();
                                    }

                                    //In case the user changes one of the following informations, the session variable needs to be updated as well.
                                    $_SESSION['logged_in_user_global']['first_name'] = $first_name;
                                    $_SESSION['logged_in_user_global']['last_name'] = $last_name;
                                    $_SESSION['logged_in_user_global']['initials'] = $initials;
                                    $_SESSION['logged_in_user_global']['email'] = $email;

                                    $display_edit_profile_pop_up = "none";
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

            <div class="profile_page">

                <div class="pic_and_info_container"> <!-- container til billede, farve og personlige oplysninger-->
                    <div class="profile_pic_container">
                        <!-- <img class="profile_pic" src="profile_img/tester.jpg" alt="Profil billede"> -->
                        <img class="profile_pic" src="<?php echo $picture_path;?>" alt="Profil billede"> 
                        <div class="profile_color" style="background-color: <?php echo $colour?>">Din farve</div>
                    </div>
                    <div class="profile_info">
                        <div>
                            <h1><?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name']; ?></h1>
                            <p><b>Navn : </b><?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name']; ?></p>
                            <p><b>Telefon nr. : </b><?php echo $phone ?></p>
                            <p><b>Privat telefon nr. : </b><?php echo $phone_private ?></p>
                            <p><b>Email : </b><?php echo $email; ?></p>
                            <p><b>Privat email : </b><?php echo $email_private; ?></p>
                            <p><b>Kontaktperson : </b><?php echo $emergency_name; ?></p>
                            <p><b>Kontaktperson nr. : </b><?php echo $emergency_phone; ?></p>
                        </div>
                        <div class="input_container"><input type="submit" name="knap" value="Rediger profil"></div>
                    </div>
                </div>

                <div class="profile_calender">
                    Kalender kommer senere
                </div>
                <div class="profile_weeklies">
                    Ugeseddler kommer senere
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
                    <h3>Opdater medarbejder</h3>
                    <div class="pop-up-row"><p>Fornavn : </p><input type="text" name="first_name_u" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Efternavn : </p><input type="text" name="last_name_u" value="<?php echo isset($last_name) ? $last_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Initialer : </p><input type="text" name="initials_u" value="<?php echo isset($initials) ? $initials : '' ?>"></div>
                    <div class="pop-up-row"><p>Arbejds tlf. : </p><input type="text" name="phone_u" value="<?php echo isset($phone) ? $phone : '' ?>"></div>
                    <div class="pop-up-row"><p>Mobil : </p><input type="text" name="phone_private_u" value="<?php echo isset($phone_private) ? $phone_private : '' ?>"></div>
                    <div class="pop-up-row"><p>Email : </p><input type="text" name="email_u" value="<?php echo isset($email) ? $email : '' ?>"></div>
                    <div class="pop-up-row"><p>Privat email : </p><input type="text" name="email_private_u" value="<?php echo isset($email_private) ? $email_private : '' ?>"></div>
                    <div class="pop-up-row"><p>Kontaktperson : </p><input type="text" name="emergency_name_u" value="<?php echo isset($emergency_name) ? $emergency_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Kontaktperson tlf. : </p><input type="text" name="emergency_phone_u" value="<?php echo isset($emergency_phone) ? $emergency_phone : '' ?>"></div>
                    <div class="pop-up-row"><p>Medarbejder farve : </p><input type="color" name="html5colorpicker" onchange="clickColor(0, -1, -1, 5)" value="<?php echo $colour?>"></div>
                    <div class="pop-up-row"><p>Upload billede : </p><input type="file" name="image" value="Upload"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                    </div>
                </div>
            </div>

      
    </form>



    </div>
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>