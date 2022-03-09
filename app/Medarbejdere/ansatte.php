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
        </div>


            <?php 
                //Vi skal have vist tabellen på siden. query er en forspørgsel, som sættes ud fra sql. (den sql vi gerne vil have lavet, send den som en forespørgesel til databasen)
                // $sql = "select * from bil";
                // $result = $conn->query($sql);

                // echo '<table border="5" cellpadding="5">';
                // echo '<tr>';
                // echo "<th>BilId</th>";
                // echo "<th>Model</th>";
                // echo "<th>Farve</th>";
                // echo "<th>År</th>";
                // echo '</tr>';

                // //Vi tjekker om der er biler. der kan opstå fejl, hvis man beder php printe noget og der ikke er noget data. vi undersøger, om vi har data
                // if($result->num_rows > 0)
                // {
                //     while($row = $result->fetch_assoc())
                //     {
                //         echo "<tr>";
                //         echo "<td>" . $row["id"] . "</td>";
                //         echo "<td>" . $row["model"] . "</td>";
                //         echo "<td>" . $row["farve"] . "</td>";
                //         echo "<td>" . $row["aar"] . "</td>";
                //         echo "</tr>";
                //     }
                // }



                // echo '</table>';
            
            ?>


    </div>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>