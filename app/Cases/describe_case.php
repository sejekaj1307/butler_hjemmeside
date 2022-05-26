<?php 
    //session start - storage information in session
    session_start(); 

    //connection to database
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    //If the user is trying go around the log in process, redirect the user back to the index.php 
    if($_SESSION['logged_in_user_global']['last_name'] == ""){
        echo "<script> window.location.href = '../index.php'; </script>";
    }
    include("../Data/data.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Beskriv sag</title>
</head>

<body>
    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
        <ul class="navbar_ul">
            <li><a href="../Profile/profile.php">Profil</a></li>
            <li><a href="../Employees/employees.php">Medarbejder</a></li>
            <li><a href="../Calender/machines_calender.php">Kalender</a></li>
            <li><a href="../Cases/cases.php" class="active-main-site">Sager</a></li>
            <li><a href="../Time_registration/time_registration.php">Tidsregistrering</a></li>
            <li><a href="../Tasks/tasks.php">Opgaver</a></li>
            <li><a href="../Storage/storage.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../Data/log_out.php">Log ud</a></div>
    </div>

    <!-- Masthead -->
    <div class="site_container">
        <div class="sec-navbar-mobile">
            <div class="logged_in">
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name'];?> </div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Sager liste<div class="arrow_container"><img src="../img/arrow.png"
                        alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <li><a href="../Cases/cases.php">Sager liste</a></li>
                <li><a href="../Cases/archived_cases.php">Arkiverede sager</a>
                <li><a href="../Cases/describe_case.php" class="active_site_dropdown">Beskriv sag</a>
                </li>
            </ul>
        </div>


    <!-- -----------------------------
                Sager
    ------------------------------ -->
    <form action="describe_case.php" method="post"> <!-- Skal den her være cases? -->
        <?php
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
            function findes($id, $c)
            {
                $sql = $c->prepare("select * from cases where id = ?");
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


        $display_describe_case_pop_up = "none";
        ?>
        <?php
            // CRUD, create, read, update, delete - og confirm og cancel knap til delete
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                //create, køres hvis "Tilføj ny medarbejder" bliver requested
                if($_REQUEST['knap'] == "Opret ny sag")
                {
                    $display_create_case_pop_up = "flex";
                }
                //read, koden køres hvis "read button" bliver requested 
                if(str_contains($_REQUEST['knap'] , "read"))
                {
                    $split = explode("_", $_REQUEST['knap']);
                    $id = $split[1];
                    if(is_numeric($id) && is_numeric(0 + $id))
                    {
                        
                        
                    }
                }
                //update
                if($_REQUEST['knap'] == "Opdater") 
                {
                    $id = $_SESSION["selected_task"];
                    $case_nr = $_REQUEST['case_nr_u'];
                    $case_responsible = $_REQUEST['case_responsible_u'];
                    $status = $_REQUEST['status_u'];
                    $location = $_REQUEST['location_u'];

                    if(is_numeric($id) && is_integer(0 + $id))
                    {
                        if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                        {
                            $sql = $conn->prepare("update cases set case_nr = ?, case_responsible = ?, status = ?, location = ? where id = ?");
                            $sql->bind_param("ssssi", $case_nr, $case_responsible, $status, $location, $id);
                            $sql->execute();    
                        }
                    }
                }
                
                //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                if($_REQUEST['knap'] == "Annuller")
                {
                    $id = "";
                    $case_nr = "";
                    $case_responsible = "";
                    $status = "";
                    $location = "";
                    $est_start_date = "";
                    $est_end_date = "";
                    $display_delete_case_pop_up = "none";
                    $display_create_case_pop_up = "none";
                    $display_edit_case_pop_up = "none";
                }
            }
        ?>


        <div class="case_list_page">
            <div class="describe_case_navbar">
                <button class="describe_case_navbar_active"><a href="describe_case.php">Sagsinfo</a></button>
                <button><a href="pictures.php">Billeder</a></button>
                <button><a href="files.php">Filer</a></button>
            </div>
            <div class="describe_case_info_container">
                <h1>Sagsinfo - sag nr #</h1>
                <div class="input_container">
                    <div class="top_inputs_container">
                        <div class="small_inputs"><p>Kunde :</p><input type="text"></div>
                        <div class="small_inputs"><p>Kundesag nr :</p><input type="text"></div>
                        <div class="small_inputs"><p>Intern sag nr. :</p><input type="text"></div>
                        <div class="small_inputs"><p>Ansvarlig :</p><input type="text"></div>
                        <div class="large_inputs"><p>Tilkørsel - pladsforhold, adgang, tid, støj, mm.</p><textarea type="subject"></textarea></div>
                    </div>
                    <div class="top_inputs_container">
                        <div class="small_inputs"><p>Lokation :</p><input type="text"></div>
                        <div class="small_inputs"><p>Postnummer :</p><input type="text"></div>
                        <div class="small_inputs"><p>Maskiner :</p><input type="text"></div>
                        <div class="small_inputs"><p>Medarbejder :</p><input type="text"></div>
                        <div class="large_inputs"><p>Ekstra arbejde/ventetid</p><textarea type="subject"></textarea></div>
                    </div>
                </div>
                <div class="bottom_inputs_container">
                    <div class="date_and_status">
                        <p>Forventet start d.: 02-02-22</p>
                        <p>Forventet slut d. : 02-02-22</p>
                        <div class="status_container"><p>Status :</p><input type="text" name="" id=""></div>    
                    </div>
                    <div class="job_type_container">
                        <label for="jobtypes">Job typer :</label>
                        <select name="jobtypes" id="jobtypes" class="job_type_select">
                            <option value="CPT">CPT</option>
                            <option value="Miljø">Miljø</option>
                            <option value="GPS_afsætning">GPS afsætning</option>
                        </select>
                    </div>
                </div>
            </div>
        <div>



        <?php 
        //Man skal huske at slukke for forbindelsen. Det er ikke så vigtigt i små programmer, men vi gør det for en god ordens skyld
            $conn->close();
        ?>

        <!----------------------------
                Edit profile pop-op
        ----------------------------->
        <div class="pop_up_modal" style="display: <?php echo $display_describe_case_pop_up ?>">
            Opdater sagen?
            <!-- <h3>Beskriv sag</h3>
            <div class="pop-up-row"><p>Sagssnr. : </p><input type="text" name="case_nr_u" value="<?php echo isset($case_nr) ? $case_nr : '' ?>"></div>
            <div class="pop-up-row">
                <p>Ansvarlig : </p>
                <select name="case_responsible_u">
                    <?php
                        foreach($case_responsible_initials_list as $case_responsible_initials){
                            echo "<option " . ($case_responsible == $case_responsible_initials ? 'selected' : '') . " value=" . $case_responsible_initials . ">" . $case_responsible_initials . "</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="pop-up-row">
                    <p>Status : </p>
                    <select name="status_u">
                        <option <?php echo $status == "Oprettet" ? 'selected' : '' ?> value="Oprettet">Oprettet</option>
                        <option <?php echo $status == "Beskrevet" ? 'selected' : '' ?> value="Beskrevet">Beskrevet</option>
                        <option <?php echo $status == "Aktiv" ? 'selected' : '' ?> value="Aktiv">Aktiv</option>
                        <option <?php echo $status == "Fuldført" ? 'selected' : '' ?> value="Fuldført">Fuldført</option>
                    </select>
                </div>   
            <div class="pop-up-row"><p>Lokation : </p><input type="text" name="location_u" value="<?php echo isset($location) ? $location : '' ?>"></div>
            <div class="pop-up-row"><p>Startdato : </p><input type="text" name="est_start_date_u" value="<?php echo isset($est_start_date) ? $est_start_date : '' ?>"></div>
            <div class="pop-up-row"><p>Deadline : </p><input type="text" name="est_end_date_u" value="<?php echo isset($est_end_date) ? $est_end_date : '' ?>"></div>
            <div class="pop-up-btn-container">
                <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                <input type="submit" name="knap" value="Opdater" class="pop_up_confirm">
            </div>
        </div> -->
        </div>
        
    </form>



    </div>
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>