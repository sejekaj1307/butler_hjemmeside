<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    $cert_status = ""; //This variable has to be defined for the html to work correctly. It is for "Create new" priority drop-down menu.

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
            <li><a href="../Time_registration/internal_case.php">Tidsregistrering</a></li>
            <li><a href="../Tasks/tasks.php">Opgaver</a></li>
            <li><a href="../Storage/storage.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../Data/log_out.php">Log ud</a></div>
    </div>
    <!-- Masthead is placed inside the form tag, because it might get updated when user updates their profile -->

    <!---------------------------
            Profile CRUD
    ---------------------------->
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
            $display_create_cert_pop_up = "none";
            $display_update_cert_pop_up = "none";
            $display_delete_cert_pop_up = "none";
            if(empty($_SESSION['display_change_password_pop_up'])){
                $_SESSION['display_change_password_pop_up'] = "none";
            }
            
            if(empty($_SESSION['error_text'])){
                $_SESSION['error_text'] = "";
            }


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
                    if($_REQUEST['knap'] == "Opdater profil") 
                    {
                        $id = $_SESSION['logged_in_user_global']['id'];
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
                        if (!empty($_FILES["image"]["name"])){
                            $fileName = basename($_FILES["image"]["name"]);
                            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
                            $target_file = "profile_img/" . $fileName;

                            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
                            if (in_array($fileType, $allowTypes)){
                                $file_tmp_name = $_FILES['image']['tmp_name'];
                                
                                $hasImage = true;
                            } if (move_uploaded_file($file_tmp_name, $target_file))  {
                                $msg = "Image uploaded successfully";
                                $fileName = "profile_img/" . $fileName;
            
                                $picture_path = $target_file;
                            } else {
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


                    /*-------------------------------
                            password CRUD
                    -------------------------------*/
                    //read, koden køres hvis "read button" bliver requested 
                    if(($_REQUEST['knap'] == "Skift password"))
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
                                $password_new = $row['password'];
                                $_SESSION['error_text'] = "";
                                
                                $_SESSION['display_change_password_pop_up'] = "flex";
                            }
                        }
                    }
                    //update
                    if($_REQUEST['knap'] == "Bekræft password") 
                    {
                        $id = $_SESSION['logged_in_user_global']['id'];
                        $password_new = $_REQUEST['password_new'];
                        $password_new2 = $_REQUEST['password_new2'];


                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //updatea all of the chosen objects elements to database
                            {
                                if($password_new != $password_new2) {
                                    $_SESSION['error_text'] = "Passwords matcher ikke";
                                } 
                                else if($password_new == ""){
                                    $_SESSION['error_text'] = "Passwords er tomt";
                                }
                                else {
                                    $sql = $conn->prepare("update employees set password = ? where id = ?");
                                    $sql->bind_param("si", $password_new, $id);
                                    $sql->execute();    
                                    $_SESSION['display_change_password_pop_up'] = "none";
                                }
                            }
                        }
                    }


                    /*-------------------------------
                            CRUD certificates
                    --------------------------------*/
                    //create pop up
                    if($_REQUEST['knap'] == "Opret nyt certifikat")
                    {
                        $display_create_cert_pop_up = "flex";
                    }
                    //Create
                    if($_REQUEST['knap'] == "Opret ny")
                    {
                        $cert_nr = "";
                        $cert_name = $_REQUEST['cert_name_c'];
                        $cert_taken = $_REQUEST['cert_taken_c'];
                        $cert_deadline = $_REQUEST['cert_deadline_c'];
                        $cert_status = $_REQUEST['cert_status_c'];
                        $cert_link = $_REQUEST['cert_link_c'];
                        $cert_employee_initials = "";

                        $sql = $conn->prepare("insert into certificates (cert_nr, cert_name, cert_taken, cert_deadline, cert_status, cert_link, cert_employee_initials) values (?, ?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("sssssss", $cert_nr, $cert_name, $cert_taken, $cert_deadline, $cert_status, $cert_link, $cert_employee_initials);
                        $sql->execute();

                        $display_create_cert_pop_up = "none";
                    }
                    //read
                    if(str_contains($_REQUEST['knap'] , "cert-read"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_numeric(0 + $id))
                        {
                            $sql = $conn->prepare( "select * from certificates where id = ?");
                            $sql->bind_param("i", $id); 
                            $sql->execute();
                            $result = $sql->get_result();
                            if($result->num_rows > 0) 
                            {
                                $row = $result->fetch_assoc();
                                $id = $row['id'];
                                $_SESSION["selected_certificate"] = $id;
                                $cert_nr = $row['cert_nr'];
                                $cert_name = $row['cert_name'];
                                $cert_taken = $row['cert_taken'];
                                $cert_deadline = $row['cert_deadline'];
                                $cert_status = $row['cert_status'];
                                $cert_link = $row['cert_link'];
                                $cert_employee_initials = $row['cert_employee_initials'];

                                $display_update_cert_pop_up = "flex";
                            }
                        } 
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater certificat")
                    {
                        $id = $_SESSION["selected_certificate"];
                        $cert_nr = "";
                        $cert_name = $_REQUEST['cert_name_u'];
                        $cert_taken = $_REQUEST['cert_taken_u'];
                        $cert_deadline = $_REQUEST['cert_deadline_u'];
                        //TODO: Update cert_status correct
                        $cert_status = "";
                        $cert_link = $_REQUEST['cert_link_u'];
                        $cert_employee_initials = "";
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //updatea all of the chosen objects elements to database
                            {
                                $sql = $conn->prepare("update certificates set cert_nr = ?, cert_name = ?, cert_taken = ?, cert_deadline = ?, cert_status = ?, cert_link = ?, cert_employee_initials = ? where id = ?");
                                $sql->bind_param("sssssssi", $cert_nr, $cert_name, $cert_taken, $cert_deadline, $cert_status, $cert_link, $cert_employee_initials, $id);
                                $sql->execute();

                                $display_update_cert_pop_up = "none";
                            }
                        }
                    }
                    //delete
                    if(str_contains($_REQUEST['knap'] , "delete"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) 
                            {
                                $_SESSION["selected_certificate"] = $id;
                                $sql = $conn->prepare("select cert_name from certificates where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $result = $sql->get_result();
                                if($result->num_rows > 0) 
                                {
                                    $row = $result->fetch_assoc();
                                    $_SESSION["selected_certificate_name"] = $row['cert_name'];
                                }
                                $display_delete_cert_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["selected_certificate"];
                        $sql = $conn->prepare("delete from certificates where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_cert_pop_up = "none";
                    }

                    /*-------------------------------
                            cancel buttons
                    -------------------------------*/
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        //cancel edit certificate
                        $id = "";
                        $cert_nr = "";
                        $cert_name = "";
                        $cert_taken = "";
                        $cert_deadline = "";
                        $cert_status = "";
                        $cert_link = "";
                        $cert_employee_initials = "";

                        //cancel edit profile
                        $id = "";
                        $case_nr = "";
                        $case_responsible = "";
                        $status = "";
                        $location = "";
                        $est_start_date = "";
                        $est_end_date = "";

                        $display_edit_profile_pop_up = "none";

                        $display_edit_task_pop_up = "none";
                        $display_delete_task_pop_up = "none";
                        $display_create_task_pop_up = "none";
                        $_SESSION['display_change_password_pop_up'] = "none";
                        $_SESSION['error_text'] = "";

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

                <!-------------------------------
                    Profile page container
                -------------------------------->
                <div class="profile_page">
                    <div class="pic_and_info_container"> 
                        <div class="profile_pic_container">
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
                                <input type="submit" name="knap" value="Skift password">
                            </div>
                            <div class="input_container"><input type="submit" name="knap" value="Rediger profil"></div>
                        </div>
                    </div>
                    <!-- calender container -->
                    <div class="profile_calender">
                        Kalender kommer senere
                    </div>
                    <!-- weeklies container -->
                    <div class="profile_weeklies">
                        Ugeseddler kommer senere
                    </div>
                </div>

                <!-- -----------------------
                    Certificates TABLE
                ------------------------ -->
                <div class="certificate_list_container">
                    <div class="add_new_link"><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret nyt certifikat"></div>
                    <?php 
                        //SQl query to aquire all data from task where archived_at field in db is empty
                        //list headers
                        $sql = "select * from certificates";
                        $result = $conn->query($sql);
                        echo '<div class="certificate_list">';
                            echo '<div class="list_color_guide_container">';
                                echo '<div class="list_color_guide_element"><div class="color red"></div><p class="color_description">Udløbet</p></div>';
                                echo '<div class="list_color_guide_element"><div class="color yellow"></div><p class="color_description">Ved at udløbe</p></div>';
                                echo '<div class="list_color_guide_element"><div class="color green"></div><p class="color_description">Aktiv</p></div>';
                            echo '</div>';
                            echo '<div class="certificate_list_header">';
                                echo '<p class="certificates_name_header">Certifikat navn</p>';
                                echo '<div class="certificate_list_all_headers">';
                                    echo '<p class="certificates_status_header">Status</p>';
                                    echo '<p class="certificates_taken_header">Udstedt</p>';
                                    echo '<p class="certificates_deadline_header">Deadline</p>';
                                    echo '<p class="button_container_header">Rediger</p>';
                                echo '</div>';
                            echo '</div>';

                            //if og while her 
                            if($result->num_rows > 0)
                            {
                                $list_order_id = 1;
                                while($row = $result->fetch_assoc())
                                {
                                    $status = "";
                                    //If we are passed the deadline
                                    if(strtotime(date_format(new DateTime($row["cert_deadline"]), 'Y-m-d')) < strtotime(date('Y-m-d'))){
                                        $status = "Udløbet";
                                        $status_color = "#FFA2A2";
                                    }
                                    else if(strtotime(date_format(new DateTime($row["cert_deadline"]), 'Y-m-d')) < strtotime(date('Y-m-d', strtotime("28 days")))){
                                        $status = "Ved at udløbe";
                                        $status_color = "#FFF06B";
                                    }
                                    else {
                                        $status = "Aktiv";
                                        $status_color = "#BBFFB9";
                                    }
                                    
                                    //list content
                                    echo '<div class="certificates_data_row" onclick="open_close_lists_mobile('. $list_order_id .', '. "'task_dropdown_mobile'" .') " style="border-left: 5px solid ' . $status_color . '">';
                                        echo '<div class="certificates_information"> ';
                                            echo '<p class="certificates_name">' . $row["cert_name"] . '</p>';
                                        echo '</div>';
                                        echo '<div class="certificates_dropdown_mobile">';
                                            echo '<p class="certificates_status">' . '<span class="dropdown_inline_headers">Status </span>'  . $status . '</p>';
                                            echo '<p class="certificates_taken">' . '<span class="dropdown_inline_headers">Udstedt </span>'  . date_format(new DateTime($row["cert_taken"]), 'd-m-y') . '</p>';
                                            echo '<p class="certificates_deadline">' . '<span class="dropdown_inline_headers">Deadline </span>'  . date_format(new DateTime($row["cert_deadline"]), 'd-m-y') . '</p>';
                                        echo '</div>';
                                        //buttons to show pop up modals
                                        echo '<div class="button_container">';
                                            echo '<button type="submit" name="knap" value="cert-read_' . $row['id'] . '"><img src="../img/edit.png" alt="Employee icon" class="edit_icons"<button>';
                                            echo '<button type="submit" name="knap" value="delete_' . $row['id'] . '"><img src="../img/trash.png" alt="Employee icon" class="edit_icons"<button>';
                                        echo '</div>';
                                    echo '</div>'; 
                                    $list_order_id += 1;
                                }   
                            }
                        echo '</div>';
                    ?>
                </div>


                <?php 
                    //closing connection to database for security reasons
                    $conn->close();
                ?>
        
            <!-----------------------------------
                Create new Certificates pop-op
            ------------------------------------>
            <div class="pop_up_modal_container" style="display: <?php echo $display_create_cert_pop_up ?>">
                <div class="pop_up_modal" >
                    <h3>Opret nyt certifikat</h3>
                    <div class="pop-up-row"><p>Certifikat navn : </p><input autocomplete="off" type="text" name="cert_name_c" maxlength="50" value="<?php echo isset($cert_name) ? $cert_name : '' ?>"></div>
                    <div class="pop-up-row">
                        <p>Status : </p>
                        <select name="cert_status_c">
                            <option <?php echo $cert_status == "Udløbet" ? 'selected' : '' ?> value="Udløbet">Udløbet</option>
                            <option <?php echo $cert_status == "Aktiv" ? 'selected' : '' ?> value="Aktiv">Aktiv</option>
                            <option <?php echo $cert_status == "Ved at udløbe" ? 'selected' : '' ?> value="Ved at udløbe">Ved at udløbe</option>
                        </select>
                    </div>
                    <div class="pop-up-row"><p>Udstedt : </p><input autocomplete="off" type="date" name="cert_taken_c" value="<?php echo isset($cert_taken) ? $cert_taken : '' ?>"></div>
                    <div class="pop-up-row"><p>Deadline : </p><input autocomplete="off" type="date" name="cert_deadline_c" value="<?php echo isset($cert_deadline) ? $cert_deadline : '' ?>"></div>
                    <div class="pop-up-row"><p>Link : </p><input type="text" name="cert_link_c" maxlength="500" value="<?php echo isset($cert_link) ? $cert_link : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                    </div>
                </div>
            </div>
            <!----------------------------
                    Edit profile pop-op
            ----------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_edit_profile_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Rediger profil</h3>
                    <div class="pop-up-row"><p>Fornavn : </p><input type="text" name="first_name_u" maxlength="50" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Efternavn : </p><input type="text" name="last_name_u" maxlength="50" value="<?php echo isset($last_name) ? $last_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Initialer : </p><input type="text" name="initials_u" maxlength="50" value="<?php echo isset($initials) ? $initials : '' ?>"></div>
                    <div class="pop-up-row"><p>Arbejds tlf. : </p><input type="text" name="phone_u" maxlength="50" value="<?php echo isset($phone) ? $phone : '' ?>"></div>
                    <div class="pop-up-row"><p>Mobil : </p><input type="text" name="phone_private_u" maxlength="50" value="<?php echo isset($phone_private) ? $phone_private : '' ?>"></div>
                    <div class="pop-up-row"><p>Email : </p><input type="text" name="email_u" maxlength="100" value="<?php echo isset($email) ? $email : '' ?>"></div>
                    <div class="pop-up-row"><p>Privat email : </p><input type="text" name="email_private_u" maxlength="100" value="<?php echo isset($email_private) ? $email_private : '' ?>"></div>
                    <div class="pop-up-row"><p>Kontaktperson : </p><input type="text" name="emergency_name_u" maxlength="100" value="<?php echo isset($emergency_name) ? $emergency_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Kontaktperson tlf. : </p><input type="text" name="emergency_phone_u" maxlength="50" value="<?php echo isset($emergency_phone) ? $emergency_phone : '' ?>"></div>
                    <div class="pop-up-row"><p>Medarbejder farve : </p><input type="color" name="html5colorpicker" onchange="clickColor(0, -1, -1, 5)" value="<?php echo $colour?>"></div>
                    <div class="pop-up-row"><p>Upload billede : </p><input type="file" name="image" maxlength="500" value="Upload"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opdater profil" class="pop_up_confirm">
                    </div>
                </div>
            </div>

            <!----------------------------
                Edit password pop-op
            ----------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $_SESSION['display_change_password_pop_up'] ?>">
                <div class="pop_up_modal">
                    <h3>Skift password</h3>
                    <div class="pop-up-row"><p>Nyt password : </p><input type="password" class="password_input"  value="<?php echo isset($password_new) ? $password_new : '' ?>"><div class="input_field_S" onclick="toggle_password_type('password_input', 'toggle_password_icon')"><img id="toggle_password_icon" src="../img/toggle_password_type_icon.png" alt="eye icon"></div></div>
                    <div class="pop-up-row"><p>Bekræft password : </p><input type="password" class="password_input2" ><div class="input_field_S" onclick="toggle_password_type('password_input2', 'toggle_password_icon2')"><img id="toggle_password_icon2" src="../img/toggle_password_type_icon.png" alt="eye icon"></div></div>
                    <p><?php echo $_SESSION['error_text'] ?></p>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Bekræft password" class="pop_up_confirm">
                    </div>
                </div>
            </div>

            <!--------------------------------
                update certificates pop-op
            --------------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_update_cert_pop_up ?>">
                <div class="pop_up_modal" >
                    <h3>Opdater certifikat</h3>
                    <div class="pop-up-row"><p>Certifikat navn : </p><input autocomplete="off" type="text" name="cert_name_u" maxlength="50" value="<?php echo isset($cert_name) ? $cert_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Udstedt : </p><input autocomplete="off" type="date" name="cert_taken_u" value="<?php echo isset($cert_taken) ? $cert_taken : '' ?>"></div>
                    <div class="pop-up-row"><p>Deadline : </p><input autocomplete="off" type="date" name="cert_deadline_u" value="<?php echo isset($cert_deadline) ? $cert_deadline : '' ?>"></div>
                    <div class="pop-up-row"><p>Link : </p><input type="text" name="cert_link_u" maxlength="500" value="<?php echo isset($cert_link) ? $cert_link : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opdater certificat" class="pop_up_confirm">
                    </div>
                </div>
            </div>

            <!------------------------
                delete cert pop up
            ------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_delete_cert_pop_up?>">
                <div class="pop_up_modal">
                    <h3>Slet certifikat?</h3>
                    <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_certificate_name"];?>"</i></p>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                    </div>
                </div>
            </div>
    </form>



    </div>
    <!-- Javascript import -->
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
    <script src="password_type_toggle.js"></script>
</body>

</html>