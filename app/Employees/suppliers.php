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
    <title>Leverandører</title>
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
            <li><a href="../Time_registration/time_registration.php">Tidsregistrering</a></li>
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
            <h2 class="sec-navbar-mobile-header">Leverandører liste <div class="arrow_container"><img
                        src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Employees/employees.php">Ansatte</a></li>
                <li><a href="../Employees/externals.php">Eksterne</a></li>
                <li><a href="../Employees/suppliers.php" class="active_site_dropdown">Leverandører</a></li>
                <li><a href="../Employees/machines.php">Maskiner</a>
                </li>
            </ul>
        </div>


    <!-- FORM emploeyee list with CRUD PHP and pop-up modals  -->
    <form action="suppliers.php" method="post">
        <?php 
        //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
            function findes($id, $c)
            {
                $sql = $c->prepare("select * from suppliers where id = ?");
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
            $display_edit_supplier_pop_up = "none";
            $display_delete_supplier_pop_up = "none";
            $display_create_supplier_pop_up = "none";
            
        ?>

        <?php
        // CRUD, create, read, update, delete - og confirm og cancel knap til delete
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //create, køres hvis "Tilføj ny medarbejder" bliver requested
            if($_REQUEST['knap'] == "Tilføj ny leverandør")
            {
                $display_create_supplier_pop_up = "flex";
            }
            //create, køres hvis "create button" bliver requested
            if($_REQUEST['knap'] == "Opret ny")
            {
                $id = $_REQUEST['id_c'];
                $first_name = $_REQUEST['first_name_c'];
                $phone = $_REQUEST['phone_c'];
                $address = $_REQUEST['address_c'];
                $email = $_REQUEST['email_c'];
                $product = $_REQUEST['product_c'];
                if(is_numeric($id) && is_integer(0 + $id)) 
                {
                    if(!findes($id, $conn)) //opret ny klub
                    {
                        $sql = $conn->prepare("insert into suppliers (id, first_name, phone, address, email, product) values (?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("isssss", $id, $first_name, $phone, $address, $email, $product);
                        $sql->execute();
                        $display_create_supplier_pop_up = "none";
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
                    $sql = $conn->prepare( "select * from suppliers where id = ?");
                    $sql->bind_param("i", $id); 
                    $sql->execute();
                    $result = $sql->get_result();
                    if($result->num_rows > 0) 
                    {
                        $row = $result->fetch_assoc();
                        $id = $row['id'];
                        $first_name = $row['first_name'];
                        $phone = $row['phone'];
                        $address = $row['address'];
                        $email = $row['email'];
                        $product = $row['product'];
                        $display_edit_supplier_pop_up = "flex";
                    }
                }
            }
            //update
            if($_REQUEST['knap'] == "Opdater") 
            {
                $id = $_REQUEST['id_u'];
                $first_name = $_REQUEST['first_name_u'];
                $phone = $_REQUEST['phone_u'];
                $address = $_REQUEST['address_u'];
                $email = $_REQUEST['email_u'];
                $product = $_REQUEST['product_u'];
                if(is_numeric($id) && is_integer(0 + $id))
                {
                    if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                    {
                        $sql = $conn->prepare("update suppliers set first_name = ?, phone = ?, address = ?, email = ?, product = ? where id = ?");
                        $sql->bind_param("sssssi", $first_name, $phone, $address, $email, $product, $id);
                        $sql->execute();
                        $display_edit_supplier_pop_up = "none";
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
                        $display_delete_supplier_pop_up = "flex";
                    }
                }
            }
            //Execute - confirm delete
            if($_REQUEST['knap'] == "Slet")
            {
                $id = $_SESSION["bilTilDelete"];
                $sql = $conn->prepare("delete from suppliers where id = ?");
                $sql->bind_param("i", $id);
                $sql->execute();          
                $display_delete_supplier_pop_up = "none";      
            }
            //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
            if($_REQUEST['knap'] == "Annuller")
            {
                $id = "";
                $first_name = "";
                $phone = "";
                $address = "";
                $email = "";
                $product = "";

                $display_edit_supplier_pop_up = "none";
                $display_delete_supplier_pop_up = "none";
            }
        }
    ?>



        <!-- SELVE TABELLEN -->
        <div class="profile_list">
            <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj ny leverandør"></div>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                $sql = "select * from suppliers";
                $result = $conn->query($sql);

                echo '<div class="external_list">';
                    echo '<div class="external_list_header">';
                        echo '<p class="external_name_header">Efternavn, fornavn</p>';
                        echo '<div class="external_all_headers">';
                            echo '<p class="external_phone_header">Telefon</p>';
                            echo '<p class="external_phone_header">Adresse</p>';
                            echo '<p class="external_email_header">Email</p>';
                            echo '<p class="external_product_header">Produkt</p>';
                            echo '<p class="button_container_header">Rediger</p>';
                        echo '</div>';
                    echo '</div>';

                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        $list_order_id = 1;
                        while($row = $result->fetch_assoc())
                        {
                            echo '<div class="external_data_row">';
                                echo '<div class="mobile_external_information" onclick="open_close_lists_mobile('. $list_order_id .', '. "'external_dropdown_mobile'" .')">';
                                    echo '<p class="external_name">' . $row["last_name"] . ", " . $row["first_name"] . '</p>';
                                echo '</div>';
                                echo '<div class="external_dropdown_mobile" id="'. $row["id"] .'">';
                                    echo '<p class="external_phone">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["phone"] . '</p>';
                                    echo '<p class="external_phone">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["address"] . '</p>';
                                    echo '<p class="external_email">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["email"] . '</p>';
                                    echo '<p class="external_product">' . '<span class="dropdown_inline_headers">Seneste </span>'  . $row["product"] . '</p>';
                                echo '</div>';
                                
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



        <!-- KNAPPERNE OG INPUT FELTERNE TIL AT ÆNDRE OG READ -->
            <?php 
            //Jeg lukker forbindelsen til databasen, af sikkerhedsmæssige årcases
                $conn->close();
            ?>


            <!----------------------------
                    Edit profile pop-op
            ----------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_edit_supplier_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Opdater ekstern</h3>
                    id : <input type="text" name="id_u" value="<?php echo isset($id) ? $id : '' ?>">
                    <div class="pop-up-row"><p>Name : </p><input type="text" name="first_name_u" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                    <div class="pop-up-row"><p>phone : </p><input type="text" name="phone_u" value="<?php echo isset($phone) ? $phone : '' ?>"></div>
                    <div class="pop-up-row"><p>Adresse : </p><input type="text" name="address_u" value="<?php echo isset($address) ? $address : '' ?>"></div>
                    <div class="pop-up-row"><p>Email : </p><input type="text" name="email_u" value="<?php echo isset($email) ? $email : '' ?>"></div>
                    <div class="pop-up-row"><p>Kontakt type : </p><input type="text" name="product_u" value="<?php echo isset($product) ? $product : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel" >
                        <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                    </div>
                </div>
            </div>

            <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_create_supplier_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Tilføj ny ekstern</h3>
                    id : <input type="text" name="id_c" value="<?php echo isset($id) ? $id : '' ?>">
                    <div class="pop-up-row"><p>Name : </p><input type="text" name="first_name_c" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                    <div class="pop-up-row"><p>phone : </p><input type="text" name="phone_c" value="<?php echo isset($phone) ? $phone : '' ?>"></div>
                    <div class="pop-up-row"><p>Adresse : </p><input type="text" name="address_c" value="<?php echo isset($address) ? $address : '' ?>"></div>
                    <div class="pop-up-row"><p>Email : </p><input type="text" name="email_c" value="<?php echo isset($email) ? $email : '' ?>"></div>
                    <div class="pop-up-row"><p>Kontakt type : </p><input type="text" name="product_c" value="<?php echo isset($product) ? $product : '' ?>"></div>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller"  class="pop_up_cancel" >
                        <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                    </div>
                </div>
            </div>

            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_delete_supplier_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Slet ekstern</h3>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel"  >
                        <input type="submit" name="knap" value="Slet" class="pop_up_confirm"  >
                    </div>
                </div>
            </div>
        </form>

    </div>






    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>