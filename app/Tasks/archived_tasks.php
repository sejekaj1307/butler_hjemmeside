<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    //If the user is trying go around the log in process, redirect the user back to the index.php 
    if($_SESSION['logged_in_user_global']['last_name'] == ""){
        echo "<script> window.location.href = '../index.php'; </script>";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Arkiverede opgaver</title>
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
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name'];?></div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Fejl og mangler <div class="arrow_container"><img
                        src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Tasks/tasks.php">Fejl og mangler</a></li>
                <li><a href="../Tasks/tasks_service.php">Planlagt service</a></li>
                <li><a href="../Tasks/archived_tasks.php" class="active_site_dropdown">Arkiverede opgaver</a>
                </li>
            </ul>
        </div>


        <!-- -----------------------------
                Archived tasks
        ------------------------------ -->
        <form action="archived_tasks.php" method="post">
            <?php
                //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
                function findes($id, $c)
                {
                    $sql = $c->prepare("select * from tasks where id = ?");
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
                $display_activate_case_pop_up = "none";


            ?>
            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //Execute - confirm archive
                    if(str_contains($_REQUEST['knap'] , "activate"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        $_SESSION["selected_task"] = $id;
                        $sql = $conn->prepare("select task_title from tasks where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $result = $sql->get_result();
                                if($result->num_rows > 0) 
                                {
                                    $row = $result->fetch_assoc();
                                    $_SESSION["selected_task_name"] = $row['task_title'];
                                }
                        $display_activate_case_pop_up = "flex";                        
                    }
                    //cancel 
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $id = "";
                        $task_title = $row['task_title'];
                        $priority = $row['priority'];
                        $status = $row['status'];
                        $deadline = $row['deadline'];
                        $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                        $comment = $row['comment'];
                        $display_edit_task_pop_up = "flex";
                    }

                    //Archive
                    if($_REQUEST['knap'] == "Aktiver")
                    {
                        $id = $_SESSION["selected_task"];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $id = $_SESSION["selected_task"];
                                $sql = $conn->prepare("update tasks set archived_at = '' where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $display_activate_case_pop_up = "none";
                            }
                        }
                    }
                }
            ?>

            <!-- ------------------
                    TABLE
            ------------------- -->
            <div class="case_list_page">
                <?php 
                    //SQl query to aquire all data from tasks where arhived_at field is filled in
                    //task headers
                    $sql = "select * from tasks where archived_at != ''";
                    $result = $conn->query($sql);

                    echo '<div class="task_list">';
                        echo '<div class="list_color_guide_container">';
                                echo '<div class="list_color_guide_element"><div class="color red"></div><p class="color_description">Ikke startet</p></div>';
                                echo '<div class="list_color_guide_element"><div class="color orange"></div><p class="color_description">Startet</p></div>';
                                echo '<div class="list_color_guide_element"><div class="color yellow"></div><p class="color_description">Venter</p></div>';
                                echo '<div class="list_color_guide_element"><div class="color green"></div><p class="color_description">Fuldført</p></div>';
                            echo '</div>';
                        echo '<div class="task_list_header">';
                            echo '<div class="task_mobile_headers">';
                                echo '<p class="task_name_header">Opgave</p>';
                            echo '</div>';
                            echo '<div class="task_all_headers">';
                                echo '<p class="task_archived_at_header">Arkiveret</p>';
                                echo '<p class="task_archived_initials_header">Arkiveret af</p>';
                                echo '<p class="task_updated_initials_header">Seneste</p>';
                                echo '<p class="task_comment_header">Bemærkninger</p>';
                                echo '<p class="button_container_header">Aktiver</p>';
                            echo '</div>';
                        echo '</div>';

                        //if og while her 
                        if($result->num_rows > 0)
                        {
                            $list_order_id = 1;
                            while($row = $result->fetch_assoc())
                            {
                                //statuscolor
                                if($row['status'] == "Ikke startet") {
                                    $status_color = "#FFA2A2";
                                } else if ($row['status'] == "Startet") {
                                    $status_color = "#FFFC9E";
                                }else if ($row['status'] == "Venter") {
                                    $status_color = "#FFD391";
                                } else {
                                    $status_color = "#BBFFB9";
                                }
                                //list content
                                echo '<div class="task_data_row" onclick="open_close_lists_mobile('. $list_order_id .', '. "'task_dropdown_mobile'" .') " style="border-left: 5px solid' . $status_color . '">';
                                    echo '<div class="task_information"> ';
                                        echo '<p class="task_name">' . $row["task_title"] . '</p>';
                                    echo '</div>';
                                    echo '<div class="task_dropdown_mobile">';
                                        echo '<p class="task_archived_at">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["archived_at"] . '</p>';
                                        echo '<p class="task_archived_initials">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["archived_initials"] . '</p>';
                                        echo '<p class="task_updated_initials">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["updated_initials"] . '</p>';
                                        echo '<p class="task_comment">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["comment"] . '</p>';
                                    echo '</div>';
                                    //buttons to show pop up modals
                                    echo '<div class="button_container">';
                                        echo '<button type="submit" name="knap" value="activate_' . $row['id'] . '"><img src="../img/activate.png" alt="Employee icon" class="edit_icons"<button>';
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
            <!------------------------
                    archive pop up
            ------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_activate_case_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Gør sagen aktiv igen?</h3>
                    <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_task_name"];?>"</i></p>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Aktiver" class="pop_up_confirm">
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