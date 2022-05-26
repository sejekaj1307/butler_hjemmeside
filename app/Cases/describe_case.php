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
    include("../Data/case_describe_data.php");

    //Get case_nr from the URL
    $case_nr = $_GET['case_nr'];
    
    //Fetch data for the selected case
    $sql = $conn->prepare("select * from cases where case_nr = ?");
    $sql->bind_param("s", $case_nr);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    
    $this_case = new case_describe_data(
        $row["id"], 
        $row["client"], 
        $row["client_case_nr"], 
        $row["case_nr"], 
        $row["case_responsible"], 
        $row["location"], 
        $row["zip_code"], 
        $row["machines"], 
        $row["employees"], 
        $row["comment_road_info"], 
        $row["comment_extra_work"], 
        $row["status"],
        $row["est_start_date"],
        $row["est_end_date"]
    );
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
                <li><a href="../Cases/describe_case.php" class="active_site_dropdown">Beskriv sag</a></li>
            </ul>
        </div>


    <!-- -----------------------------
                Sager
    ------------------------------ -->
    <form action="describe_case.php?case_nr=<?php echo $case_nr;?>" method="post"> <!-- Skal den her være cases? -->
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
                    $id = $this_case->get_id();
                    
                    if(is_numeric($id) && is_integer(0 + $id))
                    {
                        if(findes($id, $conn)) //opdaterer alle objektets elementer til databasen
                        {
                            $sql = $conn->prepare("update cases set client = ?, client_case_nr = ?, case_nr = ?, case_responsible = ?, location = ?, zip_code = ?, machines = ?, employees = ?, comment_road_info = ?, comment_extra_work = ?, status = ?, est_start_date = ?, est_end_date = ? where id = ?");
                            $sql->bind_param("sssssssssssssi", 
                                $_REQUEST['client'],
                                $_REQUEST['client_case_nr'],
                                $_REQUEST['case_nr'],
                                $_REQUEST['case_responsible'],
                                $_REQUEST['location'],
                                $_REQUEST['zip_code'],
                                $_REQUEST['machines'],
                                $_REQUEST['employees'],
                                $_REQUEST['comment_road_info'],
                                $_REQUEST['comment_extra_work'],
                                $_REQUEST['status'],
                                $_REQUEST['est_start_date'],
                                $_REQUEST['est_end_date'],
                                $id
                            );
                            $sql->execute();    
                        }
                    }
                    $this_case = new case_describe_data(
                        $id,
                        $_REQUEST['client'],
                        $_REQUEST['client_case_nr'],
                        $_REQUEST['case_nr'],
                        $_REQUEST['case_responsible'],
                        $_REQUEST['location'],
                        $_REQUEST['zip_code'],
                        $_REQUEST['machines'],
                        $_REQUEST['employees'],
                        $_REQUEST['comment_road_info'],
                        $_REQUEST['comment_extra_work'],
                        $_REQUEST['status'],
                        $_REQUEST['est_start_date'],
                        $_REQUEST['est_end_date']
                    );
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
                <div class="button_container">
                    <button class="describe_case_navbar_active"><a href="describe_case.php?case_nr=<?php echo $case_nr;?>">Sagsinfo</a></button>
                    <button><a href="pictures.php?case_nr=<?php echo $case_nr;?>">Billeder</a></button>
                    <button><a href="files.php?case_nr=<?php echo $case_nr;?>">Filer</a></button>
                </div>
                <input type="submit" name="knap" value="Opdater" class="describe_case_update">
            </div>
            <div class="describe_case_info_container">
                <h1>Sagsinfo - sag nr <?php echo $case_nr?></h1>
                <div class="input_container">
                    <div class="top_inputs_container">
                        <div class="small_inputs"><p>Kunde :</p><input name="client" type="text" value="<?php echo $this_case->get_client();?>"></div>
                        <div class="small_inputs"><p>Kundesag nr :</p><input name="client_case_nr" type="text" value="<?php echo $this_case->get_client_case_nr();?>"></div>
                        <div class="small_inputs"><p>Intern sag nr. :</p><input name="case_nr" type="text" value="<?php echo $this_case->get_case_nr();?>"></div>
                        <div class="small_inputs"><p>Ansvarlig :</p><input name="case_responsible" type="text" value="<?php echo $this_case->get_case_responsible();?>"></div>
                        <div class="large_inputs"><p>Tilkørsel - pladsforhold, adgang, tid, støj, mm.</p><textarea name="comment_road_info" type="subject"><?php echo $this_case->get_comment_road_info();?></textarea></div>
                    </div>
                    <div class="top_inputs_container">
                        <div class="small_inputs"><p>Lokation :</p><input name="location" type="text" value="<?php echo $this_case->get_location();?>"></div>
                        <div class="small_inputs"><p>Postnummer :</p><input name="zip_code" type="text" value="<?php echo $this_case->get_zip_code();?>"></div>
                        <div class="small_inputs"><p>Maskiner :</p><input name="machines" type="text" value="<?php echo $this_case->get_machines();?>"></div>
                        <div class="small_inputs"><p>Medarbejder :</p><input name="employees" type="text" value="<?php echo $this_case->get_employees();?>"></div>
                        <div class="large_inputs"><p>Ekstra arbejde/ventetid</p><textarea name="comment_extra_work" type="subject"><?php echo $this_case->get_comment_extra_work();?></textarea></div>
                    </div>
                </div>
                <div class="bottom_inputs_container">
                    <div class="date_and_status">
                        <div class="small_inputs"><p>Forventet start d. :</p><input name="est_start_date" type="date" value="<?php echo $this_case->get_est_start_date();?>"></div>
                        <div class="small_inputs"><p>Forventet slut d. :</p><input name="est_end_date" type="date" value="<?php echo $this_case->get_est_end_date();?>"></div>
                        <div class="status_container">
                            <p>Status :</p>
                            <select name="status">
                                <option <?php echo $this_case->get_status() == "Oprettet" ? 'selected' : '' ?> value="Oprettet">Oprettet</option>
                                <option <?php echo $this_case->get_status() == "Beskrevet" ? 'selected' : '' ?> value="Beskrevet">Beskrevet</option>
                                <option <?php echo $this_case->get_status() == "Aktiv" ? 'selected' : '' ?> value="Aktiv">Aktiv</option>
                                <option <?php echo $this_case->get_status() == "Fuldført" ? 'selected' : '' ?> value="Fuldført">Fuldført</option>
                            </select>
                        </div>    
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
    </form>



    </div>
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
</body>

</html>