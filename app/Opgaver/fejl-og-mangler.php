<?php
    //session start
    session_start();  
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Fejl og mangler</title>
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
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name'];?></div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Fejl og mangler <div class="arrow_container"><img
                        src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Opgaver/fejl-og-mangler.php" class="active_site_dropdown">Fejl og mangler</a></li>
                <li><a href="../Opgaver/planlagt-service.php">Planlagt service</a></li>
                <li><a href="../Opgaver/arkiverede-opgaver.php">Arkiverede opgaver</a>
                </li>
            </ul>
        </div>






        <!-- -----------------------------
                    Sager
        ------------------------------ -->
        <form action="fejl-og-mangler.php" method="post">
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
                //variables to show or hide pop-up-modals
                $display_edit_task_pop_up = "none";
                $display_delete_task_pop_up = "none";
                $display_create_task_pop_up = "none";
                $display_tasks_service_case_pop_up = "none";


                //har vi en post? har serveren en request?
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //create
                    if($_REQUEST['knap'] == "Tilføj ny opgave")
                    {
                        $display_create_task_pop_up = "flex";
                    }
                    //create, køres hvis "create button" bliver requested
                    if($_REQUEST['knap'] == "Opret ny")
                    {
                        $task_title = $_REQUEST['task_title_c'];
                        $priority = $_REQUEST['priority_c'];
                        $status = $_REQUEST['status_c'];
                        $deadline = $_REQUEST['deadline_c'];
                        $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                        $comment = $_REQUEST['comment_c'];
                        
                        $sql = $conn->prepare("insert into tasks (task_title, priority, status, deadline, updated_initials, comment) values (?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("ssssss", $task_title, $priority, $status, $deadline, $updated_initials, $comment);
                        $sql->execute();
                        $display_create_task_pop_up = "none";
                        
                    }
                    //read
                    if(str_contains($_REQUEST['knap'] , "read"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_numeric(0 + $id))
                        {
                            $sql = $conn->prepare( "select * from tasks where id = ?");
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
                                $deadline = $row['deadline'];
                                $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                                $comment = $row['comment'];
                                $display_edit_task_pop_up = "flex";
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
                        $deadline = $_REQUEST['deadline_u'];
                        $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                        $comment = $_REQUEST['comment_u'];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                            {
                                $sql = $conn->prepare("update tasks set task_title = ?, priority = ?, status = ?, deadline = ?, updated_initials = ?, comment = ? where id = ?");
                                $sql->bind_param("ssssssi", $task_title, $priority, $status, $deadline, $updated_initials, $comment, $id);
                                $sql->execute();
                                $display_edit_task_pop_up = "none";
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
                                $_SESSION["selected_task"] = $id;
                                $display_delete_task_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        //jeg gør brug af $_SESSION variablen for at sikre at hvis der sker ændringer i inputfeltet at det indtastede id forbliver det samme hvis siden genindlæses.
                        $id = $_SESSION["selected_task"];
                        $sql = $conn->prepare("delete from tasks where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_task_pop_up = "none";
                    }
                    //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $id = "";
                        $task_title = "";
                        $priority = "";
                        $status = "";
                        $deadline = "";
                        $updated_initials = "";
                        $comment = "";

                        $display_edit_task_pop_up = "none";
                        $display_delete_task_pop_up = "none";
                        $display_create_task_pop_up = "none";
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
                                $_SESSION["selected_task"] = $id;
                                $display_tasks_service_case_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm archive
                    if($_REQUEST['knap'] == "Arkiver")
                    {
                        $date_now = new DateTime();
                        $date_now_formatted = $date_now->format('Y-m-d H:i:s');
                        $id = $_SESSION["selected_task"];
                        $sql = $conn->prepare("update tasks set archived_at = ? where id = ?");
                        $sql->bind_param("si", $date_now_formatted, $id);
                        $sql->execute();
                        $display_tasks_service_case_pop_up = "none";
                        
                    }
                }
            ?>


            <div class="task_list_page">
                <div class="add_new_link"><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj ny opgave"></div>
                <?php 
                    //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                    $sql = "select * from tasks where archived_at = ''";
                    $result = $conn->query($sql);
                    echo '<div class="task_list">';
                        echo '<div class="task_list_header">';
                            echo '<div class="task_mobile_headers">';
                                echo '<p class="task_name_header">Opgave</p>';
                            echo '</div>';
                            echo '<div class="task_all_headers">';
                                echo '<p class="task_prority_header">Prioritet</p>';
                                echo '<p class="task_status_header">Status</p>';
                                echo '<p class="task_deadline_header">Deadline</p>';
                                echo '<p class="task_updated_initials_header">Seneste</p>';
                                echo '<p class="task_comment_header">Bemærkning</p>';
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
                                if($row['status'] == "Ikke startet") {
                                    $status_color = "#FFA2A2";
                                } else if ($row['status'] == "Startet") {
                                    $status_color = "#FFFC9E";
                                }
                                else if ($row['status'] == "Venter") {
                                    $status_color = "#BBFFB9";
                                } else {
                                    $status_color = "#DBB8FF";
                                }
                                echo '<div class="task_data_row" onclick="open_close_lists_mobile('. $list_order_id .', '. "'task_dropdown_mobile'" .') " style="border-left: 5px solid ' . $status_color . '">';
                                    echo '<div class="task_information"> ';
                                        echo '<p class="task_name">' . $row["task_title"] . '</p>';
                                    echo '</div>';
                                    echo '<div class="task_dropdown_mobile">';
                                        echo '<p class="task_prority">' . $row["priority"] . '</p>';
                                        echo '<p class="task_status">' . $row["status"] . '</p>';
                                        echo '<p class="task_deadline">' . $row["deadline"] . '</p>';
                                        echo '<p class="task_updated_initials">' . $row["updated_initials"] . '</p>';
                                        echo '<p class="task_comment">' . $row["comment"] . '</p>';
                                    echo '</div>';

                                    echo '<div class="button_container">';
                                        echo '<button type="submit" name="knap" value="read_' . $row['id'] . '"><img src="../img/edit.png" alt="Employee icon" class="edit_icons"<button>';
                                        echo '<button type="submit" name="knap" value="arc_' . $row['id'] . '"><img src="../img/archive.png" alt="Employee icon" class="edit_icons"<button>';
                                        echo '<button type="submit" name="knap" value="delete_' . $row['id'] . '"><img src="../img/trash.png" alt="Employee icon" class="edit_icons"<button>';
                                    echo '</div>';
                                echo '</div>'; 
                                $list_order_id += 1;
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

            <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_create_task_pop_up ?>">
                <h3>Tilføj ny opgave</h3>
                <div class="pop-up-row"><p>Opgave : </p><input type="text" name="task_title_c" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
                <div class="pop-up-row"><p>Prioritet : </p><input type="text" name="priority_c" value="<?php echo isset($priority) ? $priority : '' ?>"></div>
                <div class="pop-up-row"><p>Status : </p><input type="text" name="status_c" value="<?php echo isset($status) ? $status : '' ?>"></div>
                <div class="pop-up-row"><p>Deadline : </p><input type="text" name="deadline_c" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                <div class="pop-up-row"><p>Bemærkning : </p><input type="text" name="comment_c" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                </div>
            </div>

            
            <!----------------------------
                    Edit profile pop-op
            ----------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_edit_task_pop_up ?>">
                <h3>Opdater opgave</h3>
                <div class="pop-up-row"><p>Opgave : </p><input type="text" name="task_title_u" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
                <div class="pop-up-row"><p>Prioritet : </p><input type="text" name="priority_u" value="<?php echo isset($priority) ? $priority : '' ?>"></div>
                <div class="pop-up-row">
                    <p>Status : </p>
                    <select name="status_u">
                        <option <?php echo $status == "Ikke startet" ? 'selected' : '' ?> value="Ikke startet">Ikke startet</option>
                        <option <?php echo $status == "Startet" ? 'selected' : '' ?> value="Startet">Startet</option>
                        <option <?php echo $status == "Venter" ? 'selected' : '' ?> value="Venter">Venter</option>
                        <option <?php echo $status == "Fuldført" ? 'selected' : '' ?> value="Fuldført">Fuldført</option>
                    </select>
                </div>
                <div class="pop-up-row"><p>Deadline : </p><input type="text" name="deadline_u" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                <div class="pop-up-row"><p>Bemærkning : </p><input type="text" name="comment_u" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                </div>
            </div>

            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_delete_task_pop_up ?>">
                <h3>Slet opgave</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                </div>
            </div>
            <!------------------------
                    archive pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_tasks_service_case_pop_up ?>">
                <h3>Arkiver opgave</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Arkiver" class="pop_up_confirm">
                </div>
            </div>

        </form>






    </div>

    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>
</html>