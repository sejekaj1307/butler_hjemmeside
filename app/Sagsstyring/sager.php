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
    <title>Sager</title>
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
                <li><a href="../Sagsstyring/sager.php" class="active_site_dropdown">Sager liste</a></li>
                <li><a href="../Sagsstyring/arkiverede-sager.php">Arkiverede sager</a>
                </li>
            </ul>
        </div>


<!-- -----------------------------
            Sager
------------------------------ -->
    <form action="sager.php" method="post">
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
                    //create, køres hvis "Tilføj ny medarbejder" bliver requested
                    if($_REQUEST['knap'] == "Opret ny sag")
                    {
                        $display_create_case_pop_up = "flex";
                    }
                    //create, køres hvis "create button" bliver requested
                    if($_REQUEST['knap'] == "Opret")
                    {
                        $case_nr = $_REQUEST['case_nr_c'];
                        $case_responsible = $_REQUEST['case_responsible_c'];
                        $status = 'Oprettet';
                        $location = $_REQUEST['location_c'];
                        $est_start_date = $_REQUEST['est_start_date_c'];
                        $est_end_date = $_REQUEST['est_end_date_c'];
                        $date_now = new DateTime();
                        $date_now_formatted = $date_now->format('Y-m-d H:i:s');
                        
                        $sql = $conn->prepare("insert into cases (case_nr, case_responsible, status, location, est_start_date, est_end_date, created_at) values (?, ?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("sssssss", $case_nr, $case_responsible, $status, $location, $est_start_date, $est_end_date, $date_now_formatted);
                        $sql->execute();
                        
                    }
                    //read, koden køres hvis "read button" bliver requested 
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
                        $id = $_REQUEST['id_u'];
                        $case_nr = $_REQUEST['case_nr_u'];
                        $case_responsible = $_REQUEST['case_responsible_u'];
                        $status = $_REQUEST['status_u'];
                        $location = $_REQUEST['location_u'];

                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
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
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $_SESSION["bilTilDelete"] = $id;
                                $display_delete_case_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["bilTilDelete"];
                        $sql = $conn->prepare("delete from cases where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_case_pop_up = "none";
                        
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
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $_SESSION["bilTilDelete"] = $id;
                                $display_archive_case_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm archive
                    if($_REQUEST['knap'] == "Arkiver")
                    {
                        $date_now = new DateTime();
                        $date_now_formatted = $date_now->format('Y-m-d H:i:s');
                        $id = $_SESSION["bilTilDelete"];
                        $sql = $conn->prepare("update cases set archived_at = ? where id = ?");
                        $sql->bind_param("si", $date_now_formatted, $id);
                        $sql->execute();
                        $display_archive_case_pop_up = "none";
                        
                    }
                }
            ?>


        <div class="case_list_page">
            <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret ny sag"></div>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                $sql = "select * from cases where archived_at = ''";
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
                            echo '<div class="case_data_row" style="border-left: 5px solid' . $statusColor . '">';
                                echo '<div class="case_information"> ';
                                    echo '<p class="case_nr">' . $row["case_nr"] . '</p>';
                                    echo '<p class="dark_dropdown_table case_responsible">' . $row["case_responsible"] . '</p>';
                                    echo '<p class="light_dropdown_table case_status">' . $row["status"] . '</p>';
                                echo '</div>';
                                echo '<div class="case_dropdown_mobile">';
                                    echo '<p class="dark_dropdown_table case_location">' . $row["location"] . '</p>';
                                    echo '<p class="light_dropdown_table case_est_start">' . date_format(new DateTime($row["est_start_date"]), 'd-m-y') . '</p>';
                                    echo '<p class="dark_dropdown_table case_deadline">' . date_format(new DateTime($row["est_end_date"]), 'd-m-y') . '</p>';
                                echo '</div>';
                                
                                echo '<div class="button_container">';
                                    echo '<button type="submit" name="knap" value="read_' . $row['id'] . '"><img src="../img/person-login.png" alt="Employee icon" class="edit_icons"<button>';
                                    echo '<button type="submit" name="knap" value="arc_' . $row['id'] . '"><img src="../img/person-login.png" alt="Employee icon" class="edit_icons"<button>';
                                    echo '<button type="submit" name="knap" value="delete_' . $row['id'] . '"><img src="../img/person-login.png" alt="Employee icon" class="edit_icons"<button>';
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

        <!---------------------------
            Add new case pop-up
        ---------------------------->
        <div class="pop_up_modal" style="display: <?php echo $display_create_case_pop_up ?>">
            <h3>Opret ny sag</h3>
            <div class="pop-up-row"><p>Sagssnr. : </p><input type="text" name="case_nr_c" value="<?php echo isset($case_nr) ? $case_nr : '' ?>"></div>
            <div class="pop-up-row"><p>Ansvarlig : </p><input type="text" name="case_responsible_c" value="<?php echo isset($case_responsible) ? $case_responsible : '' ?>"></div>
            <div class="pop-up-row"><p>Status : </p><input type="text" name="status_c" value="<?php echo isset($status) ? $status : '' ?>"></div>
            <div class="pop-up-row"><p>Lokation : </p><input type="text" name="location_c" value="<?php echo isset($location) ? $location : '' ?>"></div>
            <div class="pop-up-row"><p>Startdato : </p><input type="date" name="est_start_date_c" value="<?php echo isset($est_start_date) ? $est_start_date : '' ?>"></div>
            <div class="pop-up-row"><p>Deadline : </p><input type="date" name="est_end_date_c" value="<?php echo isset($est_end_date) ? $est_end_date : '' ?>"></div>
            <div class="pop-up-btn-container">
                <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                <input type="submit" name="knap" value="Opret" class="pop_up_confirm">
            </div>
        </div>

        <!----------------------------
                Edit profile pop-op
        ----------------------------->
        <div class="pop_up_modal" style="display: <?php echo $display_edit_case_pop_up ?>">
            <h3>Opdater sag</h3>
            id : <input type="text" name="id_u" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="pop-up-row"><p>Sagssnr. : </p><input type="text" name="case_nr_u" value="<?php echo isset($case_nr) ? $case_nr : '' ?>"></div>
            <div class="pop-up-row"><p>Ansvarlig : </p><input type="text" name="case_responsible_u" value="<?php echo isset($case_responsible) ? $case_responsible : '' ?>"></div>
            <div class="pop-up-row"><p>Status : </p><input type="text" name="status_u" value="<?php echo isset($status) ? $status : '' ?>"></div>
            <div class="pop-up-row"><p>Lokation : </p><input type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div>
            <div class="pop-up-row"><p>Startdato : </p><input type="text" name="est_start_date_u" value="<?php echo isset($est_start_date) ? $est_start_date : '' ?>"></div>
            <div class="pop-up-row"><p>Deadline : </p><input type="text" name="est_end_date_u" value="<?php echo isset($est_end_date) ? $est_end_date : '' ?>"></div>
            <div class="pop-up-btn-container">
                <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
            </div>
        </div>


        <!------------------------
                archive pop up
        ------------------------->
        <div class="pop_up_modal" style="display: <?php echo $display_archive_case_pop_up ?>">
            <h3>Arkiver sag</h3>
            <div class="pop-up-btn-container">
                <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                <input type="submit" name="knap" value="Arkiver" class="pop_up_confirm">
            </div>
        </div>
        <!------------------------
                delete pop up
        ------------------------->
        <div class="pop_up_modal" style="display: <?php echo $display_delete_case_pop_up ?>">
            <h3>Slet sag</h3>
            <div class="pop-up-btn-container">
                <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
            </div>
        </div>
        
    </form>



    </div>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>