<?php 
    //session start
    session_start(); 
    //Forbindelse til database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");
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
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name'];?> </div>
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
                $display_edit_task_service_pop_up = "none";
                $display_create_task_service_pop_up = "none";
                $display_delete_harmonica_pop_up = "none";

            ?>

            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //create
                    if($_REQUEST['knap'] == "Tilføj nyt element")
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
                        
                        $sql = $conn->prepare("insert into tasks_service (task_header, task_title, priority, status, last_service, deadline, updated_initials, comment) values (?, ?, ?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("ssssssss", $task_header, $task_title, $priority, $status, $last_service, $deadline, $updated_initials, $comment);
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
                        $task_title = $_REQUEST['task_title_u'];
                        $priority = $_REQUEST['priority_u'];
                        $status = $_REQUEST['status_u'];
                        $last_service = $_REQUEST['last_service_u'];
                        $deadline = $_REQUEST['deadline_u'];
                        $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                        $comment = $_REQUEST['comment_u'];
                        if(is_numeric($id) && is_integer(0 + $id))
                        { 
                            if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                            {
                                $sql = $conn->prepare("update tasks_service set task_title = ?, priority = ?, status = ?, last_service = ?, deadline = ?, updated_initials = ?, comment = ? where id = ?");
                                $sql->bind_param("sssssssi", $task_title, $priority, $status, $last_service, $deadline, $updated_initials, $comment, $id);
                                $sql->execute();
                                $display_edit_task_service_pop_up = "none";
                            }
                        }
                    }
                    // Skal man kunne slette dem?
                    //delete
                    if(str_contains($_REQUEST['knap'] , "delete"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $_SESSION["selected_task"] = $id;
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
                <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj nyt element"></div>
                <?php 
                    //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                    $sql = "select * from tasks_service order by task_header asc";
                    $result = $conn->query($sql);
                    echo '<div class="harmonica_harmonica">';
                        echo '<div class="harmonica_headers">';
                            echo '<div class="harmonica_mobile_headers">';
                                echo '<p class="harmonica_name_header">Opgave</p>';
                            echo '</div>';
                            echo '<div class="harmonica_all_headers">';
                                echo '<p class="harmonica_priority_header">Prioritet</p>';
                                echo '<p class="harmonica_status_header">Status</p>';
                                echo '<p class="harmonica_last_service_header">Sidste service</p>';
                                echo '<p class="harmonica_deadline_header">Deadline</p>';
                                echo '<p class="harmonica_updated_initials_header">Seneste</p>';
                                echo '<p class="harmonica_comment_header">Bemærkning</p>';
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
                                        echo '<div class="harmonica_information" onclick="open_close_harmonica1('. array_search($row["task_header"], $seen_task_headers) .', '. "'harmonica_data_row_all_info'" .') " >  ';
                                            echo '<p class="harmonica_task_header">' . $row['task_header'] . '</p>';
                                        echo '</div>';
                                    echo '</div>';         
                                }
                                //statuscolor
                                if($row['status'] == "Ikke startet") {
                                    $status_color = "#FFA2A2";
                                } else if ($row['status'] == "Startet") {
                                    $status_color = "#FFFC9E";
                                }
                                else {
                                    $status_color = "#BBFFB9";
                                }
                                echo '<div class="harmonica_data_row_all_info" id="'. array_search($row["task_header"], $seen_task_headers) .'" style="border-left: 5px solid ' . $status_color . '">';
                                    echo '<div class="data_row_info">';
                                        echo '<p class="harmonica_title">' .  $row["task_title"] . '</p>';
                                        echo '<p class="harmonica_priority">' . '<span class="dropdown_inline_headers">Prioritet </span>' . $row["priority"] . '</p>';
                                        echo '<p class="harmonica_status">' . '<span class="dropdown_inline_headers">Status </span>' . $row["status"] . '</p>';
                                        echo '<p class="harmonica_last_service">' . '<span class="dropdown_inline_headers">Sidste service </span>' . date_format(new DateTime($row["last_service"]), 'd-m-y') . '</p>';
                                        echo '<p class="harmonica_deadline">' . '<span class="dropdown_inline_headers">Deadline </span>' . date_format(new DateTime($row["deadline"]), 'd-m-y') . '</p>';
                                        echo '<p class="harmonica_updated_initials">' . '<span class="dropdown_inline_headers">Seneste </span>' . $row["updated_initials"] . '</p>';
                                        echo '<p class="harmonica_comment">' . '<span class="dropdown_inline_headers">Bemærkning </span>' . $row["comment"] . '</p>';
                                    echo "</div>";
                                
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



            <!-- KNAPPERNE OG INPUT FELTERNE TIL AT ÆNDRE OG READ -->
            <?php 
            //Jeg lukker forbindelsen til databasen, af sikkerhedsmæssige årsager
                $conn->close();
            ?>

            <!-----------------------------------
                Add new task_service pop-up
            ------------------------------------>
            <div class="pop_up_modal" style="display: <?php echo $display_create_task_service_pop_up ?>">
                <h3>Tilføj ny opgave</h3>
                <div class="pop-up-row"><p>Maskine : </p><input type="text" name="task_header_c" value="<?php echo isset($task_header) ? $task_header : '' ?>"></div>
                <div class="pop-up-row"><p>Opgave : </p><input type="text" name="task_title_c" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
                <div class="pop-up-row">
                    <p>Prioritet : </p>
                    <select name="priority_c">
                        <option <?php echo $priority == "Lav" ? 'selected' : '' ?> value="Lav">Lav</option>
                        <option <?php echo $priority == "Middel" ? 'selected' : '' ?> value="Middel">Middel</option>
                        <option <?php echo $priority == "Høj" ? 'selected' : '' ?> value="Høj">Høj</option>
                    </select>
                </div> 
                <div class="pop-up-row"><p>Deadline : </p><input type="date" name="deadline_c" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                <div class="pop-up-row"><p>Bemærkning : </p><input type="text" name="comment_c" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                </div>
            </div>

            <!-------------------------------------
                    Edit task_service pop-op    
            -------------------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_edit_task_service_pop_up ?>">
                <h3>Opdater element</h3>
                <div class="pop-up-row"><p>Opgave : </p><input type="text" name="task_title_u" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
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
                        <option <?php echo $status == "Fuldført" ? 'selected' : '' ?> value="Fuldført">Fuldført</option>
                    </select>
                </div> 
                <div class="pop-up-row"><p>Seneste service : </p><input type="date" name="last_service_u" value="<?php echo isset($last_service) ? $last_service : '' ?>"></div>
                <div class="pop-up-row"><p>Deadline : </p><input type="date" name="deadline_u" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                <div class="pop-up-row"><p>Bemærkning : </p><input type="text" name="comment_u" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                </div>
            </div>
            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_delete_harmonica_pop_up ?>">
                <h3>Slet opgave</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                </div>
            </div>
            
        </form>
    </div>

    <script src="opgaver-harmonika.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>
</html>