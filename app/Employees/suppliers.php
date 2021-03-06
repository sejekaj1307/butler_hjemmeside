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


    <!-- -----------------------------
                suppliers CRUD
    ------------------------------ -->
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
            //create pop up
            if($_REQUEST['knap'] == "Opret ny leverandør")
            {
                $display_create_supplier_pop_up = "flex";
            }
            //create
            if($_REQUEST['knap'] == "Opret ny")
            {
                $first_name = $_REQUEST['first_name_c'];
                $last_name = $_REQUEST['last_name_c'];
                $phone = $_REQUEST['phone_c'];
                $address = $_REQUEST['address_c'];
                $email = $_REQUEST['email_c'];
                $product = $_REQUEST['product_c'];

                $sql = $conn->prepare("insert into suppliers (first_name, last_name, phone, address, email, product) values (?, ?, ?, ?, ?, ?)");
                $sql->bind_param("ssssss", $first_name, $last_name, $phone, $address, $email, $product);
                $sql->execute();
                $display_create_supplier_pop_up = "none"; 
            }
            //read 
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
                        $_SESSION["selected_supplier"] = $id;
                        $first_name = $row['first_name'];
                        $last_name = $row['last_name'];
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
                $id = $_SESSION["selected_supplier"];
                $first_name = $_REQUEST['first_name_u'];
                $last_name = $_REQUEST['last_name_u'];
                $phone = $_REQUEST['phone_u'];
                $address = $_REQUEST['address_u'];
                $email = $_REQUEST['email_u'];
                $product = $_REQUEST['product_u'];
                if(is_numeric($id) && is_integer(0 + $id))
                {
                    if(findes($id, $conn)) 
                    {
                        $sql = $conn->prepare("update suppliers set first_name = ?, last_name = ?, phone = ?, address = ?, email = ?, product = ? where id = ?");
                        $sql->bind_param("ssssssi", $first_name, $last_name, $phone, $address, $email, $product, $id);
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
                    if(findes($id, $conn))
                    {
                        $_SESSION["selected_supplier"] = $id;
                        $sql = $conn->prepare("select first_name, last_name from suppliers where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $result = $sql->get_result();
                        if($result->num_rows > 0) 
                        {
                            $row = $result->fetch_assoc();
                            $_SESSION["selected_supplier_first_name"] = $row['first_name'];
                            $_SESSION["selected_supplier_last_name"] = $row['last_name'];
                        }
                        $display_delete_supplier_pop_up = "flex";
                    }
                }
            }
            //Execute - confirm delete
            if($_REQUEST['knap'] == "Slet")
            {
                $id = $_SESSION["selected_supplier"];
                $sql = $conn->prepare("delete from suppliers where id = ?");
                $sql->bind_param("i", $id);
                $sql->execute();          
                $display_delete_supplier_pop_up = "none";      
            }
            //cancel 
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



        <!-- -----------------------------
                  supplier TABLE
        ------------------------------ -->
        <div class="employee_list_page">
            <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret ny leverandør"></div>
            <?php 
                //SQl query to aquire all data from suppliers
                //list headers
                $sql = "select * from suppliers";
                $result = $conn->query($sql);

                echo '<div class="external_list">';
                    echo '<div class="external_list_header">';
                        echo '<p class="external_name_header">Efternavn, fornavn</p>';
                        echo '<div class="external_all_headers">';
                            echo '<p class="external_phone_header">Telefon</p>';
                            echo '<p class="external_phone_header">Adresse</p>';
                            echo '<p class="external_email_header">Email</p>';
                            echo '<p class="external_product_header">Kontaktype</p>';
                            echo '<p class="button_container_header">Rediger</p>';
                        echo '</div>';
                    echo '</div>';

                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        $list_order_id = 1;
                        while($row = $result->fetch_assoc())
                        {
                            //list content
                            echo '<div class="external_data_row">';
                                echo '<div class="mobile_external_information" onclick="open_close_lists_mobile('. $list_order_id .', '. "'external_dropdown_mobile'" .')">';
                                    echo '<p class="external_name">' . $row["last_name"] . ", " . $row["first_name"] . '</p>';
                                echo '</div>';
                                echo '<div class="external_dropdown_mobile" id="'. $row["id"] .'">';
                                    echo '<p class="external_phone">' . '<span class="dropdown_inline_headers">tlf. nr. </span>'  . $row["phone"] . '</p>';
                                    echo '<p class="external_phone">' . '<span class="dropdown_inline_headers">Adresse </span>'  . $row["address"] . '</p>';
                                    echo '<p class="external_email">' . '<span class="dropdown_inline_headers">Email </span>'  . $row["email"] . '</p>';
                                    echo '<p class="external_product">' . '<span class="dropdown_inline_headers">Kontakttype </span>'  . $row["product"] . '</p>';
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


            <!----------------------------
                    Edit profile pop-op
            ----------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_edit_supplier_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Rediger leverandør</h3>
                    <div class="pop-up-row"><p>Fornavn : </p><input type="text" name="first_name_u" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Efternavn : </p><input type="text" name="last_name_u" value="<?php echo isset($last_name) ? $last_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Tlf. nr : </p><input type="text" name="phone_u" value="<?php echo isset($phone) ? $phone : '' ?>"></div>
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
                    <h3>Opret ny leverandør</h3>
                    <div class="pop-up-row"><p>Fornavn : </p><input type="text" name="first_name_c" value="<?php echo isset($first_name) ? $first_name : '' ?>"></div>
                    <div class="pop-up-row"><p>Efternavn : </p><input type="text" name="last_name_c" value="<?php echo isset($last_name) ? $last_name : '' ?>"></div>
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
                    <h3>Slet leverandør</h3>
                    <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["selected_supplier_last_name"]. ', ' . $_SESSION["selected_supplier_first_name"];?>"</i></p>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel"  >
                        <input type="submit" name="knap" value="Slet" class="pop_up_confirm"  >
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