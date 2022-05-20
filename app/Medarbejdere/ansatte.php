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
    <title>Ansatte</title>
</head>
    <body>

    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
        <ul class="navbar_ul">
            <li><a href="../Profil/profil.php">Profil</a></li>
            <li><a href="../Medarbejdere/ansatte.php" class="active-main-site">Medarbejder</a></li>
            <li><a href="../Kalender/kalender-maskiner.php">Kalender</a></li>
            <li><a href="../Sagsstyring/sager.php">Sagsstyring</a></li>
            <li><a href="../Tidsregistrering/tidsregistrering.php">Tidsregistrering</a></li>
            <li><a href="../Opgaver/fejl-og-mangler.php">Opgaver</a></li>
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
            <h2 class="sec-navbar-mobile-header">Ansatte liste<div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Medarbejdere/ansatte.php" class="active_site_dropdown">Ansatte</a></li>
                <li><a href="../Medarbejdere/eksterne.php">Eksterne</a></li>
                <li><a href="../Medarbejdere/leverandoere.php">Leverandører</a></li>
                <li><a href="../Medarbejdere/maskiner.php">Maskiner</a>
                </li>
            </ul>
        </div>



        <!-- FORM emploeyee list with CRUD PHP and pop-up modals  -->
        <form action="ansatte.php" method="post">
            <?php 
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
                function findes($id, $c)
                {
                    $sql = $c->prepare("select * from employees where id = ?");
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
                $display_edit_employee_pop_up = "none";
                $display_delete_employee_pop_up = "none";
                $display_create_employee_pop_up = "none";


            ?>

            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //create, køres hvis "Tilføj ny medarbejder" bliver requested
                    if($_REQUEST['knap'] == "Tilføj ny medarbejder")
                    {
                        $display_create_employee_pop_up = "flex";
                    }
                    //create, køres hvis "create button" bliver requested
                    if($_REQUEST['knap'] == "Opret ny")
                    {
                        $id = $_REQUEST['id_c'];
                        $first_name = $_REQUEST['first_name_c'];
                        $initials = $_REQUEST['initials_c'];
                        $phone = $_REQUEST['phone_c'];
                        $phone_private = $_REQUEST['phone_private_c'];
                        $email = $_REQUEST['email_c'];
                        $emergency_name = $_REQUEST['emergency_name_c'];
                        if(is_numeric($id) && is_integer(0 + $id)) 
                        {
                            if(!findes($id, $conn)) //opret ny klub
                            {
                                $sql = $conn->prepare("insert into employees (id, first_name, initials, phone, phone_private, email, emergency_name) values (?, ?, ?, ?, ?, ?, ?)");
                                $sql->bind_param("issssss", $id, $first_name, $initials, $phone, $phone_private, $email, $emergency_name);
                                $sql->execute();
                            }
                        } 
                    }
                    //read, koden køres hvis "read button" bliver requested 
                    if(str_contains($_REQUEST['knap'] , "read"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_numeric(0 + $id))
                        {
                            $sql = $conn->prepare( "select * from employees where id = ?");
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

                                $display_edit_employee_pop_up = "flex";
                            }
                        }
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater") 
                    {
                        $id = $_REQUEST['id_u'];
                        $first_name = $_REQUEST['first_name_u'];
                        $initials = $_REQUEST['initials_u'];
                        $phone = $_REQUEST['phone_u'];
                        $phone_private = $_REQUEST['phone_private_u'];
                        $email = $_REQUEST['email_u'];
                        $emergency_name = $_REQUEST['emergency_name_u'];

                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                            {
                                $sql = $conn->prepare("update employees set first_name = ?, initials = ?, phone = ?, phone_private = ?, email = ?, emergency_name = ? where id = ?");
                                $sql->bind_param("ssssssi", $first_name, $initials, $phone, $phone_private, $email, $emergency_name, $id);
                                $sql->execute();    
                            }
                        }
                    }
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
                                $display_delete_employee_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["bilTilDelete"];
                        $sql = $conn->prepare("delete from employees where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_employee_pop_up = "none";
                        
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
                        $display_delete_employee_pop_up = "none";
                        $display_create_employee_pop_up = "none";
                        $display_edit_employee_pop_up = "none";
                    }
                }
            ?>

            <!-- SELVE TABELLEN -->
            <div class="profile_list">
                <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj ny medarbejder"></div>
                <?php 


                    //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                    $sql = "select * from employees";
                    $result = $conn->query($sql);
                    echo '<div class="employee_list">';
                        echo '<div class="employee_list_header">';
                            echo '<div class="employee_mobile_headers">';
                                echo '<p class="employee_name_header">Medarbejder</p>';
                                echo '<p class="employee_initials_header">Initialer</p>';
                            echo '</div>';
                            echo '<div class="employee_all_headers">';
                                echo '<p class="employee_phone_header">Arbejds-tlf</p>';
                                echo '<p class="employee_phone_header">Mobil</p>';
                                echo '<p class="employee_email_header">Email</p>';
                                echo '<p class="employee_emergency_header">Kontaktperson</p>';
                                echo '<p class="button_container_header">Rediger</p>';
                            echo '</div>';
                        echo '</div>';

                        //if og while her 
                        if($result->num_rows > 0)
                        {
                            while($row = $result->fetch_assoc())
                            {
                                echo '<div class="employee_data_row" >';
                                    echo '<div class="mobile_employee_information" onclick="open_close_employee_info('. $row["id"] .', '. "'employee_dropdown_mobile'" .') " style="border-left: 5px solid' . $row['colour'] . '">  ';
                                        echo '<p class="employee_name">' . $row["last_name"] . ", " . $row["first_name"] . '</p>';
                                        echo '<p class="employee_initials">' . $row["initials"] . '</p>';
                                    echo '</div>';
                                    echo '<div class="employee_dropdown_mobile" id="'. $row["id"] .'">';
                                        echo '<p class="dark_dropdown_table employee_phone">' . $row["phone"] . '</p>';
                                        echo '<p class="light_dropdown_table employee_phone">' . $row["phone_private"] . '</p>';
                                        echo '<p class="dark_dropdown_table employee_email">' . $row["email"] . '</p>';
                                        echo '<p class="light_dropdown_table employee_emergency">' . $row["emergency_name"] . ", " . $row["emergency_phone"] . '</p>';
                                    echo '</div>';
                                ?>
                                <div class="button_container">
                                    <!-- <input type="submit" name="knap" value="read_<?php echo $row['id'];?>"> -->
                                    <button type="submit" name="knap" value="read_<?php echo $row['id'];?>"><img src="../img/person-login.png" alt="Employee icon" class="edit_icons"></button>
                                    <input type="submit" name="knap" value="slet_<?php echo $row['id'];?>">
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
            <div class="pop_up_modal" style="display: <?php echo $display_edit_employee_pop_up ?>">
                <h3>Opdater medarbejderprofil</h3>
                id : <input type="text" name="id_u" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="pop-up-row"><p>Navn : </p><input type="text" name="first_name_u" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                <div class="pop-up-row"><p>Initialer : </p><input type="text" name="initials_u" value="<?php echo isset($initials) ? $initials : '' ?>"></div>
                <div class="pop-up-row"><p>phone : </p><input type="text" name="phone_u" value="<?php echo isset($phone) ? $phone : '' ?>"></div>
                <div class="pop-up-row"><p>Mobil : </p><input type="text" name="phone_private_u" value="<?php echo isset($phone_private) ? $phone_private : '' ?>"></div>
                <div class="pop-up-row"><p>Email : </p><input type="text" name="email_u" value="<?php echo isset($email) ? $email : '' ?>"></div>
                <div class="pop-up-row"><p>Emergency : </p><input type="text" name="emergency_name_u" value="<?php echo isset($emergency_name) ? $emergency_name : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                </div>
            </div>

            <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_create_employee_pop_up ?>">
                <h3>Tilføj ny medarbejder</h3>
                id : <input type="text" name="id_c" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="pop-up-row"><p>Name : </p><input type="text" name="first_name_c" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                <div class="pop-up-row"><p>Initialer : </p><input type="text" name="initials_c" value="<?php echo isset($initials) ? $initials : '' ?>"></div>
                <div class="pop-up-row"><p>phone : </p><input type="text" name="phone_c" value="<?php echo isset($phone) ? $phone : '' ?>"></div>
                <div class="pop-up-row"><p>Mobil : </p><input type="text" name="phone_private_c" value="<?php echo isset($phone_private) ? $phone_private : '' ?>"></div>
                <div class="pop-up-row"><p>Email : </p><input type="text" name="email_c" value="<?php echo isset($email) ? $email : '' ?>"></div>
                <div class="pop-up-row"><p>Emergency : </p><input type="text" name="emergency_name_c" value="<?php echo isset($emergency_name) ? $emergency_name : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                </div>
            </div>

            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_delete_employee_pop_up ?>">
                <h3>Slet medarbejder</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                </div>
            </div>
            
        </form>
    </div>

    <script src="medarbejdere.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>
</html>