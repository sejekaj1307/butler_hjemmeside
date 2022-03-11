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



        <div class="profile_list">
            <button class="add-new-link"><img src="../img/kryds.png" alt="plus">Tilføj ny</button>
            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
               $sql = "select * from employees";
                $result = $conn->query($sql);

                function tester() {
                    echo '<script>console.log("Maja")</script>';
                }

                echo '<div class="employee_list">';
                    echo '<div class="table_row">';
                        echo '<div class="employee_list_header">';
                            echo '<div class="mobile_employee_information_header">';
                                echo '<p class="table_first_item_header">Medarbejder</p>';
                                echo '<p class="employee_initials_header">Initialer</p>';
                            echo '</div>';
                            echo '<div class="employee_dropdown_mobile_all_headers">';
                                echo '<p class="employee_phone_header">Arbejds-tlf</p>';
                                echo '<p class="employee_phone_header">Mobil</p>';
                                echo '<p class="employee_email_header">Email</p>';
                                echo '<p class="employee_emergency_header">Kontaktperson</p>';
                            echo '</div>';
                        echo '</div>';

                        //if og while her 
                        if($result->num_rows > 0)
                        {
                            while($row = $result->fetch_assoc())
                            {
                                echo '<div class="employee_tester">';
                                    echo '<div class="mobile_employee_information" onclick=open_close_employee_info()> ';
                                        echo '<p class="table_first_item">' . $row["last_name"] . ", " . $row["first_name"] . '</p>';
                                        echo '<p class="employee_initials">' . $row["initials"] . '</p>';
                                    echo '</div>';
                                    echo '<div class="employee_dropdown_mobile">';
                                        echo '<p class="dark_dropdown_table employee_phone">' . $row["phone"] . '</p>';
                                        echo '<p class="light_dropdown_table employee_phone">' . $row["phone_private"] . '</p>';
                                        echo '<p class="dark_dropdown_table employee_email">' . $row["email"] . '</p>';
                                        echo '<p class="light_dropdown_table employee_emergency">' . $row["emergency_name"] . ", " . $row["emergency_phone"] . '</p>';
                                    echo '</div>';
                                echo '</div>';
                            }   
                        }
                    echo '</div>';
                echo '</div>';
            ?>
        </div>

       

    </div>
    <script src="../javaScript/navbars.js"></script>
    <script src="medarbejdere.js"></script>
</body>

</html>