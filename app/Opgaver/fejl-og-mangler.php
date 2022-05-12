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
        <div class="navbar_mid"></div>
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
                Efternavn, Fornavn
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
                        $id = $_REQUEST['id_c'];
                        $task_title = $_REQUEST['task_title_c'];
                        $priority = $_REQUEST['priority_c'];
                        $status = $_REQUEST['status_c'];
                        $deadline = $_REQUEST['deadline_c'];
                        $updated_initials = $_REQUEST['updated_initials_c'];
                        $comment = $_REQUEST['comment_c'];
                        if(is_numeric($id) && is_integer(0 + $id)) 
                        {
                            if(!findes($id, $conn)) //opret ny klub
                            {
                                $sql = $conn->prepare("insert into tasks (id, task_title, priority, status, deadline, updated_initials, comment) values (?, ?, ?, ?, ?, ?, ?)");
                                $sql->bind_param("issssss", $id, $task_title, $priority, $status, $deadline, $updated_initials, $comment);
                                $sql->execute();
                                $display_create_task_pop_up = "none";
                            }
                        }
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
                                $task_title = $row['task_title'];
                                $priority = $row['priority'];
                                $status = $row['status'];
                                $deadline = $row['deadline'];
                                $updated_initials = $row['updated_initials'];
                                $comment = $row['comment'];
                                $display_edit_task_pop_up = "flex";
                            }
                        } 
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater")
                    {
                        $id = $_REQUEST['id_u'];
                        $task_title = $_REQUEST['task_title_u'];
                        $priority = $_REQUEST['priority_u'];
                        $status = $_REQUEST['status_u'];
                        $deadline = $_REQUEST['deadline_u'];
                        $updated_initials = $_REQUEST['updated_initials_u'];
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
                                $_SESSION["bilTilDelete"] = $id;
                                $display_delete_task_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        //jeg gør brug af $_SESSION variablen for at sikre at hvis der sker ændringer i inputfeltet at det indtastede id forbliver det samme hvis siden genindlæses.
                        $id = $_SESSION["bilTilDelete"];
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
                }
            ?>


            <div class="task_list_page">
                <div class="add_new_link"><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj ny opgave"></div>
                <?php 
                    //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                    $sql = "select * from tasks";
                    $result = $conn->query($sql);
                    echo '<div class="task_list">';
                        echo '<div class="task_list_header">';
                            echo '<div class="task_mobile_headers">';
                                echo '<p class="task_name_header">Opgave</p>';
                            echo '</div>';
                            echo '<div class="task_all_headers">';
                                echo '<p class="task_prority_header">prioritet</p>';
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
                            while($row = $result->fetch_assoc())
                            {
                                echo '<div class="task_data_row">';
                                    echo '<div class="task_information"> ';
                                        echo '<p class="task_name">' . $row["task_title"] . '</p>';
                                    echo '</div>';
                                    echo '<div class="task_dropdown_mobile">';
                                        echo '<p class="dark_dropdown_table task_prority">' . $row["priority"] . '</p>';
                                        echo '<p class="dark_dropdown_table task_status">' . $row["status"] . '</p>';
                                        echo '<p class="dark_dropdown_table task_deadline">' . $row["deadline"] . '</p>';
                                        echo '<p class="light_dropdown_table task_updated_initials">' . $row["updated_initials"] . '</p>';
                                        echo '<p class="dark_dropdown_table task_comment">' . $row["comment"] . '</p>';
                                    echo '</div>';
                                    ?> 
                                <div class="button_container">
                                    <input type="submit" name="knap" value="read_<?php echo $row['id'];?>">
                                    <button type="submit" name="knap" value="re">Ar</button>
                                    <input type="submit" name="knap" value="delete_<?php echo $row['id'];?>">
                                </div>
                            <?php 

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
            <div class="pop_up_modal" style="display: <?php echo $display_edit_task_pop_up ?>">
                <h3>Opdater opgave</h3>
                id : <input type="text" name="id_u" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="pop-up-row"><p>Opgave : </p><input type="text" name="task_title_u" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
                <div class="pop-up-row"><p>Prioritet : </p><input type="text" name="priority_u" value="<?php echo isset($priority) ? $priority : '' ?>"></div>
                <div class="pop-up-row"><p>status : </p><input type="text" name="status_u" value="<?php echo isset($status) ? $status : '' ?>"></div>
                <div class="pop-up-row"><p>Deadline : </p><input type="text" name="deadline_u" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                <div class="pop-up-row"><p>Seneste : </p><input type="text" name="updated_initials_u" value="<?php echo isset($updated_initials) ? $updated_initials : '' ?>"></div>
                <div class="pop-up-row"><p>Kommentar : </p><input type="text" name="comment_u" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                </div>
            </div>

            <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_create_task_pop_up ?>">
                <h3>Tilføj ny opgave</h3>
                id : <input type="text" name="id_c" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="pop-up-row"><p>Opgave : </p><input type="text" name="task_title_c" value="<?php echo isset($task_title) ? $task_title : '' ?>"></div>
                <div class="pop-up-row"><p>Prioritet : </p><input type="text" name="priority_c" value="<?php echo isset($priority) ? $priority : '' ?>"></div>
                <div class="pop-up-row"><p>status : </p><input type="text" name="status_c" value="<?php echo isset($status) ? $status : '' ?>"></div>
                <div class="pop-up-row"><p>Deadline : </p><input type="text" name="deadline_c" value="<?php echo isset($deadline) ? $deadline : '' ?>"></div>
                <div class="pop-up-row"><p>Seneste : </p><input type="text" name="updated_initials_c" value="<?php echo isset($updated_initials) ? $updated_initials : '' ?>"></div>
                <div class="pop-up-row"><p>Kommentar : </p><input type="text" name="comment_c" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
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

        </form>






    </div>


    <script src="../javaScript/navbars.js"></script>
</body>
</html>