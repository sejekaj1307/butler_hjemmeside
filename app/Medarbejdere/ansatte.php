<!-- Start session -->
<?php session_start(); ?>
<!-- Forbindelse til database -->
    <?php 
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
        <div class="navbar_mid"></div>
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
                Efternavn, Fornavn
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
                //Knapper på siden. Active eller disable. start værdier
                $buttonExecute = "";
                $buttonCancel = "";
                $buttonClear = "";
                $buttonCreate = "";
                $buttonRead = "";
                $buttonUpdate = "";
                $buttonDelete = "";
                $buttonClear = "";

                $display_edit_employee_pop_up = "none";
                $display_delete_employee_pop_up = "none";
                $display_create_employee_pop_up = "none";
            ?>

            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    //read, koden køres hvis "read button" bliver requested 
                    if($_REQUEST['knap'] == "read")
                    {
                        $id = $_REQUEST['id'];
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
                    //create, køres hvis "Tilføj ny medarbejder" bliver requested
                    if($_REQUEST['knap'] == "Tilføj ny medarbejder")
                    {
                        $display_create_employee_pop_up = "flex";
                    }
                    //create, køres hvis "create button" bliver requested
                    if($_REQUEST['knap'] == "create")
                    {
                        $id = $_REQUEST['id'];
                        $first_name = $_REQUEST['first_name'];
                        $initials = $_REQUEST['initials'];
                        $phone = $_REQUEST['phone'];
                        $phone_private = $_REQUEST['phone_private'];
                        $email = $_REQUEST['email'];
                        $emergency_name = $_REQUEST['emergency_name'];
                        if(is_numeric($id) && is_integer(0 + $id)) 
                        {
                            if(!findes($id, $conn)) //opret ny klub
                            {
                                $sql = $conn->prepare("insert into employees (id, first_name, initials, phone, phone_private, email, emergency_name) values (?, ?, ?, ?, ?, ?, ?)");
                                $sql->bind_param("issssss", $id, $first_name, $initials, $phone, $phone_private, $email, $emergency_name);
                                $sql->execute();
                            }
                        } //forkert input af id, klub id skal være heltal
                    }
                    //delete
                    if($_REQUEST['knap'] == "delete")
                    {
                        $id = $_REQUEST['id'];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $_SESSION["bilTilDelete"] = $id;
                                $display_delete_employee_pop_up = "flex";
                            }
                        }
                    }
                    //update
                    if($_REQUEST['knap'] == "update") 
                    {
                        $id = $_REQUEST['id'];
                        $first_name = $_REQUEST['first_name'];
                        $initials = $_REQUEST['initials'];
                        $phone = $_REQUEST['phone'];
                        $phone_private = $_REQUEST['phone_private'];
                        $email = $_REQUEST['email'];
                        $emergency_name = $_REQUEST['emergency_name'];

                        $display_edit_employee_pop_up = "none";
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
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "execute")
                    {
                        //jeg gør brug af $_SESSION variablen for at sikre at hvis der sker ændringer i inputfeltet at det indtastede id forbliver det samme hvis siden genindlæses.
                        //Vi skal have fat i bilid, men vi kan ikke længere bruge den tidligere variabel. Vi skal sikre at brugeren ikke har ændret tallet i mellemtiden
                        $id = $_SESSION["bilTilDelete"];
                        $sql = $conn->prepare("delete from employees where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_employee_pop_up = "none";
                        
                    }
                    //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                    if($_REQUEST['knap'] == "cancel")
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
                <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj ny medarbejder" style="width:80px" <?php echo $buttonCancel ?> ></div>
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
                            echo '<div class="machines_all_headers">';
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
                                // $testercolor = "red";
                                // style="border-left:4px solid $testercolor"
                                echo '<div class="employee_data_row" >';
                                    echo '<div class="mobile_employee_information" onclick=open_close_employee_info()> ';
                                        echo '<p class="employee_name">' . $row["last_name"] . ", " . $row["first_name"] . '</p>';
                                        echo '<p class="employee_initials">' . $row["initials"] . '</p>';
                                    echo '</div>';
                                    echo '<div class="employee_dropdown_mobile">';
                                        echo '<p class="dark_dropdown_table employee_phone">' . $row["phone"] . '</p>';
                                        echo '<p class="light_dropdown_table employee_phone">' . $row["phone_private"] . '</p>';
                                        echo '<p class="dark_dropdown_table employee_email">' . $row["email"] . '</p>';
                                        echo '<p class="light_dropdown_table employee_emergency">' . $row["emergency_name"] . ", " . $row["emergency_phone"] . '</p>';
                                    echo '</div>';
                                    ?> 
                                <div class="button_container">
                                    <input type="submit" name="knap" value="read" style="width:80px" <?php echo $buttonRead ?>>
                                    <input type="submit" name="knap" value="delete" style="width:80px" <?php echo $buttonDelete ?>>
                                    <!-- <input type="submit" name="knap" value="re">
                                    <button type="submit" name="knap" value="de">De</button> -->
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
                <div class="pop-up-row"><p>Name : </p><input type="text" name="first_name" value="<?php echo isset($first_name) ? $first_name : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Initialer : </p><input type="text" name="initials" value="<?php echo isset($initials) ? $initials : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>phone : </p><input type="text" name="phone" value="<?php echo isset($phone) ? $phone : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Mobil : </p><input type="text" name="phone_private" value="<?php echo isset($phone_private) ? $phone_private : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Email : </p><input type="text" name="email" value="<?php echo isset($email) ? $email : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Emergency : </p><input type="text" name="emergency_name" value="<?php echo isset($emergency_name) ? $emergency_name : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="cancel"  class="pop_up_cancel"<?php echo $buttonCancel ?> >
                    <input type="submit" name="knap" value="update" class="pop_up_confirm"<?php echo $buttonUpdate ?>>
                </div>
            </div>

            <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_create_employee_pop_up ?>">
                <h3>Tilføj ny medarbejder</h3>
                <div class="pop-up-row"><p>Name : </p><input type="text" name="first_name" value="<?php echo isset($first_name) ? $first_name : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Initialer : </p><input type="text" name="initials" value="<?php echo isset($initials) ? $initials : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>phone : </p><input type="text" name="phone" value="<?php echo isset($phone) ? $phone : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Mobil : </p><input type="text" name="phone_private" value="<?php echo isset($phone_private) ? $phone_private : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Email : </p><input type="text" name="email" value="<?php echo isset($email) ? $email : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-row"><p>Emergency : </p><input type="text" name="emergency_name" value="<?php echo isset($emergency_name) ? $emergency_name : '' ?>" style="position: relative; left:15px; width:100px; height:22px"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="cancel" class="pop_up_cancel"<?php echo $buttonCancel ?> >
                    <input type="submit" name="knap" value="create" class="pop_up_confirm"<?php echo $buttonCreate ?>>
                </div>
            </div>

            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_delete_employee_pop_up ?>">
                <h3>Slet medarbejder</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="cancel" class="pop_up_cancel" <?php echo $buttonCancel ?> >
                    <input type="submit" name="knap" value="execute" class="pop_up_confirm" <?php echo $buttonExecute ?> >
                </div>
            </div>
            
            <p>
                id : <input type="text" name="id" value="<?php echo isset($id) ? $id : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
            </p>
        </form>
    </div>

</body>
</html>