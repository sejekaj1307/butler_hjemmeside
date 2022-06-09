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
            <li><a href="../Profile/profile.php" class="active-main-site">Profil</a></li>
            <li><a href="../Employees/employees.php">Medarbejder</a></li>
            <li><a href="../Calender/machines_calender.php">Kalender</a></li>
            <li><a href="../Cases/cases.php">Sager</a></li>
            <li><a href="../Time_registration/time_registration.php">Tidsregistrering</a></li>
            <li><a href="../Tasks/tasks.php">Opgaver</a></li>
            <li><a href="../Storage/storage.php">Lager styring</a></li>
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
            <h2 class="sec-navbar-mobile-header">Profile <div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Profile/profile.php">Profil</a>
                <li><a href="../Profile/notifications.php">Notifikationer</a>
                <li><a href="../Profile/video_guides.php" class="active_site_dropdown">Hjælpevideoer</a></li>
                </li>
            </ul>
        </div>


        <!------------------------------
                    Videos
        ------------------------------->
        <div class="profile_list">
            
            <!-- Video 1 Opret ny opgave og rediger den-->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Opret ny opgave og rediger den</h2>
                    <p>I denne video kan du få hjælp til, hvordan du skal oprette en ny opgave under <i>Fejl og mangler</i> i <i>Opgaver</i>.</p>
                    <br>
                    <p>Se også hvordan du kan redigere en ønsket opgave.</p>
                    <br>
                    <p>Hvis du ønsker mere information se også brugermanualen.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe sandbox class="video" src="video_guides/Opret-ny-opgave-og-rediger-hjælpevideo.mp4" allowfullscreen></iframe></div>
                </div>
            </div>
            
            <!-- Video 2 arkiver og aktiver -->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Arkiver og aktiver en opgave</h2>
                    <p>I denne video kan du få hjælp til, hvordan du kan arkivere en opgave, hvor du kan finde den og hvordan du kan aktivere den igen.</p>
                    <p>Fremgangsmåden er ens for <i>opgaver</i> og <i>sager</i>.</p>
                    <br>
                    <p><i>OBS:</i> Vær opmærksom på, at hvis du ønsker at redigere en opgave eller sag, skal de aktiveres igen og findes på den aktive liste.</p>
                    <br>
                    <p>Hvis du ønsker mere information se også brugermanualen.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe sandbox class="video" src="video_guides/Arkiver-aktiver-hjælpevideo.mp4" allowfullscreen></iframe></div>
                </div>
            </div>
            
            <!-- Video 3 slet opgave -->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Slet en opgave</h2>
                    <p>I denne video kan du få hjælp til, hvordan du sletter en opgave.</p>
                    <p><i>OBS:</i> Fremgangsmåden er ens for alle elementer, der kan slettes.</p>
                    <br>
                    <p>Hvis du ønsker mere information se også brugermanualen.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe sandbox class="video" src="video_guides/Slet-opgave-hjælpevideo.mp4" allowfullscreen></iframe></div>
                </div>
            </div>
            <!-- Video 4 planlagt service -->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Opret ny planlagt service og rediger den</h2>
                    <p>I denne video kan du få hjælp til, hvordan du skal oprette en <i>planlagt service</i> under <i>opgaver</i>, og hvordan du kan redigere den.</p>
                    <br>
                    <p>Hvis du ønsker mere information se også brugermanualen.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe sandbox class="video" src="video_guides/planlagt-service-hjælpevideo.mp4" allowfullscreen></iframe></div>
                </div>
            </div>
            <!-- Video 5 Opret ny sag -->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Opret ny sag</h2>
                    <p>I denne video kan du få hjælp til, hvordan du kan oprette en ny <i>sag</i> under <i>sagsstyring</i>.</p>
                    <br>
                    <p><i>OBS:</i> vær opmærksom på at <i>opret ny sag</i> kun indebærer de helt basale elementer for en sag, sagen skal herefter uddybes i <i>beskriv sag</i></p>
                    <br>
                    <p>Hvis du ønsker mere information se også brugermanualen.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe sandbox class="video" src="video_guides/Opret-ny-sag-hjælpevideo.mp4" allowfullscreen></iframe></div>
                </div>
            </div>
            <!-- Video 6 beskriv ny sag -->
            <div class="video_guides_container">
                <div class="video_text">
                    <h2>Beskriv sag</h2>
                    <p>I denne video kan du få hjælp til, hvordan du beskriver en <i>sag</i> under <i>sagsstyring</i>.</p>
                    <br>
                    <p><i>OBS: </i>vær opmærksom på at en sag skal oprettes før den kan beskrives, se video om <i>Opret ny sag</i></p>
                    <br>
                    <p>Hvis du ønsker mere information se også brugermanualen.</p>
                </div>
                <div class="iframe_container_parent">
                    <div class="iframe_container"><iframe sandbox class="video" src="video_guides/beskriv-sag-hjælpevideomp4.mp4" allowfullscreen></iframe></div>
                </div>
            </div>
    </div>



    <!-- Javascript import -->
    <script src="../javaScript/navbars.js"></script>
</body>
</html>