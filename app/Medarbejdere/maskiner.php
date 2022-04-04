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
    <title>Maskiner</title>
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
            <h2 class="sec-navbar-mobile-header">Maskine liste <div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Medarbejdere/ansatte.php">Ansatte</a></li>
                <li><a href="../Medarbejdere/eksterne.php">Eksterne</a></li>
                <li><a href="../Medarbejdere/leverandoere.php">Leverandører</a></li>
                <li><a href="../Medarbejdere/maskiner.php" class="active_site_dropdown">Maskiner</a>
                </li>
            </ul>
        </div>



        <!-- FORM emploeyee list with CRUD PHP and pop-up modals  -->
        <form action="maskiner.php" method="post">
            <?php 
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
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
            ?>

            <?php
            // CRUD, create, read, update, delete - og confirm og cancel knap til delete
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                //create, køres hvis "Tilføj ny medarbejder" bliver requested
                if($_REQUEST['knap'] == "Tilføj ny maskine")
                {
                    $display_create_machine_pop_up = "flex";
                }
                //create, køres hvis "create button" bliver requested
                if($_REQUEST['knap'] == "create")
                {
                    $id = $_REQUEST['id'];
                    $name = $_REQUEST['name'];
                    $name_nordic = $_REQUEST['name_nordic'];
                    $link = $_REQUEST['link'];
                    if(is_numeric($id) && is_integer(0 + $id)) 
                    {
                        if(!findes($id, $conn)) //opret ny klub
                        {
                            $sql = $conn->prepare("insert into machines (id, name, name_nordic, link) values (?, ?, ?, ?)");
                            $sql->bind_param("isss", $id, $name, $name_nordic, $link);
                            $sql->execute();
                            $display_create_machine_pop_up = "none";
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
                        $sql = $conn->prepare( "select * from machines where id = ?");
                        $sql->bind_param("i", $id); 
                        $sql->execute();
                        $result = $sql->get_result();
                        if($result->num_rows > 0) 
                        {
                            $row = $result->fetch_assoc();
                            $id = $row['id'];
                            $name = $row['name'];
                            $name_nordic = $row['name_nordic'];
                            $link = $row['link'];
                            $display_edit_machine_pop_up = "flex";
                        }
                    } 
                }
                //update
                if($_REQUEST['knap'] == "update") 
                {
                    $id = $_REQUEST['id'];
                    $name = $_REQUEST['name'];
                    $name_nordic = $_REQUEST['name_nordic'];
                    $link = $_REQUEST['link'];
                    if(is_numeric($id) && is_integer(0 + $id))
                    {
                        if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                        {
                            $sql = $conn->prepare("update machines set name = ?, name_nordic = ?, link = ? where id = ?");
                            $sql->bind_param("sssi", $name, $name_nordic, $link, $id);
                            $sql->execute();
                            $display_edit_machine_pop_up = "none";
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
                            $display_delete_machine_pop_up = "flex";
                        }
                    }
                }
                //Execute - confirm delete
                if($_REQUEST['knap'] == "execute")
                {
                    //jeg gør brug af $_SESSION variablen for at sikre at hvis der sker ændringer i inputfeltet at det indtastede id forbliver det samme hvis siden genindlæses.
                    $id = $_SESSION["bilTilDelete"];
                    $sql = $conn->prepare("delete from machines where id = ?");
                    $sql->bind_param("i", $id);
                    $sql->execute();
                    $display_delete_machine_pop_up = "none";
                }
                //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                if($_REQUEST['knap'] == "cancel")
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



        <!-- SELVE TABELLEN -->
        <div class="profile_list">
            <div class="add_new_link" ><img src="../img/kryds.png" alt="plus"><input type="submit" name="knap" value="Tilføj ny maskine"></div>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
               $sql = "select * from machines";
                $result = $conn->query($sql);

                function tester() {
                    echo '<script>console.log("Maja")</script>';
                }

                echo '<div class="machine_list">';
                    echo '<div class="machine_list_header">';
                    echo '<p class="machine_name_header">Navn</p>';
                        echo '<div class="machines_all_headers">';
                            echo '<p class="machine_nordic_name_header">Nordic navn</p>';
                            echo '<p class="machine_link_header">Link til BB hjemmeside</p>';
                            echo '<p class="button_container_header">Rediger</p>';
                        echo '</div>';
                    echo '</div>';

                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        while($row = $result->fetch_assoc())
                        {
                            echo '<div class="machine_data_row">';
                                echo '<div class="machine_information" onclick=open_close_employee_info()> ';
                                    echo '<p class="machine_name">' . $row["name"] . '</p>';
                                echo '</div>';
                                echo '<div class="machine_dropdown_mobile">';
                                    echo '<p class="dark_dropdown_table machine_nordic_name">' . $row["name_nordic"] . '</p>';
                                    echo '<p class="light_dropdown_table machine_link">' . "<a href=" . $row['link'] . ">Link til BB hjemmeside</a>" . '</p>';
                                echo '</div>';
                                ?> 
                                    <div class="button_container">
                                        <input type="submit" name="knap" value="read_<?php echo $row['id'];?>">
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
            <div class="pop_up_modal" style="display: <?php echo $display_edit_machine_pop_up ?>">
                <h3>Opdater medarbejderprofil</h3>
                <div class="pop-up-row"><p>Navn : </p><input type="text" name="name" value="<?php echo isset($name) ? $name : '' ?>"></div>
                <div class="pop-up-row"><p>Nordisk navn : </p><input type="text" name="name_nordic" value="<?php echo isset($name_nordic) ? $name_nordic : '' ?>"></div>
                <div class="pop-up-row"><p>Link : </p><input type="text" name="link" value="<?php echo isset($link) ? $link : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="cancel"  class="pop_up_cancel">
                    <input type="submit" name="knap" value="update" class="pop_up_confirm">
                </div>
            </div>

            <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_create_machine_pop_up ?>">
                <h3>Tilføj ny medarbejder</h3>
                id : <input type="text" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="pop-up-row"><p>Navn : </p><input type="text" name="name" value="<?php echo isset($name) ? $name : '' ?>"></div>
                <div class="pop-up-row"><p>Nordisk navn : </p><input type="text" name="name_nordic" value="<?php echo isset($name_nordic) ? $name_nordic : '' ?>"></div>
                <div class="pop-up-row"><p>Link : </p><input type="text" name="link" value="<?php echo isset($link) ? $link : '' ?>"></div>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="cancel" class="pop_up_cancel">
                    <input type="submit" name="knap" value="create" class="pop_up_confirm">
                </div>
            </div>

            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_delete_machine_pop_up ?>">
                <h3>Slet medarbejder</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="cancel" class="pop_up_cancel">
                    <input type="submit" name="knap" value="execute" class="pop_up_confirm">
                </div>
            </div>
        </form>

    </div>



    <script src="../javaScript/navbars.js"></script>
</body>

</html>