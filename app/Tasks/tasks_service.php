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

    $priority = ""; //This variable has to be defined for the html to work correctly. It is for "Create new" priority drop-down menu.   
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Planlagt service</title>
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
            <li><a href="../Cases/cases.php">Sager</a></li>
            <li><a href="../Time_registration/time_registration.php">Tidsregistrering</a></li>
            <li><a href="../Tasks/tasks.php" class="active-main-site">Opgaver</a></li>
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
            <h2 class="sec-navbar-mobile-header">Planlagt service<div class="arrow_container"><img
                        src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Tasks/tasks.php">Fejl og mangler</a></li>
                <li><a href="../Tasks/tasks_service.php" class="active_site_dropdown">Planlagt service</a></li>
                <li><a href="../Tasks/archived_tasks.php">Arkiverede opgaver</a>
                </li>
            </ul>
        </div>

        <!-- -----------------------------
                Tasks_service CRUD
        ------------------------------ -->
        <!-- FORM emploeyee list with CRUD PHP and pop-up modals  -->
        <form action="tasks_service.php" method="post">
            <?php 
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
                function findes($id, $c)
                {
                    $sql = $c->prepare("select * from tasks_service where id = ?");
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
                $display_none_archive_pop_up = "none";
                $display_edit_task_service_pop_up = "none";
                $display_create_task_service_pop_up = "none";
                $display_delete_harmonica_pop_up = "none";
            ?>

            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //create
                    if($_REQUEST['knap'] == "Opret ny planlagt service")
                    {
                        $display_create_task_service_pop_up = "flex";
                    }
                    //create, køres hvis "create button" bliver requested
                    if($_REQUEST['knap'] == "Opret ny")
                    {
                        $task_header = $_REQUEST['task_header_c'];
                        $task_title = $_REQUEST['task_title_c'];
                        $priority = $_REQUEST['priority_c'];
                        $status = "Ikke startet";
                        $last_service = $_REQUEST['last_service_c'];
                        $deadline = $_REQUEST['deadline_c'];
                        $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                        $comment = $_REQUEST['comment_c'];
                        $updated_at = "";
                        
                        $sql = $conn->prepare("insert into tasks_service (task_header, task_title, priority, status, last_service, deadline, updated_initials, comment, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("sssssssss", $task_header, $task_title, $priority, $status, $deadline, $last_service, $updated_initials, $comment, $updated_at);
                        $sql->execute();
                        $display_create_task_service_pop_up = "none";
                        
                    }
                    //read, koden køres hvis "read button" bliver requested 
                    if(str_contains($_REQUEST['knap'] , "read"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_numeric(0 + $id))
                        {
                            $sql = $conn->prepare( "select * from tasks_service where id = ?");
                            $sql->bind_param("i", $id); 
                            $sql->execute();
                            $result = $sql->get_result();
                            if($result->num_rows > 0) 
                            {
                                $row = $result->fetch_assoc();
                                $id = $row['id'];
                                $_SESSION["selected_task"] = $id;
                                $task_header = $row['task_header'];
                                $task_title = $row['task_title'];
                                $priority = $row['priority'];
                                $status = $row['status'];
                                $last_service = $row['last_service'];
                                $deadline = $row['deadline'];
                                $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                                $comment = $row['comment'];

                                $display_edit_task_service_pop_up = "flex";
                            }
                        }
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater")
                    {
                        $id = $_SESSION["selected_task"];
                        $task_header = $_REQUEST['task_header_u'];
                        $task_title = $_REQUEST['task_title_u'];
                        $priority = $_REQUEST['priority_u'];
                        $status = $_REQUEST['status_u'];
                        $last_service = $_REQUEST['last_service_u'];
                        $deadline = $_REQUEST['deadline_u'];
                        $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                        $comment = $_REQUEST['comment_u'];
                        if(is_numeric($id) && is_integer(0 + $id))
                        { 
                            if(findes($id, $conn)) //updatea all of the chosen objects elements to database
                            {
                                $sql = $conn->prepare("update tasks_service set task_header = ?, task_title = ?, priority = ?, status = ?, last_service = ?, deadline = ?, updated_initials = ?, comment = ? where id = ?");
                                $sql->bind_param("ssssssssi", $task_header, $task_title, $priority, $status, $last_service, $deadline, $updated_initials, $comment, $id);
                                $sql->execute();
                                $display_edit_task_service_pop_up = "none";
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
                                $_SESSION["selected_task"] = $id;
                                $sql = $conn->prepare("select task_title from tasks_service where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $result = $sql->get_result();
                                if($result->num_rows > 0) 
                                {
                                    $row = $result->fetch_assoc();
                                    $_SESSION["selected_task_name"] = $row['task_title'];
                                }
                                $display_delete_harmonica_pop_up = "flex";
                            }
                        }
                    }
                    // Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["selected_task"];
                        $sql = $conn->prepare("delete from tasks_service where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_harmonica_pop_up = "none";
                        
                    }
                    //cancel 
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $id = "";
                        $task_title = "";
                        $task_header = "";
                        $priority = "";
                        $last_service = "";
                        $deadline = "";
                        $updated_initials = "";
                        $comment = "";
                        $display_none_archive_pop_up = "none";
                    }
                }
            ?>


            <!-- ------------------
                    TABLE
            ------------------- -->
            <div class="profile_list">
                <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret ny planlagt service"></div>
                <?php 
                    //SQl query to aquire all data from task_service for each task header
                    //list headers
                    $sql = "select * from tasks_service order by task_header asc";
                    $result = $conn->query($sql);
                    echo '<div class="harmonica_container">';
                        echo '<div class="list_color_guide_container">';
                            echo '<div class="list_color_guide_element"><div class="color red"></div><p class="color_description">Ikke startet</p></div>';
                            echo '<div class="list_color_guide_element"><div class="color orange"></div><p class="color_description">Startet</p></div>';
                            echo '<div class="list_color_guide_element"><div class="color yellow"></div><p class="color_description">Venter</p></div>';
                            echo '<div class="list_color_guide_element"><div class="color green"></div><p class="color_description">Fuldført</p></div>';
                        echo '</div>';
                        echo '<div class="harmonica_headers">';
                            echo '<div class="harmonica_mobile_headers">';
                                echo '<p class="harmonica_name_header">Opgave</p>';
                            echo '</div>';
                            echo '<div class="harmonica_all_headers">';
                                echo '<p class="tasks_service_harmonica_priority_header">Prioritet</p>';
                                echo '<p class="tasks_service_harmonica_status_header">Status</p>';
                                echo '<p class="tasks_service_harmonica_last_service_header">Sidste service</p>';
                                echo '<p class="tasks_service_harmonica_deadline_header">Deadline</p>';
                                echo '<p class="tasks_service_updated_initials_header">Seneste</p>';
                                echo '<p class="tasks_service_harmonica_comment_header">Bemærkning</p>';
                                echo '<p class="button_container_header">Rediger</p>';
                            echo '</div>';
                        echo '</div>';
                        //if og while her
                        $seen_task_headers=array();
                        //
                        if($result->num_rows > 0)
                        {
                            while($row = $result->fetch_assoc()) {
                                if(!in_array($row['task_header'], $seen_task_headers)){
                                    array_push($seen_task_headers, $row['task_header']); 
                                    echo '<div class="harmonica_data_row" >';
                                        echo '<div class="harmonica_information" onclick="harmonica_open_close('. array_search($row["task_header"], $seen_task_headers) .', '. "'harmonica_data_row_all_info'" .') " >  ';
                                            echo '<p class="harmonica_task_header">' . $row['task_header'] . '</p>';
                                        echo '</div>';
                                    echo '</div>';         
                                }
                                //statuscolor
                                if($row['status'] == "Ikke startet") {
                                    $status_color = "#FFA2A2";
                                } else if ($row['status'] == "Startet") {
                                    $status_color = "#FFD391";
                                }else if ($row['status'] == "Venter") {
                                    $status_color = "#FFFC9E";
                                } else {
                                    $status_color = "#BBFFB9";
                                }
                                //list content
                                echo '<div class="harmonica_data_row_all_info" id="'. array_search($row["task_header"], $seen_task_headers) .'" style="border-left: 5px solid ' . $status_color . '">';
                                    echo '<div class="data_row_info">';
                                        echo '<p class="harmonica_title">' .  $row["task_title"] . '</p>';
                                        echo '<p class="tasks_service_harmonica_priority">' . '<span class="dropdown_inline_headers">Prioritet </span>' . $row["priority"] . '</p>';
                                        echo '<p class="tasks_service_harmonica_status">' . '<span class="dropdown_inline_headers">Status </span>' . $row["status"] . '</p>';
                                        echo '<p class="tasks_service_harmonica_last_service">' . '<span class="dropdown_inline_headers">Sidste service </span>' . date_format(new DateTime($row["last_service"]), 'd-m-y') . '</p>';
                                        echo '<p class="tasks_service_harmonica_deadline">' . '<span class="dropdown_inline_headers">Deadline </span>' . date_format(new DateTime($row["deadline"]), 'd-m-y') . '</p>';
                                        echo '<p class="tasks_service_updated_initials">' . '<span class="dropdown_inline_headers">Seneste </span>' . $row["updated_initials"] . '</p>';
                                        echo '<p class="tasks_service_harmonica_comment">' . '<span class="dropdown_inline_headers">Bemærkning </span>' . $row["comment"] . '</p>';
                                    echo "</div>";
                                    //buttons to show pop up modals
                                    echo '<div class="button_container">';
                                        echo '<button type="submit" name="knap" value="read_' . $row['id'] . '"><img src="../img/edit.png" alt="Employee icon" class="edit_icons"<button>';
                                        echo '<button type="submit" name="knap" value="delete_' . $row['id'] . '"><img src="../img/trash.png" alt="Employee icon" class="edit_icons"<button>';
                                    echo '</div>';
                                echo '</div>'; 
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
                Add new task_service pop-up
            ------------------------------------>
            <div class="pop_up_modal_container" style="display: <?php echo $display_create_task_service_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Opret ny planlagt service</h3>
                    <div class="pop-up-row">
                        <p>Maskine : </p>
                        <select name="task_header_c">
                            <?php
                                foreach($tasks_machines_options as $tasks_machines_option){
                                    echo '<option ' . ($task_header == $tasks_machines_option ? 'selected' : '') . ' value="' . $tasks_machines_option . '">' . $tasks_machines_option . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="pop-up-row"><p>Opgave : </p><input autocomplete="off"  type="text" name="task_title_c" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
                    <div class="pop-up-row">
                        <p>Prioritet : </p>
                        <select name="priority_c">
                            <option <?php echo $priority == "Lav" ? 'selected' : '' ?> value="Lav">Lav</option>
                            <option <?php echo $priority == "Middel" ? 'selected' : '' ?> value="Middel">Middel</option>
                            <option <?php echo $priority == "Høj" ? 'selected' : '' ?> value="Høj">Høj</option>
                        </select>
                    </div> 
                    <div class="pop-up-row"><p>Sidste service : </p><input autocomplete="off" type="date" name="last_service_c" value="<?php echo isset($last_service) ? $last_service : '' ?>"></div>
                    <div class="pop-up-row"><p>Deadline : </p><input autocomplete="off" type="date" name="deadline_c" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                    <div class="pop-up-row"><p>Bemærkning : </p><input autocomplete="off" type="text" name="comment_c" maxlength="50" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                    </div>
                </div>
            </div>

            <!-------------------------------------
                    Edit task_service pop-op    
            -------------------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_edit_task_service_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Rediger planlagt service</h3>
                    <div class="pop-up-row">
                        <p>Maskine : </p>
                        <select name="task_header_u">
                            <?php
                                foreach($tasks_machines_options as $tasks_machines_option){
                                    echo '<option ' . ($task_header == $tasks_machines_option ? 'selected' : '') . ' value="' . $tasks_machines_option . '">' . $tasks_machines_option . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="pop-up-row"><p>Opgave : </p><input autocomplete="off" type="text" name="task_title_u" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
                    <div class="pop-up-row">
                        <p>Prioritet : </p>
                        <select name="priority_u">
                            <option <?php echo $priority == "Lav" ? 'selected' : '' ?> value="Lav">Lav</option>
                            <option <?php echo $priority == "Middel" ? 'selected' : '' ?> value="Middel">Middel</option>
                            <option <?php echo $priority == "Høj" ? 'selected' : '' ?> value="Høj">Høj</option>
                        </select>
                    </div> 
                    <div class="pop-up-row">
                        <p>Status : </p>
                        <select name="status_u">
                            <option <?php echo $status == "Ikke startet" ? 'selected' : '' ?> value="Ikke startet">Ikke startet</option>
                            <option <?php echo $status == "Startet" ? 'selected' : '' ?> value="Startet">Startet</option>
                            <option <?php echo $status == "Venter" ? 'selected' : '' ?> value="Venter">Venter</option>
                            <option <?php echo $status == "Fuldført" ? 'selected' : '' ?> value="Fuldført">Fuldført</option>
                        </select>
                    </div> 
                    <div class="pop-up-row"><p>Seneste service : </p><input autocomplete="off" type="date" name="last_service_u" value="<?php echo isset($last_service) ? $last_service : '' ?>"></div>
                    <div class="pop-up-row"><p>Deadline : </p><input autocomplete="off" type="date" name="deadline_u" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                    <div class="pop-up-row"><p>Bemærkning : </p><input autocomplete="off" type="text" name="comment_u" maxlength="50" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                    </div>
                </div>
            </div>
            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_delete_harmonica_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Slet planlagt service?</h3>
                    <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_task_name"];?>"</i></p>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                    </div>
                </div>
            </div>
            
        </form>
    </div>

    <!-- Javascript import -->
    <script src="../javaScript/harmonica_open_close.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>
</html>