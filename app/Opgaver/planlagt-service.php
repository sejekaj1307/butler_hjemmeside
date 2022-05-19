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
    <title>Planlagt service</title>
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
            <li><a href="../Sagsstyring/sager.php">Sagsstyring</a></li>
            <li><a href="../Tidsregistrering/tidsregistrering.php">Tidsregistrering</a></li>
            <li><a href="../Opgaver/fejl-og-mangler.php" class="active-main-site">Opgaver</a></li>
            <li><a href="../Lagerstyring/lade.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../index.php">Log ud</a></div>
    </div>

    <div class="site_container">
        <!-- Masthead -->
        <div class="sec-navbar-mobile">
            <div class="logged_in">
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> Efternavn, Fornavn</div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Planlagt service<div class="arrow_container"><img
                        src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Opgaver/fejl-og-mangler.php">Fejl og mangler</a></li>
                <li><a href="../Opgaver/planlagt-service.php" class="active_site_dropdown">Planlagt service</a></li>
                <li><a href="../Opgaver/arkiverede-opgaver.php">Arkiverede opgaver</a>
                </li>
            </ul>
        </div>





        <!-- FORM emploeyee list with CRUD PHP and pop-up modals  -->
        <form action="planlagt-service.php" method="post">
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


            ?>

            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    
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
                                $first_name = $row['first_name'];
                                $initials = $row['initials'];
                                $phone = $row['phone'];
                                $phone_private = $row['phone_private'];
                                $email = $row['email'];
                                $emergency_name = $row['emergency_name'];

                                $display_none_archive_pop_up = "flex";
                            }
                        }
                    }
                    // Skal man kunne slette dem?
                    //delete
                    if(str_contains($_REQUEST['knap'] , "slet"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $_SESSION["bilTilDelete"] = $id;
                                $display_delete_tasks_service_pop_up = "flex";
                            }
                        }
                    }
                    // Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["bilTilDelete"];
                        $sql = $conn->prepare("delete from tasks_service where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_tasks_service_pop_up = "none";
                        
                    }
                    //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $id = "";
                        $first_name = "";
                        $initials = "";
                        $phone = "";
                        $phone_private = "";
                        $email = "";
                        $emergency_name = "";
                        $display_none_archive_pop_up = "none";
                    }
                }
            ?>

            <!-- SELVE TABELLEN -->
            <div class="profile_list">
                <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj ny medarbejder"></div>
                <?php 


                    //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                    $sql = "select * from tasks_service";
                    $result = $conn->query($sql);
                    echo '<div class="tasks_service_harmonica">';
                        echo '<div class="tasks_service_headers">';
                            echo '<div class="tasks_service_mobile_headers">';
                                echo '<p class="tasks_service_name_header">Opgave</p>';
                            echo '</div>';
                            echo '<div class="tasks_service_all_headers">';
                                echo '<p class="tasks_service_priority_header">Prioritet</p>';
                                echo '<p class="tasks_service_status_header">Status</p>';
                                echo '<p class="tasks_service_last_service_header">Sidste service</p>';
                                echo '<p class="tasks_service_deadline_header">Deadline</p>';
                                echo '<p class="tasks_service_updated_initials_header">Seneste</p>';
                                echo '<p class="tasks_service_comment_header">Bemærkning</p>';
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
                                    echo '<div class="tasks_service_data_row" >';
                                        echo '<div class="tasks_service_information" onclick="open_close_tasks_service('. array_search($row["task_header"], $seen_task_headers) .', '. "'tasks_service_data_row_all_info'" .') " >  ';
                                            echo '<p class="tasks_service_task_header">' . $row['task_header'] . '</p>';
                                        echo '</div>';
                                    echo '</div>';         
                                }
                                echo '<div class="tasks_service_data_row_all_info" id="'. array_search($row["task_header"], $seen_task_headers) .'">';
                                    echo '<div class="data_row_info">';
                                        echo '<p class="tasks_service_title">' .  $row["task_title"] . '</p>';
                                        echo '<p class="tasks_service_priority">' . '<span class="dropdown_inline_headers">Prioritet </span>' . $row["priority"] . '</p>';
                                        echo '<p class="tasks_service_status">' . '<span class="dropdown_inline_headers">Status </span>' . $row["status"] . '</p>';
                                        echo '<p class="tasks_service_last_service">' . '<span class="dropdown_inline_headers">Sidste service </span>' . $row["last_service"] . '</p>';
                                        echo '<p class="tasks_service_deadline">' . '<span class="dropdown_inline_headers">Deadline </span>' . $row["deadline"] . '</p>';
                                        echo '<p class="tasks_service_updated_initials">' . '<span class="dropdown_inline_headers">Seneste </span>' . $row["updated_initials"] . '</p>';
                                        echo '<p class="tasks_service_comment">' . '<span class="dropdown_inline_headers">Bemærkning </span>' . $row["comment"] . '</p>';
                                    echo "</div>";
                                
                                    echo '<div class="button_container">';
                                        echo '<input type="submit" name="knap" value="read_' . $row['id'] . '">';
                                        echo '<input type="submit" name="knap" value="slet_' . $row['id'] . '">';
                                    echo '</div>';
                                echo '</div>'; 
                            }
                                  
                        }
                    echo '</div>';
                            ?>
                            
            </div>
                                


            <!-- KNAPPERNE OG INPUT FELTERNE TIL AT ÆNDRE OG READ -->
            <?php 
            //Jeg lukker forbindelsen til databasen, af sikkerhedsmæssige årsager
                $conn->close();
            ?>
            <!----------------------------
                    Edit profile pop-op
            ----------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_none_archive_pop_up ?>">
                <h3>Opdater medarbejderprofil</h3>
                id : <input type="text" name="id_u" value="<?php echo isset($id) ? $id : '' ?>">

                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                </div>
            </div>

            <!------------------------
                    delete pop up
            ------------------------->
            <!-- <div class="pop_up_modal" style="display: <?php echo $display_delete_tasks_service_pop_up ?>">
                <h3>Slet medarbejder</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                </div>
            </div> -->
            
        </form>
    </div>

    <script src="opgaver-harmonika.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>
</html>