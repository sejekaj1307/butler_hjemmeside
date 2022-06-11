<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    //If the user is trying go around the log in process, redirect the user back to the index.php 
    if($_SESSION['logged_in_user_global']['last_name'] == ""){
        echo "<script> window.location.href = '../index.php'; </script>";
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
            <li><a href="../Profile/profile.php">Profil</a></li>
            <li><a href="../Employees/employees.php">Medarbejder</a></li>
            <li><a href="../Calender/machines_calender.php">Kalender</a></li>
            <li><a href="../Cases/cases.php" class="active-main-site">Sager</a></li>
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
                <li><a href="../Cases/cases.php" class="active_site_dropdown">Sager liste</a></li>
                <li><a href="../Cases/archived_cases.php">Arkiverede sager</a>
                </li>
            </ul>
        </div>


    <!-- -----------------------------
                Sager
    ------------------------------ -->
    <form action="cases.php" method="post">
        <?php
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
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
            $display_edit_case_pop_up = "none";
            $display_delete_case_pop_up = "none";
            $display_archive_case_pop_up = "none";
            $display_create_case_pop_up = "none";


        ?>
            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //create pop up
                    if($_REQUEST['knap'] == "Opret ny sag")
                    {
                        $display_create_case_pop_up = "flex";
                    }
                    //create
                    if($_REQUEST['knap'] == "Opret")
                    {
                        $case_nr = $_REQUEST['case_nr_c'];
                        $case_responsible = $_REQUEST['case_responsible_c'];
                        $status = 'Oprettet';
                        $location = $_REQUEST['location_c'];
                        $est_start_date = $_REQUEST['est_start_date_c'];
                        $est_end_date = $_REQUEST['est_end_date_c'];
                        $date_now = new DateTime();
                        $date_now_formatted = $date_now->format('Y-m-d');

                        $employees_initials_list = array();
                        
                        $sql = "select initials from employees";
                        $result = $conn->query($sql);
                        if($result->num_rows > 0)
                        {
                            while($row = $result->fetch_assoc())
                            {
                                if (!array_key_exists($row['initials'], $employees_initials_list)){
                                    $employees_initials_list[$row['initials']] = false;
                                    $employee_leader_initials_list[$row['initials']] = false;
                                }
                            }
                        }

                        $machine_name_list = array();
                        $sql = "select name from machines";
                        $result = $conn->query($sql);
                        if($result->num_rows > 0)
                        {
                            while($row = $result->fetch_assoc())
                            {
                                if (!array_key_exists($row['name'], $machine_name_list)){
                                    $machine_name_list[$row['name']] = false;
                                }
                            }
                        }
                        $default_job_types_json = array();
                        for($i=0; $i<count($case_job_types_list); $i++)
                        {
                            if (!array_key_exists($case_job_types_list[$i], $default_job_types_json)){
                                $default_case_job_types_json[$case_job_types_list[$i]] = false;
                            }
                        }
                        $employees_initials_list = json_encode($employees_initials_list);
                        $employee_leader_initials_list = json_encode($employee_leader_initials_list);
                        $machine_name_list = json_encode($machine_name_list);
                        $default_case_job_types_json = json_encode($default_case_job_types_json);
                        
                        $client = "";
                        $client_case_nr = "";
                        $zip_code = "";
                        $comment_road_info = "";
                        $comment_extra_work = "";
                        $archived_initials = "";
                        $archived_at = "";

                        $sql = $conn->prepare("insert into cases (case_nr, client, client_case_nr, location, zip_code, case_responsible, est_start_date, est_end_date, status, job_type, machines, employees, employee_leader, comment_road_info, comment_extra_work, archived_initials, archived_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("sssssssssssssssss", $case_nr, $client, $client_case_nr, $location, $zip_code, $case_responsible, $est_start_date, $est_end_date, $status, $default_case_job_types_json, $machine_name_list, $employees_initials_list, $employee_leader_initials_list, $comment_road_info, $comment_extra_work, $archived_initials, $archived_at);
                        $sql->execute();
                        
                    }
                    //read
                    if(str_contains($_REQUEST['knap'] , "read"))
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
                                $case_nr = $row['case_nr'];
                                $case_responsible = $row['case_responsible'];
                                $status = $row['status'];
                                $location = $row['location'];

                                $display_edit_case_pop_up = "flex";
                            }
                        }
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater") 
                    {
                        $id = $_SESSION["selected_task"];
                        $case_nr = $_REQUEST['case_nr_u'];
                        $case_responsible = $_REQUEST['case_responsible_u'];
                        $status = $_REQUEST['status_u'];
                        $location = $_REQUEST['location_u'];

                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //updatea all of the chosen objects elements to database
                            {
                                $sql = $conn->prepare("update cases set case_nr = ?, case_responsible = ?, status = ?, location = ? where id = ?");
                                $sql->bind_param("ssssi", $case_nr, $case_responsible, $status, $location, $id);
                                $sql->execute();    
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
                                $_SESSION["selected_case"] = $id;
                                $sql = $conn->prepare("select case_nr from cases where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $result = $sql->get_result();
                                if($result->num_rows > 0) 
                                {
                                    $row = $result->fetch_assoc();
                                    $_SESSION["selected_case_nr"] = $row['case_nr'];
                                }
                                $display_delete_case_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["selected_case"];
                        $sql = $conn->prepare("delete from cases where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_case_pop_up = "none";
                        
                    }
                    //cancel 
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $id = "";
                        $case_nr = "";
                        $case_responsible = "";
                        $status = "";
                        $location = "";
                        $est_start_date = "";
                        $est_end_date = "";
                        $display_delete_case_pop_up = "none";
                        $display_create_case_pop_up = "none";
                        $display_edit_case_pop_up = "none";
                    }

                    //Archive
                    if(str_contains($_REQUEST['knap'] , "arc"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn))
                            {
                                $_SESSION["selected_case"] = $id;
                                $sql = $conn->prepare("select case_nr from cases where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $result = $sql->get_result();
                                if($result->num_rows > 0) 
                                {
                                    $row = $result->fetch_assoc();
                                    $_SESSION["selected_case_nr"] = $row['case_nr'];
                                }
                                $display_archive_case_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm archive
                    if($_REQUEST['knap'] == "Arkiver")
                    {
                        $date_now = new DateTime();
                        $date_now_formatted = $date_now->format('Y-m-d H:i:s');
                        $id = $_SESSION["selected_case"];
                        $sql = $conn->prepare("update cases set archived_at = ? where id = ?");
                        $sql->bind_param("si", $date_now_formatted, $id);
                        $sql->execute();
                        $display_archive_case_pop_up = "none";
                        
                    }
                }
            ?>

        <!-- ------------------
                Cases TABLE
        ------------------- -->
        <div class="case_list_page">
            <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret ny sag"></div>
            <?php 
                //SQl query to aquire all data from cases where archived_at field in db is empty
                //list headers
                $sql = "select * from cases where archived_at = ''";
                $result = $conn->query($sql);
                echo '<div class="case_list">';
                    echo '<div class="list_color_guide_container">';
                        echo '<div class="list_color_guide_element"><div class="color red"></div><p class="color_description">Oprettet af leder</p></div>';
                        echo '<div class="list_color_guide_element"><div class="color orange"></div><p class="color_description">Beskrevet yderligere</p></div>';
                        echo '<div class="list_color_guide_element"><div class="color yellow"></div><p class="color_description">Aktiv og arbejdes på</p></div>';
                        echo '<div class="list_color_guide_element"><div class="color green"></div><p class="color_description">Fuldført og afventer godkendelse</p></div>';
                    echo '</div>';
                    echo '<div class="case_list_header">';
                        echo '<div class="case_mobile_headers">';
                            echo '<p class="case_nr_header">Sagsnr.</p>';
                            echo '<p class="case_responsible_header">Ansvarlig</p>';
                        echo '</div>';
                        echo '<div class="case_all_headers">';
                            echo '<p class="case_status_header">Status</p>';
                            echo '<p class="case_location_header">Adresse</p>';
                            echo '<p class="case_est_start_header">Opstart</p>';
                            echo '<p class="case_deadline_header">Deadline</p>';
                            echo '<p class="button_container_header">Rediger</p>';
                        echo '</div>';
                    echo '</div>';

                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        $list_order_id = 1;
                        while($row = $result->fetch_assoc())
                        {
                            //statuscolor
                            if($row['status'] == "Oprettet") {
                                $status_color = "#FFA2A2";
                            } else if ($row['status'] == "Beskrevet") {
                                $status_color = "#FFFC9E";
                            } else if ($row['status'] == "Aktiv") {
                                $status_color = "#FFD391";
                            } else {
                                $status_color = "#BBFFB9";
                            }
                            //list content
                            echo '<div class="case_data_row" onclick="open_close_lists_mobile('. $list_order_id .', '. "'case_dropdown_mobile'" .') " style="border-left: 5px solid' . $status_color . '")>';
                                echo '<div class="case_information"> ';
                                    echo '<p class="case_nr">' . $row["case_nr"] . '</p>';
                                    echo '<p class="case_responsible">' . $row["case_responsible"] . '</p>';
                                echo '</div>';
                                echo '<div class="case_dropdown_mobile">';
                                    echo '<p class="case_status">' . '<span class="dropdown_inline_headers">Status </span>' . $row["status"] . '</p>';
                                    echo '<p class="case_location">' . '<span class="dropdown_inline_headers">Adresse </span>' . $row["location"] . '</p>';
                                    echo '<p class="case_est_start">' . '<span class="dropdown_inline_headers">Forventet start </span>' . date_format(new DateTime($row["est_start_date"]), 'd-m-y') . '</p>';
                                    echo '<p class="case_deadline">' . '<span class="dropdown_inline_headers">Forventet deadline </span>' . date_format(new DateTime($row["est_end_date"]), 'd-m-y') . '</p>';
                                echo '</div>';
                                //buttons to show pop up modals
                                echo '<div class="button_container">';
                                        echo '<a class="describe_case_link" href="describe_case.php?case_nr=' . $row['case_nr'] . '"><img src="../img/edit.png" alt="Employee icon" class="edit_icons"></a>';
                                        echo '<button type="submit" name="knap" value="arc_' . $row['id'] . '"><img src="../img/archive.png" alt="Employee icon" class="edit_icons"><button>';
                                        echo '<button type="submit" name="knap" value="delete_' . $row['id'] . '"><img src="../img/trash.png" alt="Employee icon" class="edit_icons"><button>';
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

        <!---------------------------
            Add new case pop-up
        ---------------------------->
        <div class="pop_up_modal_container" style="display: <?php echo $display_create_case_pop_up ?>">
            <div class="pop_up_modal">
                <h3>Opret ny sag</h3>
                <div class="pop-up-row"><p>Sagssnr. : </p><input autocomplete="off" type="text" name="case_nr_c" value="<?php echo isset($case_nr) ? $case_nr : '' ?>"></div>
                <div class="pop-up-row">
                    <p>Ansvarlig : </p>
                    <select name="case_responsible_c">
                        <?php
                            foreach($case_responsible_initials_list as $case_responsible_initials){
                                echo "<option " . ($case_responsible == $case_responsible_initials ? 'selected' : '') . "value=" . $case_responsible_initials . ">" . $case_responsible_initials . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="pop-up-row"><p>Adresse : </p><input autocomplete="off" type="text" name="location_c" value="<?php echo isset($location) ? $location : '' ?>"></div>
                <div class="pop-up-row"><p>Startdato : </p><input autocomplete="off" type="date" name="est_start_date_c" value="<?php echo isset($est_start_date) ? $est_start_date : '' ?>"></div>
                <div class="pop-up-row"><p>Deadline : </p><input autocomplete="off" type="date" name="est_end_date_c" value="<?php echo isset($est_end_date) ? $est_end_date : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opret" class="pop_up_confirm">
                </div>
            </div>
        </div>

        <!----------------------------
                Edit profile pop-op
        ----------------------------->
        <div class="pop_up_modal_container" style="display: <?php echo $display_edit_case_pop_up ?>">
            <div class="pop_up_modal">
                <h3>Opdater sag</h3>
                <div class="pop-up-row"><p>Sagssnr. : </p><input autocomplete="off" type="text" name="case_nr_u" value="<?php echo isset($case_nr) ? $case_nr : '' ?>"></div>
                <div class="pop-up-row">
                    <p>Ansvarlig : </p>
                    <select name="case_responsible_u">
                        <?php
                            foreach($case_responsible_initials_list as $case_responsible_initials){
                                echo "<option " . ($case_responsible == $case_responsible_initials ? 'selected' : '') . " value=" . $case_responsible_initials . ">" . $case_responsible_initials . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="pop-up-row">
                        <p>Status : </p>
                        <select name="status_u">
                            <option <?php echo $status == "Oprettet" ? 'selected' : '' ?> value="Oprettet">Oprettet</option>
                            <option <?php echo $status == "Beskrevet" ? 'selected' : '' ?> value="Beskrevet">Beskrevet</option>
                            <option <?php echo $status == "Aktiv" ? 'selected' : '' ?> value="Aktiv">Aktiv</option>
                            <option <?php echo $status == "Fuldført" ? 'selected' : '' ?> value="Fuldført">Fuldført</option>
                        </select>
                    </div>
                <div class="pop-up-row"><p>Adresse : </p><input autocomplete="off" type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div>
                <div class="pop-up-row"><p>Startdato : </p><input autocomplete="off" type="date" name="est_start_date_u" value="<?php echo isset($est_start_date) ? $est_start_date : '' ?>"></div>
                <div class="pop-up-row"><p>Deadline : </p><input autocomplete="off" type="date" name="est_end_date_u" value="<?php echo isset($est_end_date) ? $est_end_date : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                </div>
            </div>
        </div>

        <!------------------------
                delete pop up
        ------------------------->
        <div class="pop_up_modal_container" style="display: <?php echo $display_delete_case_pop_up ?>">
            <div class="pop_up_modal">
                <h3>Slet sag</h3>
                <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_case_nr"];?>"</i></p>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                </div>
            </div>
        </div>
        <!------------------------
                archive pop up
        ------------------------->
        <div class="pop_up_modal_container" style="display: <?php echo $display_archive_case_pop_up ?>">
            <div class="pop_up_modal">
                <h3>Arkiver sag</h3>
                <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_case_nr"];?>"</i></p>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Arkiver" class="pop_up_confirm">
                </div>
            </div>
        </div>
        
    </form>



    </div>

    <!-- Javascript import -->
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>