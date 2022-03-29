<?php 
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");
    session_start(); 
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
            <h2 class="sec-navbar-mobile-header">Leverandører liste <div class="arrow_container"><img
                        src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Medarbejdere/ansatte.php">Ansatte</a></li>
                <li><a href="../Medarbejdere/eksterne.php">Eksterne</a></li>
                <li><a href="../Medarbejdere/leverandoere.php" class="active_site_dropdown">Leverandører</a></li>
                <li><a href="../Medarbejdere/maskiner.php">Maskiner</a>
                </li>
            </ul>
        </div>


    <!-- FORM emploeyee list with CRUD PHP and pop-up modals  -->
    <form action="leverandoere.php" method="post">
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
                        $phone_private = $row['phone_private'];
                        $email = $row['email'];
                        $product = $row['product'];
                        $fejltekst = "read ok";
                        $tekstfarve = "#000000";
                    }
                    else //Hvis ikke der returneres et resultat, hvis det indtastede id ikke findes
                    {
                        $fejltekst = "suppliers nummer $id findes ikke";
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
                $first_name = $_REQUEST['first_name'];
                $phone = $_REQUEST['phone'];
                $phone_private = $_REQUEST['phone_private'];
                $email = $_REQUEST['email'];
                $product = $_REQUEST['product'];
                if(is_numeric($id) && is_integer(0 + $id)) 
                {
                    if(!findes($id, $conn)) //opret ny klub
                    {
                        $sql = $conn->prepare("insert into suppliers (id, first_name, phone, phone_private, email, product) values (?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("isssss", $id, $first_name, $phone, $phone_private, $email, $product);
                        $sql->execute();
                        $fejltekst = "Create Ok";
                        $tekstfarve = "#000000";
                    }
                    else //hvis klub nummer allerede eksiterer i databasen
                    {
                        $fejltekst = "suppliers nummer $id findes allerede";
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
                        $fejltekst = "Tryk 'Execute' for at slette suppliers $id . tryk 'cancel' for at annullere";
                        $tekstfarve = "#ff00ff";
                    }
                }
                else //hvis input af klub id er forkert
                {
                    $fejltekst = "suppliers nummer $id findes ikke";
                    $tekstfarve = "#ff0000";
                }
            }
            //update
            if($_REQUEST['knap'] == "update") 
            {
                $id = $_REQUEST['id'];
                $first_name = $_REQUEST['first_name'];
                $phone = $_REQUEST['phone'];
                $phone_private = $_REQUEST['phone_private'];
                $email = $_REQUEST['email'];
                $product = $_REQUEST['product'];
                if(is_numeric($id) && is_integer(0 + $id))
                {
                    if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                    {
                        $sql = $conn->prepare("update suppliers set first_name = ?, phone = ?, phone_private = ?, email = ?, product = ? where id = ?");
                        $sql->bind_param("sssssi", $first_name, $phone, $phone_private, $email, $product, $id);
                        $sql->execute();
                    }
                    else //forkert input af klub id
                    {
                        $fejltekst = "suppliers nummer $id findes ikke";
                        $tekstfarve = "#ff0000";
                    }
                }
            }
            //Execute - confirm delete
            if($_REQUEST['knap'] == "execute")
            {
                //jeg gør brug af $_SESSION variablen for at sikre at hvis der sker ændringer i inputfeltet at det indtastede id forbliver det samme hvis siden genindlæses.
                //Vi skal have fat i bilid, men vi kan ikke længere bruge den tidligere variabel. Vi skal sikre at brugeren ikke har ændret tallet i mellemtiden
                $id = $_SESSION["bilTilDelete"];
                $sql = $conn->prepare("delete from suppliers where id = ?");
                $sql->bind_param("i", $id);
                $sql->execute();
                $fejltekst = "delete ok";
                $tekstfarve = "#000000";
                
            }
            //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
            if($_REQUEST['knap'] == "cancel")
            {
                $id = "";
                $first_name = "";
                $phone = "";
                $phone_private = "";
                $email = "";
                $product = "";
                $fejltekst = "Delete cancelled";
                $tekstfarve = "#000000";
            }

            //clear
            if($_REQUEST['knap'] == "clear")
            {
                $id = "";
                $first_name = "";
                $phone = "";
                $phone_private = "";
                $email = "";
                $product= "";
                $fejltekst = "suppliers database";
                $tekstfarve = "#000000";
            }
        }
        else 
        {
            $fejltekst = "suppliers database";
            $tekstfarve = "#000000";
        }        
    ?>



        <!-- SELVE TABELLEN -->
        <div class="profile_list">
            <button class="add_new_link"><img src="../img/kryds.png" alt="plus">Tilføj ny leverandør</button>
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
                        while($row = $result->fetch_assoc())
                        {
                            echo '<div class="external_data_row">';
                                echo '<div class="mobile_external_information" onclick=open_close_employee_info()> ';
                                    echo '<p class="external_name">' . $row["last_name"] . ", " . $row["first_name"] . '</p>';
                                echo '</div>';
                                echo '<div class="external_dropdown_mobile">';
                                    echo '<p class="dark_dropdown_table external_phone">' . $row["phone"] . '</p>';
                                    echo '<p class="dark_dropdown_table external_phone">' . $row["address"] . '</p>';
                                    echo '<p class="dark_dropdown_table external_email">' . $row["email"] . '</p>';
                                    echo '<p class="dark_dropdown_table external_product">' . $row["product"] . '</p>';
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
                Name : <input type="text" name="first_name" value="<?php echo isset($first_name) ? $first_name : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
                phone : <input type="text" name="phone" value="<?php echo isset($phone) ? $phone : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
                Mobil : <input type="text" name="address" value="<?php echo isset($phone_private) ? $phone_private : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
                Email : <input type="text" name="email" value="<?php echo isset($email) ? $email : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
                Produkt : <input type="text" name="product" value="<?php echo isset($product) ? $product : '' ?>" style="position: relative; left:15px; width:100px; height:22px">
                <br/>
                <br/>
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