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
    <title>Arkiverede notifications</title>
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
                <li><a href="../Profile/profile.php">Profil</a></li>
                <li><a href="../Profile/notifications.php" class="active_site_dropdown">Notifikationer</a>
                <li><a href="../Profile/video_guides.php">Hjælpevideoer</a>
                </li>
            </ul>
        </div>


<!-- -----------------------------
            Sager
------------------------------ -->
    <form action="archived_notifications.php" method="post">
        <?php
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
            function findes($id, $c)
            {
                $sql = $c->prepare("select * from notifications where id = ?");
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
            $display_activate_notification_pop_up = "none";


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
                        $_SESSION["selected_notification"] = $id;
                        $sql = $conn->prepare("select text from notifications where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $result = $sql->get_result();
                        if($result->num_rows > 0) 
                        {
                            $row = $result->fetch_assoc();
                            $_SESSION["selected_notification_text"] = $row['text'];
                        }

                        $display_activate_notification_pop_up = "flex";                        
                    }
                    //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $id = "";
                    }

                    //Archive
                    if($_REQUEST['knap'] == "Arktiver")
                    {
                        $id = $_SESSION["selected_notification"];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $id = $_SESSION["selected_notification"];
                                $sql = $conn->prepare("update notifications set archived_at = '' where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $display_activate_notification_pop_up = "none";
                            }
                        }
                    }
                }
            ?>


        <div class="notification_list_page">
            <div class="button_container">
                <button><a href="notifications.php">Notifikationer</a></button>
                <button class="notification_navbar_active"><a href="archived_notifications.php">Arkiverede</a></button>
            </div>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                $sql = "select * from notifications where archived_at != ''";
                $result = $conn->query($sql);

                echo '<div class="notification_list">';
                   echo '<div class="notification_text_header">Dato for notifikation</div>'; //header - hvilken dato?

                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        $list_order_id = 1;
                        while($row = $result->fetch_assoc())
                        {

                            echo '<div class="notification_row" ';
                                echo '<div class="notification_information"> ';
                                    echo '<p class="notification_text">' . $row["text"] . '</p>'; //teksten på notifikationen
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
                activate pop up
        ------------------------->
        <div class="pop_up_modal_container"  style="display: <?php echo $display_activate_notification_pop_up ?>">
            <div class="pop_up_modal">
                <h3>Gør notifikationen igen?</h3>
                <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_notification_text"];?>"</i></p>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Anuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Arktiver" class="pop_up_confirm">
                </div>
            </div>
        </div>
        
    </form>



    </div>
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>