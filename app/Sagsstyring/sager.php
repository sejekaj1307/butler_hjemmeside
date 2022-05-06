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
    <title>Sager</title>
</head>

<body>
    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"></div>
        <ul class="navbar_ul">
            <li><a href="../Profil/profil.php">Profil</a></li>
            <li><a href="../Medarbejdere/ansatte.php">Medarbejder</a></li>
            <li><a href="../Kalender/kalender-maskiner.php">Kalender</a></li>
            <li><a href="../Sagsstyring/sager.php" class="active-main-site">Sagsstyring</a></li>
            <li><a href="../Tidsregistrering/tidsregistrering.php">Tidsregistrering</a></li>
            <li><a href="../Opgaver/fejl-og-mangler.php">Opgaver</a></li>
            <li><a href="../Lagerstyring/lade.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../index.php">Log ud</a></div>
    </div>

    <!-- Masthead -->
    <div class="site_container">
        <div class="sec-navbar-mobile">
            <div class="logged_in">
                Efternavn, Fornavn
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Sager liste<div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Sagsstyring/sager.php" class="active_site_dropdown">Sager liste</a></li>
                <li><a href="../Sagsstyring/arkiverede-sager.php">Arkiverede sager</a>
                </li>
            </ul>
        </div>


<!-- -----------------------------
            Sager
------------------------------ -->
    <form action="sager.php" method="post">
        <?php
            //har vi en post? har serveren en request?
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                //read
                if($_REQUEST['knap'] == "re")
                {
                    // $bilid = $_REQUEST['bilid'];
                    // if(is_numeric($bilid))
                    // {
                    //     $sql = $conn->prepare( "select * from bil where id = ?");
                    //     $sql->bind_param("i", $bilid); //i står for integar
                    //     $sql->execute();
                    //     $result = $sql->get_result();
                    //     $row = $result->fetch_assoc();
                    //     $bilid = $row['id'];
                    //     $model = $row['model'];
                    //     $farve = $row['farve'];
                    //     $aar = $row['aar'];
                    // } 
                    echo "Read";
                }
                //create
                if($_REQUEST['knap'] == "cr")
                {
                    // $bilid = $_REQUEST['bilid'];
                    // $model = $_REQUEST['model'];
                    // $farve = $_REQUEST['farve'];
                    // $aar = $_REQUEST['aar'];
                    // if($model == "") $model = "ukendt";
                    // if($farve == "") $farve = "ukendt";
                    // if($aar == "") $aar = -1;
                    // if(is_numeric($bilid))
                    // {
                    //     $sql = $conn->prepare("insert into bil (id, mode, farve, aar) values (?, ?, ?, ?)");
                    //     $sql->bind_param("issi", $bilid, $model, $farve, $aar);
                    //     $sql->execute();
                    // }
                    echo "Create";
                }
                //delete
                if($_REQUEST['knap'] == "de")
                {
                    // $bilid = $_REQUEST['delete'];
                    // if(is_numeric($bilid))
                    // {
                    //     $sql = $conn->prepare("delete from bil where id = ?");
                    //     $sql->bins_param("i", $bilid);
                    //     $sql->execute();
                    // }
                    echo "delete";
                }
                //update
                if($_REQUEST['knap'] == "up")
                {
                    // $bilid = $_REQUEST['bilid'];
                    // $model = $_REQUEST['model'];
                    // $farve = $_REQUEST['farve'];
                    // $aar = $_REQUEST['aar'];
                    // if($model == "") $model = "ukendt";
                    // if($farve == "") $farve = "ukendt";
                    // if($aar == "") $aar = -1;
                    // if(is_numeric($bilid))
                    // {
                    //     $sql = $conn->prepare("update bil set model = ?, farve = ?, aar = ? where id = ?");
                    //     $sql->bind_param("ssii", $model, $farve, $aar, $bilid);
                    //     $sql->execute();
                    // }
                    echo "update";
                }
            }
        ?>
        <p>
            <input type="submit" name="knap" value="up">
        </p>


        <div class="case_list_page">
            <button class="add_new_link" type="submit" name="knap" value="cr" style="width:80px"><img src="../img/kryds.png" alt="plus">Tilføj ny</button>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                $sql = "select * from cases";
                $result = $conn->query($sql);

                echo '<div class="case_list">';
                    echo '<div class="case_list_header">';
                        echo '<div class="case_mobile_headers">';
                            echo '<p class="case_nr_header">Sagsnr.</p>';
                            echo '<p class="case_responsible_header">Ansvarlig</p>';
                            echo '<p class="case_status_header">Status</p>';
                        echo '</div>';
                        echo '<div class="case_all_headers">';
                            echo '<p class="case_location_header">Sagsoversigt</p>';
                            echo '<p class="case_est_start_header">Opstart</p>';
                            echo '<p class="case_deadline_header">Deadline</p>';
                            echo '<p class="button_container_header">Rediger</p>';
                        echo '</div>';
                    echo '</div>';

                    //if og while her 
                    if($result->num_rows > 0)
                    {
                        while($row = $result->fetch_assoc())
                        {
                            echo '<div class="case_data_row">';
                                echo '<div class="case_information"> ';
                                    echo '<p class="case_nr">' . $row["case_nr"] . '</p>';
                                    echo '<p class="dark_dropdown_table case_responsible">' . $row["case_responsible"] . '</p>';
                                    echo '<p class="light_dropdown_table case_status">' . $row["status"] . '</p>';
                                echo '</div>';
                                echo '<div class="case_dropdown_mobile">';
                                    echo '<p class="dark_dropdown_table case_location">' . $row["location"] . '</p>';
                                    echo '<p class="light_dropdown_table case_est_start">' . $row["est_start_date"] . '</p>';
                                    echo '<p class="dark_dropdown_table case_deadline">' . $row["est_end_date"] . '</p>';
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

        <?php 
        //Man skal huske at slukke for forbindelsen. Det er ikke så vigtigt i små programmer, men vi gør det for en god ordens skyld
            $conn->close();
        
        ?>























    </div>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>