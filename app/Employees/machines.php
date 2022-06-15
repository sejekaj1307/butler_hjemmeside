<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");
//    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

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
    <title>Maskiner</title>
</head>

<body>

    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
        <ul class="navbar_ul">
            <li><a href="../Profile/profile.php">Profil</a></li>
            <li><a href="../Employees/employees.php" class="active-main-site">Medarbejder</a></li>
            <li><a href="../Calender/machines_calender.php">Kalender</a></li>
            <li><a href="../Cases/cases.php">Sager</a></li>
            <li><a href="../Time_registration/internal_case.php">Tidsregistrering</a></li>
            <li><a href="../Tasks/tasks.php">Opgaver</a></li>
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
            <h2 class="sec-navbar-mobile-header">Maskine liste <div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Employees/employees.php">Ansatte</a></li>
                <li><a href="../Employees/externals.php">Eksterne</a></li>
                <li><a href="../Employees/suppliers.php">Leverandører</a></li>
                <li><a href="../Employees/machines.php" class="active_site_dropdown">Maskiner</a>
                </li>
            </ul>
        </div>



        <!-- -----------------------------
                    Machines CRUD
        ------------------------------ -->
        <form action="machines.php" method="post">
            <?php 
                //function to validate id, it returns a true $result if there's $rows in database
                function findes($id, $c)
                {
                    $sql = $c->prepare("select * from machines where id = ?");
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
                $display_edit_machine_pop_up = "none";
                $display_delete_machine_pop_up = "none";
                $display_create_machine_pop_up = "none";


            // CRUD, create, read, update, delete - og confirm og cancel knap til delete
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                //create pop up
                if($_REQUEST['knap'] == "Opret ny maskine")
                {
                    $display_create_machine_pop_up = "flex";
                }
                //create
                if($_REQUEST['knap'] == "Opret ny")
                {
                    $name = $_REQUEST['name_c'];
                    $name_nordic = $_REQUEST['name_nordic_c'];
                    $link = $_REQUEST['link_c'];
    
                    $sql = $conn->prepare("insert into machines (name, name_nordic, link) values (?, ?, ?)");
                    $sql->bind_param("sss", $name, $name_nordic, $link);
                    $sql->execute();
                    $display_create_machine_pop_up = "none";
                }
                //read 
                if(str_contains($_REQUEST['knap'] , "read"))
                {
                    $split = explode("_", $_REQUEST['knap']);
                    $id = $split[1];
                    if(is_numeric($id) && is_numeric(0 + $id))
                    {
                        $sql = $conn->prepare( "select * from machines where id = ?");
                        $sql->bind_param("i", $id); 
                        $sql->execute();
                        $result = $sql->get_result();
                        if($result->num_rows > 0) 
                        {
                            $row = $result->fetch_assoc();
                            $id = $row['id'];
                            $_SESSION["selected_machine"] = $id;
                            $name = $row['name'];
                            $name_nordic = $row['name_nordic'];
                            $link = $row['link'];
                            $display_edit_machine_pop_up = "flex";
                        }
                    } 
                }
                //update
                if($_REQUEST['knap'] == "Opdater") 
                {
                    $id = $_SESSION["selected_machine"];
                    $name = $_REQUEST['name_u'];
                    $name_nordic = $_REQUEST['name_nordic_u'];
                    $link = $_REQUEST['link_u'];
                    if(is_numeric($id) && is_integer(0 + $id))
                    {
                        if(findes($id, $conn)) //updatea all of the chosen objects elements to database
                        {
                            $sql = $conn->prepare("update machines set name = ?, name_nordic = ?, link = ? where id = ?");
                            $sql->bind_param("sssi", $name, $name_nordic, $link, $id);
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
                        if(findes($id, $conn)) 
                        {
                            $_SESSION["selected_machine"] = $id;
                            $sql = $conn->prepare("select name from machines where id = ?");
                            $sql->bind_param("i", $id);
                            $sql->execute();
                            $result = $sql->get_result();
                            if($result->num_rows > 0) 
                            {
                                $row = $result->fetch_assoc();
                                $_SESSION["selected_machine_name"] = $row['name'];
                            }

                            $display_delete_machine_pop_up = "flex";
                        }
                    }
                }
                //Execute - confirm delete
                if($_REQUEST['knap'] == "Slet")
                {
                    $id = $_SESSION["selected_machine"];
                    $sql = $conn->prepare("delete from machines where id = ?");
                    $sql->bind_param("i", $id);
                    $sql->execute();
                    $display_delete_machine_pop_up = "none";
                }
                //cancel 
                if($_REQUEST['knap'] == "Annuller")
                {
                    $id = "";
                    $name = "";
                    $name_nordic = "";
                    $link = "";
                    $email = "";
                    $product = "";

                    $display_edit_machine_pop_up = "none";
                    $display_delete_machine_pop_up = "none";
                    $display_create_machine_pop_up = "none";
                }
            }
        ?>



        <!-- ------------------------
                machines TABLE
        ------------------- -------->
        <div class="employee_list_page">
            <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret ny maskine"></div>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
               $sql = "select * from machines";
                $result = $conn->query($sql);

                echo '<div class="machine_list">';
                    echo '<div class="machine_list_header">';
                    echo '<p class="machine_name_header">Navn</p>';
                        echo '<div class="machines_all_headers">';
                            echo '<p class="machine_nordic_name_header">Nordisk navn</p>';
                            echo '<p class="machine_link_header">Link til BB hjemmeside</p>';
                            echo '<p class="button_container_header">Rediger</p>';
                        echo '</div>';
                    echo '</div>';

                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        $list_order_id = 1;
                        while($row = $result->fetch_assoc())
                        {
                            echo '<div class="machine_data_row">';
                                echo '<div class="machine_information" onclick="open_close_lists_mobile('. $list_order_id .', '. "'machine_dropdown_mobile'" .')">';
                                    echo '<p class="machine_name">' . $row["name"] . '</p>';
                                echo '</div>';
                                echo '<div class="machine_dropdown_mobile" id="'. $row["id"] .'">';
                                    echo '<p class="machine_nordic_name">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["name_nordic"] . '</p>';
                                    echo '<p class="machine_link">' . '<span class="dropdown_inline_headers">Seneste </span>'  . "<a target='_blank' href=" . $row['link'] . ">Link til BB hjemmeside</a>" . '</p>';
                                echo '</div>';
                                //buttons to show pop up modals
                                echo '<div class="button_container">';
                                    echo '<button type="submit" name="knap" value="read_' . $row['id'] . '"><img src="../img/edit.png" alt="Employee icon" class="edit_icons"<button>';
                                    echo '<button type="submit" name="knap" value="delete_' . $row['id'] . '"><img src="../img/trash.png" alt="Employee icon" class="edit_icons"<button>';
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
            <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_create_machine_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Opret ny maskine</h3>
                    <div class="pop-up-row"><p>Navn : </p><input type="text" name="name_c" maxlength="50" value="<?php echo isset($name) ? $name : '' ?>"></div>
                    <div class="pop-up-row"><p>Nordisk navn : </p><input type="text" name="name_nordic_c" maxlength="50" value="<?php echo isset($name_nordic) ? $name_nordic : '' ?>"></div>
                    <div class="pop-up-row"><p>Link : </p><input type="text" name="link_c" maxlength="500" value="<?php echo isset($link) ? $link : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                    </div>
                </div>
            </div>

            <!----------------------------
                    Edit profile pop-op
            ----------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_edit_machine_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Rediger maskine</h3>
                    <div class="pop-up-row"><p>Navn : </p><input type="text" name="name_u" maxlength="50" value="<?php echo isset($name) ? $name : '' ?>"></div>
                    <div class="pop-up-row"><p>Nordisk navn : </p><input type="text" name="name_nordic_u" maxlength="50" value="<?php echo isset($name_nordic) ? $name_nordic : '' ?>"></div>
                    <div class="pop-up-row"><p>Link : </p><input type="text" name="link_u" maxlength="500" value="<?php echo isset($link) ? $link : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel">
                        <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                    </div>
                </div>
            </div>
            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_delete_machine_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Slet maskine</h3>
                    <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_machine_name"];?>"</i></p>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
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