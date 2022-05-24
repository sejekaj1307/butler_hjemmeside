<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    //If the user is trying go around the log in process, redirect the user back to the index.php 
    if($_SESSION['logged_in_user_global']['last_name'] == ""){
        echo "<script> window.location.href = '../index.php'; </script>";
    }

    $priority = ""; //This variable has to be defined for the html to work correctly. It is for "Create new" priority drop-down menu.   
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Hjælpevideoer</title>
</head>
    <body>

    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
        <ul class="navbar_ul">
            <li><a href="../Profil/profil.php" class="active-main-site">Profil</a></li>
            <li><a href="../Medarbejdere/ansatte.php">Medarbejder</a></li>
            <li><a href="../Kalender/kalender-maskiner.php">Kalender</a></li>
            <li><a href="../Sagsstyring/sager.php">Sagsstyring</a></li>
            <li><a href="../Tidsregistrering/tidsregistrering.php">Tidsregistrering</a></li>
            <li><a href="../Opgaver/fejl-og-mangler.php">Opgaver</a></li>
            <li><a href="../Lagerstyring/lade.php">Lager styring</a></li>
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
            <h2 class="sec-navbar-mobile-header">Profil <div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Profil/profil.php">Profil</a>
                <li><a href="../Profil/notifikationer.php">Notifikationer</a>
                <li><a href="../Profil/hjælpevideoer.php" class="active_site_dropdown">Hjælpevideoer</a></li>
                </li>
            </ul>
        </div>


        <!------------------------------
                content on page
        ------------------------------->
        <div class="profile_list">
            
            <!-- Video 1-->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Opret ny aftale guide</h2>
                    <p>I denne video kan du få hjælp til, hvordan du skal oprette en ny aftale i kalenderen.</p>
                    <p>Dette gælder både for medarbejdere og chefer. Du lærer også hvordan du kan redigere og slette dine allerede eksisterende aftaler.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe class="video" src="https://www.youtube.com/embed/7zT-t-diLNs" allowfullscreen></iframe></div>
                </div>
            </div>
            
            <!-- Video 2-->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Opret nyt skab guide</h2>
                    <p>I denne video kan du få hjælp til, hvordan du skal oprette et nyt skab og sætte indhold i skabet.</p>
                    <p>Du lærer også hvordan du registrerer, hvis du Bruger noget indhold fra et allerede eksisterende skab, eller hvis du vil fylde op og registrere dette. </p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe class="video" src="https://www.youtube.com/embed/7zT-t-diLNs" allowfullscreen></iframe></div>
                </div>
            </div>
            
            <!-- Video 3-->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Opret ny aftale guide</h2>
                    <p>I denne video kan du få hjælp til, hvordan du skal oprette en ny aftale i kalenderen.</p>
                    <p>Dette gælder både for medarbejdere og chefer. Du lærer også hvordan du kan redigere og slette dine allerede eksisterende aftaler.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe class="video" src="https://www.youtube.com/embed/7zT-t-diLNs" allowfullscreen></iframe></div>
                </div>
            </div>
    </div>




    <script src="../javaScript/navbars.js"></script>
</body>
</html>