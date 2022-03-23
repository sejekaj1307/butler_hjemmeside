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
                //Knapper på siden. Active eller disable. start værdier
                $buttonExecute = "disabled";
                $buttonCancel = "disabled";
                $buttonClear = "";
                $buttonCreate = "";
                $buttonRead = "";
                $buttonUpdate = "";
                $buttonDelete = "";
                $buttonClear = "";
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
                        }
                        else //Hvis ikke der returneres et resultat, hvis det indtastede id ikke findes
                        {
                            $fejltekst = "machines nummer $id findes ikke";
                            $tekstfarve = "#ff0000";
                        }
                    } 
                    else //Hvis brugeren ikke har indtastet en korrekt værdi (heltal)
                    {
                        $fejltekst = "id skal være heltal";
                        $tekstfarve = "#ff0000";
                    }
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
                            $fejltekst = "Create Ok";
                            $tekstfarve = "#000000";
                        }
                        else //hvis klub nummer allerede eksiterer i databasen
                        {
                            $fejltekst = "machines nummer $id findes allerede";
                            $tekstfarve = "#ff0000";
                        }
                    } //forkert input af id, klub id skal være heltal
                    else 
                    {
                        $fejltekst = "id skal være heltal";
                        $tekstfarve = "#ff0000";
                    }
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
                            $buttonExecute = "";
                            $buttonCancel = "";
                            $buttonClear = "disabled";
                            $buttonRead = "disabled";
                            $buttonCreate = "disabled";
                            $buttonUpdate = "disabled";
                            $buttonDelete = "disabled";
                            $buttonClear = "disabled";
                            $fejltekst = "Tryk 'Execute' for at slette machines $id . tryk 'cancel' for at annullere";
                            $tekstfarve = "#ff00ff";
                        }
                    }
                    else //hvis input af klub id er forkert
                    {
                        $fejltekst = "machines nummer $id findes ikke";
                        $tekstfarve = "#ff0000";
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
                        }
                        else //forkert input af klub id
                        {
                            $fejltekst = "machines nummer $id findes ikke";
                            $tekstfarve = "#ff0000";
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
                    $fejltekst = "delete ok";
                    $tekstfarve = "#000000";
                    
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
                    $fejltekst = "Delete cancelled";
                    $tekstfarve = "#000000";
                }

                //clear
                if($_REQUEST['knap'] == "clear")
                {
                    $id = "";
                    $name = "";
                    $name_nordic = "";
                    $link = "";
                    $email = "";
                    $product= "";
                    $fejltekst = "machines database";
                    $tekstfarve = "#000000";
                }
            }
            else 
            {
                $fejltekst = "machines database";
                $tekstfarve = "#000000";
            }        
        ?>



        <!-- SELVE TABELLEN -->
        <div class="profile_list">
            <button class="add_new_link"><img src="../img/kryds.png" alt="plus">Tilføj ny maskine</button>
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
                                        <button type="submit" name="knap" value="re">Re</button>
                                        <button type="submit" name="knap" value="de">De</button>
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

            <p>
                id : <input type="text" name="id" value="<?php echo isset($id) ? $id : '' ?>" style="position: relative; left:15px; width:100px; height:22px"> <!--Isset i php tjekker om følgende har en værdi-->
                <br/>
                <br/>
                Name : <input type="text" name="name" value="<?php echo isset($name) ? $name : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
                Nordic_name : <input type="text" name="name_nordic" value="<?php echo isset($name_nordic) ? $name_nordic : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
                Link : <input type="text" name="link" value="<?php echo isset($link) ? $link : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
            </p>
            <p>
                <input type="submit" name="knap" value="read" style="width:80px" <?php echo $buttonRead ?>>
                <input type="submit" name="knap" value="update" style="width:80px" <?php echo $buttonUpdate ?>>
                <input type="submit" name="knap" value="create" style="width:80px" <?php echo $buttonCreate ?>>
                <input type="submit" name="knap" value="delete" style="width:80px" <?php echo $buttonDelete ?>>
                <input type="submit" name="knap" value="clear" style="width:80px" <?php echo $buttonClear ?>>
                <input type="submit" name="knap" value="execute" style="width:80px" <?php echo $buttonExecute ?> >
                <input type="submit" name="knap" value="cancel" style="width:80px" <?php echo $buttonCancel ?> >
            </p>

        </form>

    </div>



    <script src="../javaScript/navbars.js"></script>
</body>

</html>