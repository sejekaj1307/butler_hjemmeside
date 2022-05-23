<?php 
    //session start
    session_start(); 
    //Forbindelse til database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    include("element-location-options.php");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Lager - lade</title>
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
            <li><a href="../Opgaver/fejl-og-mangler.php">Opgaver</a></li>
            <li><a href="../Lagerstyring/lade.php" class="active-main-site">Lager styring</a></li>
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
            <h2 class="sec-navbar-mobile-header">Lade lager <div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Lagerstyring/lade.php" class="active_site_dropdown">Lade lager</a></li>
            </ul>
        </div>


    

        <!-- FORM emploeyee list with CRUD PHP and pop-up modals  -->
        <form action="lade.php" method="post">
            <?php 
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
                function findes($id, $c)
                {
                    $sql = $c->prepare("select * from storage where id = ?");
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
                $display_edit_storage_pop_up = "none";
                $display_create_element_pop_up = "none";
                $display_delete_storage_pop_up = "none";


            ?>

            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //create, køres hvis "Tilføj ny medarbejder" bliver requested
                    if($_REQUEST['knap'] == "Opret nyt element")
                    {
                        $display_create_element_pop_up = "flex";
                    }
                    //create, køres hvis "create button" bliver requested
                    if($_REQUEST['knap'] == "Opret")
                    {
                        $location = $_REQUEST['location_c'];
                        $element = $_REQUEST['element_c'];
                        $quantity = $_REQUEST['quantity_c'];
                        $min_quantity = $_REQUEST['min_quantity_c'];
                        $comment = $_REQUEST['comment_c'];
                        $sql = $conn->prepare("insert into storage (element_location, element, quantity, min_quantity, comment) values (?, ?, ?, ?, ?)");
                        $sql->bind_param("ssiis", $location, $element, $quantity, $min_quantity, $comment);
                        $sql->execute();
                        
                    }
                    //read, koden køres hvis "read button" bliver requested 
                    if(str_contains($_REQUEST['knap'] , "read"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_numeric(0 + $id))
                        {
                            $sql = $conn->prepare( "select * from storage where id = ?");
                            $sql->bind_param("i", $id); 
                            $sql->execute();
                            $result = $sql->get_result();
                            if($result->num_rows > 0) 
                            {
                                $row = $result->fetch_assoc();
                                $id = $row['id'];
                                $_SESSION["selected_element"] = $id;
                                $element = $row['element'];
                                $status = $row['status'];
                                $element_location = $row['element_location'];
                                $quantity = $row['quantity'];
                                $min_quantity = $row['min_quantity'];
                                $updated_initials = $row['updated_initials'];
                                $comment = $row['comment'];

                                $display_edit_storage_pop_up = "flex";
                            }
                        }
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater") 
                    {
                        $id = $_SESSION["selected_element"];
                        $element = $_REQUEST['element_u'];
                        $element_location = $_REQUEST['element_location_u'];
                        $quantity = $_REQUEST['quantity_u'];
                        $min_quantity = $_REQUEST['min_quantity_u'];
                        $updated_initials = $_SESSION['logged_in_user_global']['initials'];
                        $comment = $_REQUEST['comment_u'];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                            {
                                $sql = $conn->prepare("update storage set element = ?, element_location = ?, quantity = ?, min_quantity = ?, updated_initials = ?, comment = ? where id = ?");
                                $sql->bind_param("ssiissi", $element, $element_location, $quantity, $min_quantity, $updated_initials, $comment, $id);
                                $sql->execute();    
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
                                $_SESSION["selected_element"] = $id;
                                $display_delete_storage_pop_up = "flex";
                            }
                        }
                    }
                    // Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["selected_element"];
                        $sql = $conn->prepare("delete from storage where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_storage_pop_up = "none";
                        
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
                <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Opret nyt element"></div>
                <?php 
                    //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                    $sql = "select * from storage order by element_location asc";
                    $result = $conn->query($sql);
                    echo '<div class="harmonica_harmonica">';
                        echo '<div class="harmonica_headers">';
                            echo '<div class="harmonica_mobile_headers">';
                                echo '<p class="harmonica_element_headers">Element</p>';
                            echo '</div>';
                            echo '<div class="harmonica_all_headers">';
                                echo '<p class="harmonica_quantity_header">Antal</p>';
                                echo '<p class="harmonica_status_header">Status</p>';
                                echo '<p class="harmonica_updated_by_header">Seneste</p>';
                                echo '<p class="harmonica_comment_header">Bemærkning</p>';
                                echo '<p class="button_container_header">Rediger</p>';
                            echo '</div>';
                        echo '</div>';
                        //if og while her
                        $seen_element_location=array();
                        //
                        if($result->num_rows > 0)
                        {
                            while($row = $result->fetch_assoc()) {
                                if(!in_array($row['element_location'], $seen_element_location)){
                                    array_push($seen_element_location, $row['element_location']); 
                                    echo '<div class="harmonica_data_row" >';
                                        echo '<div class="harmonica_information" onclick="open_close_harmonica('. array_search($row["element_location"], $seen_element_location) .', '. "'harmonica_data_row_all_info'" .')" >  ';
                                            echo '<p class="harmonica_task_header">' . $row['element_location'] . '</p>';
                                        echo '</div>';
                                    echo '</div>';         
                                }

                                if($row['quantity'] == 0) {
                                    $status = "Tom";
                                    $status_color = "#FFA2A2";
                                } else if ($row['quantity'] < $row['min_quantity']) {
                                    $status = "lav";
                                    $status_color = "#FFFC9E";
                                }
                                else {
                                    $status = "Fuld";
                                    $status_color = "#BBFFB9";
                                }
                                                        
                                echo '<div class="harmonica_data_row_all_info" id="'. array_search($row["element_location"], $seen_element_location) .'" style="border-left: 5px solid ' . $status_color . '" >';
                                    echo '<div class="data_row_info">';
                                        echo '<p class="harmonica_element">' . $row["element"] . '</p>';
                                        echo '<p class="harmonica_quantity">' . '<span class="dropdown_inline_headers">Antal </span>' . $row["quantity"] . '</p>';
                                        echo '<p class="harmonica_status">' . '<span class="dropdown_inline_headers">Status </span>' . $status . '</p>';
                                        echo '<p class=" harmonica_updated_by">' . '<span class="dropdown_inline_headers">Seneste </span>' . $row["updated_initials"] . '</p>';
                                        echo '<p class=" harmonica_comment">' . '<span class="dropdown_inline_headers">Bemærkning </span>' . $row["comment"] . '</p>';
                                    echo "</div>";
                                
                                    echo '<div class="button_container">';
                                        echo '<button type="submit" name="knap" value="read_' . $row['id'] . '"><img src="../img/edit.png" alt="Employee icon" class="edit_icons"<button>';
                                        echo '<button type="submit" name="knap" value="slet_' . $row['id'] . '"><img src="../img/trash.png" alt="Employee icon" class="edit_icons"<button>';
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
            <!--------------------------------------
                    Create new storage pop-op
            --------------------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_create_element_pop_up ?>">
                <h3>Opret nyt element</h3>
                <div class="pop-up-row">
                    <p>Lokation : </p>
                    <select name="element_location_c">
                        <?php
                            foreach($element_location_options as $element_location_option){
                                echo "<option " . ($element_location == $element_location_option ? 'selected' : '') . "value=" . $element_location_option . ">" . $element_location_option . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="pop-up-row"><p>Element : </p><input type="text" name="element_c" value="<?php echo isset($element) ? $element : '' ?>"></div>
                <div class="pop-up-row"><p>Antal : </p><input type="number" name="quantity_c" value="<?php echo isset($quantity) ? $quantity : '' ?>"></div>
                <div class="pop-up-row"><p>Min. antal : </p><input type="number" name="min_quantity_c" value="<?php echo isset($min_quantity) ? $min_quantity : '' ?>"></div>
                <div class="pop-up-row"><p>Bemærkning : </p><input type="text" name="comment_c" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opret" class="pop_up_confirm">
                </div>
            </div>

            <!----------------------------
                    Edit storage pop-op
            ----------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_edit_storage_pop_up ?>">
                <h3>Opdater element</h3>
                <div class="pop-up-row">
                    <p>Lokation : </p>
                    <select name="element_location_u">
                        <?php
                            foreach($element_location_options as $element_location_option){
                                echo "<option " . ($element_location == $element_location_option ? 'selected' : '') . "value=" . $element_location_option . ">" . $element_location_option . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="pop-up-row"><p>Element : </p><input type="text" name="element_u" value="<?php echo isset($element) ? $element : '' ?>"></div>
                <div class="pop-up-row"><p>Antal : </p><input type="number" name="quantity_u" value="<?php echo isset($quantity) ? $quantity : '' ?>"></div>
                <div class="pop-up-row"><p>Min. antal : </p><input type="number" name="min_quantity_u" value="<?php echo isset($min_quantity) ? $min_quantity : '' ?>"></div>
                <div class="pop-up-row"><p>Bemærkning : </p><input type="text" name="comment_u" value="<?php echo isset($comment) ? $comment : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
                </div>
            </div>

            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_delete_storage_pop_up ?>">
                <h3>Slet element</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                </div>
            </div>
            
        </form>
    </div>

    <script src="lager-harmonika.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>