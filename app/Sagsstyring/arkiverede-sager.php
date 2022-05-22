<?php 
    //session start
    session_start(); 
    //Forbindelse til database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Arkiverede sager</title>
</head>

<body>
    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
        <ul class="navbar_ul">
            <li><a href="../Profil/profil.php">Profil</a></li>
            <li><a href="../Medarbejdere/ansatte.php">Medarbejder</a></li>
            <li><a href="../Kalender/kalender-maskiner.php">Kalender</a></li>
            <li><a href="../Sagsstyring/sager.php" class="active-main-site">Sagsstyring</a></li>
            <li><a href="../Tidsregistrering/tidsregistrering.php">Tidsregistrering</a></li>
            <li><a href="../Opgaver/fejl-og-mangler.php">Opgaver</a></li>
            <li><a href="../Lagerstyring/lade.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../index.php">Log ud</a></div>
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
                <li><a href="../Sagsstyring/sager.php">Sager liste</a></li>
                <li><a href="../Sagsstyring/arkiverede-sager.php" class="active_site_dropdown">Arkiverede sager</a>
                </li>
            </ul>
        </div>


<!-- -----------------------------
            Sager
------------------------------ -->
    <form action="arkiverede-sager.php" method="post">
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
                    echo '<div class="case_list_header">';
                        echo '<div class="case_mobile_headers">';
                            echo '<p class="case_nr_header">Sagsnr.</p>';
                            echo '<p class="case_responsible_header">Ansvarlig</p>';
                            echo '<p class="case_status_header">Status</p>';
                        echo '</div>';
                        echo '<div class="case_all_headers">';
                            echo '<p class="case_location_header">Sagsoversigt</p>';
                            echo '<p class="case_est_start_header">Opstart</p>';
                            echo '<p class="case_deadline_header">Deadline</p>';
                            echo '<p class="button_container_header">Rediger</p>';
                        echo '</div>';
                    echo '</div>';

                    $statusColor = '#345643';
                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        while($row = $result->fetch_assoc())
                        {
                            //statuscolor
                            if($row['status'] == "Oprettet") {
                                $status_color = "#FFA2A2";
                            } else if ($row['status'] == "Beskrevet") {
                                $status_color = "#FFFC9E";
                            }
                            else if ($row['status'] == "Aktiv") {
                                $status_color = "#BBFFB9";
                            } else {
                                $status_color = "#DBB8FF";
                            }
                            echo '<div class="case_data_row" style="border-left: 5px solid' . $status_color . '">';
                                echo '<div class="case_information"> ';
                                    echo '<p class="case_nr">' . $row["case_nr"] . '</p>';
                                    echo '<p class="dark_dropdown_table case_responsible">' . $row["case_responsible"] . '</p>';
                                    echo '<p class="light_dropdown_table case_status">' . $row["status"] . '</p>';
                                echo '</div>';
                                echo '<div class="case_dropdown_mobile">';
                                    echo '<p class="dark_dropdown_table case_location">' . $row["location"] . '</p>';
                                    echo '<p class="case_est_start">' . date_format(new DateTime($row["est_start_date"]), 'd-m-y') . '</p>';
                                    echo '<p class="case_deadline">' . date_format(new DateTime($row["est_end_date"]), 'd-m-y') . '</p>';
                                echo '</div>';
                                
                                echo '<div class="button_container">';
                                    echo '<button type="submit" name="knap" value="activate_' . $row['id'] . '"><img src="../img/person-login.png" alt="Employee icon" class="edit_icons"<button>';
                                echo '</div>';
                            echo '</div>'; 
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
        <div class="pop_up_modal" style="display: <?php echo $display_activate_case_pop_up ?>">
            <h3>Gør sagen aktiv igen</h3>
            <div class="pop-up-btn-container">
                <?php echo $id;?>
                <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                <input type="submit" name="knap" value="Arkiver" class="pop_up_confirm">
            </div>
        </div>
        
    </form>



    </div>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>