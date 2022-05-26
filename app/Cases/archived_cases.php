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
    <title>Arkiverede cases</title>
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
                <li><a href="../Cases/cases.php">Sager liste</a></li>
                <li><a href="../Cases/archived_cases.php" class="active_site_dropdown">Arkiverede sager</a>
                </li>
            </ul>
        </div>


<!-- -----------------------------
            Sager
------------------------------ -->
    <form action="archived_cases.php" method="post">
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

                        $display_activate_case_pop_up = "flex";                        
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
                    }

                    //Archive
                    if($_REQUEST['knap'] == "Arkiver")
                    {
                        $id = $_SESSION["selected_case"];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $id = $_SESSION["selected_case"];
                                $sql = $conn->prepare("update cases set archived_at = '' where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $display_activate_case_pop_up = "none";
                            }
                        }
                    }
                }
            ?>


        <div class="case_list_page">
            <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret ny sag"></div>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                $sql = "select * from cases where archived_at != ''";
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
                            echo '<p class="case_location_header">Sagsoversigt</p>';
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
                            }
                            else if ($row['status'] == "Aktiv") {
                                $status_color = "#FFD391";
                            } else {
                                $status_color = "#BBFFB9";
                            }
                            echo '<div class="case_data_row" onclick="open_close_lists_mobile('. $list_order_id .', '. "'case_dropdown_mobile'" .') " style="border-left: 5px solid' . $status_color . '">';
                                echo '<div class="case_information">';
                                    echo '<p class="case_nr">' . $row["case_nr"] . '</p>';
                                    echo '<p class="case_responsible">' . $row["case_responsible"] . '</p>';
                                echo '</div>';
                                echo '<div class="case_dropdown_mobile">';
                                    echo '<p class="case_status">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["status"] . '</p>';
                                    echo '<p class="case_location">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["location"] . '</p>';
                                    echo '<p class="case_est_start">' . '<span class="dropdown_inline_headers">Seneste </span>'  . date_format(new DateTime($row["est_start_date"]), 'd-m-y') . '</p>';
                                    echo '<p class="case_deadline">' . '<span class="dropdown_inline_headers">Seneste </span>'  . date_format(new DateTime($row["est_end_date"]), 'd-m-y') . '</p>';
                                echo '</div>';
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
        //Man skal huske at slukke for forbindelsen. Det er ikke så vigtigt i små programmer, men vi gør det for en god ordens skyld
            $conn->close();
        ?>
        <!------------------------
                archive pop up
        ------------------------->
        <div class="pop_up_modal_container"  style="display: <?php echo $display_activate_case_pop_up ?>">
            <div class="pop_up_modal">
                <h3>Gør sagen aktiv igen?</h3>
                <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_case_nr"];?>"</i></p>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Arkiver" class="pop_up_confirm">
                </div>
            </div>
        </div>
        
    </form>



    </div>
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>