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
    <title>Arkiverede sager</title>
</head>

<body>

    <!-- Navigationsbar -->
    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
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
        <div class="sec-navbar-mobile ">
            <div class="logged_in">
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> Efternavn, Fornavn</div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Arkiverede sager <div class="arrow_container"><img
                        src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Sagsstyring/sager.php">Sager liste</a></li>
                <li><a href="../Sagsstyring/arkiverede-sager.php" class="active_site_dropdown">Arkiverede sager</a>
                </li>
            </ul>
        </div>





<!-- -----------------------------
            Sager
------------------------------ -->
    <form action="arkiverede-sager.php" method="post">
        <?php
            //har vi en post? har serveren en request?
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                
            }
        ?>


        <div class="case_list_page">
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                $sql = "select * from cases";
                $result = $conn->query($sql);
                echo '<div class="case_list">';
                    echo '<div class="case_list_header">';
                        echo '<div class="case_mobile_headers">';
                            echo '<p class="case_nr_header">Sagsnr.</p>';
                            echo '<p class="case_responsible_header">Ansvarlig</p>';
                            echo '<p class="case_archived_at_header">Arkiveret</p>';
                        echo '</div>';
                        echo '<div class="case_all_headers">';
                            echo '<p class="case_status_header">Status</p>';
                            echo '<p class="case_location_header">Sagsoversigt</p>';
                            echo '<p class="case_est_start_header">Opstart</p>';
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
                                    echo '<p class="dark_dropdown_table case_archived_at">' . $row["archived_at"] . '</p>';
                                echo '</div>';
                                echo '<div class="case_dropdown_mobile">';
                                    echo '<p class="dark_dropdown_table case_status">' . $row["status"] . '</p>';
                                    echo '<p class="light_dropdown_table case_location">' . $row["location"] . '</p>';
                                    echo '<p class="dark_dropdown_table case_est_start">' . $row["est_start_date"] . '</p>';
                                echo '</div>';
                                ?> 
                            <div class="button_container">
                                <button type="submit" name="knap" value="re">UA</button>
                            </div>
                        <?php 

                            echo '</div>';
                        }   
                    }
                echo '</div>';
            ?>
        </div>



    <script src="../javaScript/navbars.js"></script>
</body>

</html>