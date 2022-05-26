<?php
    session_start();
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    $time_reg_id = array();
    $time_reg_input_types = array();
    $time_reg_input_labels = array();
    $time_reg_job_type = array();
    $sql = "select * from time_reg_fields";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($time_reg_id, $row["id"]);
            array_push($time_reg_input_types, $row["input_type"]);
            array_push($time_reg_input_labels, $row["input_lable"]);
            array_push($time_reg_job_type, $row["job_type"]);
        }
    }
    //TODO: remove this 
    $_SESSION['logged_in_user_global'] = array("initials"=>'BL');

    $my_cases = array();
    $my_case_job_types = array();
    $sql = "select * from cases";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $this_row_employees_json = json_decode($row['employees'], true);
            if($this_row_employees_json[$_SESSION['logged_in_user_global']['initials']]){
                array_push($my_cases, $row["case_nr"]);
                array_push($my_case_job_types, $row["job_types"], true);
            }
        }
    }
    
    $my_case_job_types = array_keys(array_filter(json_decode($my_case_job_types[0], true)));

    $level_1 = "none";
    $level_2 = "none";
    $level_1_selected = 0;

   
    $sql = $conn->prepare("select * from daily_reports where date = ?");
    $myDate = date('d-m-Y');

    $sql->bind_param("s", $myDate);
    $sql->execute();
    $result = $sql->get_result();
    
    $daily_report_data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($daily_report_data, $row["id"] . '_' . $row["time_reg_data"]);
        }
    }
                

    /*__________ TODO __________        
        

        IMPORTANT!!
        7. Gør opmærksom på hvis man prøver at forlade siden uden at have gemt sine ændringer
        Note - Koden til dette er i bunden, men den skal kun komme med en alert hvis der er ugemte ændringer.
        (MAJA) - Update cases table. Employees: varchar(50) -> varchar(100), added collumn "Job_types"
        (MAJA) - Can we change case_nr in cases to be the id?

        DONE
        1. Check hvilke sager medarbejderen er tilknyttet ---> Lav top menu bar efter dette (Intern sag skal altid være der for all).
        2. Check hvilke job typer der er tilknytte til den først valgte sag (default nå man kommer ind på siden) 
            ---> Load alle "time_reg_feilds" basseret på dette (job typer i sag og altid 'generalt' felterne).
        3. Check for data i "daily_reports" for i dags dato og display hvis der er nogen.  
        4. Mulighed for at oprette nye input felter.
        5. Mulighed for at redigere og opdatere.
       
    */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Tidsregistrering</title>
</head>

<body>

    <!-- Navigationsbar -->
    <div class="navbar_container">
        <div class="navbar_top"><img src="../img/navbar-cross.png" alt="navbar cross" class="navbar_cross"></div>
        <div class="navbar_mid"><img src="../img/DayTask_logo.png" alt="DayTask logo" class="day_task_logo"></div>
        <ul class="navbar_ul">
            <li><a href="../Profil/profil.php">Profil</a></li>
            <li><a href="../Medarbejdere/ansatte.php">Medarbejder</a></li>
            <li><a href="../Kalender/kalender-maskiner.php">Kalender</a></li>
            <li><a href="../Sagsstyring/sager.php">Sagsstyring</a></li>
            <li><a href="../Tidsregistrering/tidsregistrering.php" class="active-main-site">Tidsregistrering</a></li>
            <li><a href="../Opgaver/fejl-og-mangler.php">Opgaver</a></li>
            <li><a href="../Lagerstyring/lade.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../index.php">Log ud</a></div>
    </div>

    <div class="site_container">
        <!-- Masthead -->
        <div class="sec-navbar-mobile">
            <div class="logged_in">
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name']; ?></div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Tidsregistrering<div class="arrow_container"><img src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div>
            </h2>
            <ul class="sec_navbar_ul_dropdown">
                <?php
                    for($i=0; $i<count($my_cases); $i++){
                        $selected_case = !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : (!empty($my_cases) ? $my_cases[0] : '');
                        if($my_cases[$i] == $selected_case){
                            echo '<li><a href="tidsregistrering.php?cases_selected=' . $my_cases[$i] . '" class="active_site_dropdown">'. $my_cases[$i] .'</a></li>';
                        }
                        else {
                            echo '<li><a href="tidsregistrering.php?cases_selected=' . $my_cases[$i] . '">'. $my_cases[$i] .'</a></li>';
                        }
                    }
                    if(empty($selected_case)){
                        echo '<li><a href="../Tidsregistrering/intern-sag.php" class="active_site_dropdown">2022 intern sag</a></li>';
                    }
                    else {
                        echo '<li><a href="../Tidsregistrering/intern-sag.php">2022 intern sag</a></li>';
                    }
                ?>
            </ul>
        </div>


        <div class="time_registration">
            <div class="time_registration_navbar">
                <button class="active_time_registration_page">Tidsregistrering</button>
                <button>Forbrug</button>
            </div>
        </div>


    <form action="tidsregistrering.php" method="post">
            <?php 
            //funktion til validering, den returnerer et true $result, hvis der er $rows i databasen
                function findes($id, $c)
                {
                    $sql = $c->prepare("select * from daily_reports where id = ?");
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
                //variables to show or hide pop-up modals
                $display_create_time_reg_field_pop_up = "none";
                $display_delete_time_reg_field_pop_up = "none";
            ?>

            <?php
                // CRUD, create, read, update, delete - og confirm og cancel knap til delete
                if($_SERVER['REQUEST_METHOD'] === 'POST')
                {
                    
                   //read, koden køres hvis "read button" bliver requested 
                    if(str_contains($_REQUEST['knap'] , "read"))
                    {
                        $display_create_time_reg_field_pop_up = "flex";    
                    }
                    //update
                    if($_REQUEST['knap'] == "Opdater") 
                    {
                        $field_to_update = array();
                        for($i=0; $i<count($daily_report_data); $i++){
                            $split = explode("_", $daily_report_data[$i]);
                            if($split[1] != $_REQUEST['time_reg_field_'.$i]){
                                array_push($field_to_update, array("daily_report_id"=>$split[0], "time_reg_data"=>$_REQUEST['time_reg_field_'.$i]));
                            }
                        }
                        foreach($field_to_update as $data){
                            $sql = $conn->prepare("update daily_reports set time_reg_data = ? where id = ?");
                            $sql->bind_param("si", $data['time_reg_data'], $data['daily_report_id']);
                            $sql->execute();  
                        }
                    }
                    //delete
                    if(str_contains($_REQUEST['knap'] , "slet"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[1];
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {
                                $_SESSION["tidsregistreringsFeltTilDelete"] = $id;
                                $display_delete_time_reg_field_pop_up = "flex";
                            }
                        }
                    }
                    //Execute - confirm delete
                    if($_REQUEST['knap'] == "Slet")
                    {
                        $id = $_SESSION["tidsregistreringsFeltTilDelete"];
                        $sql = $conn->prepare("delete from daily_reports where id = ?");
                        $sql->bind_param("i", $id);
                        $sql->execute();
                        $display_delete_time_reg_field_pop_up = "none";
                        
                    }
                    //cancel - samme som clear funktionen, den ryder alle input felterne og knapperne får deres start værdi
                    if($_REQUEST['knap'] == "Annuller")
                    {
                        $display_create_time_reg_field_pop_up = "none";
                        $display_delete_time_reg_field_pop_up = "none";
                    }
                    if(str_contains($_REQUEST['knap'], "level0"))
                    {
                        $level_1 = "flex";
                        $level_2 = "none";
                        $display_create_time_reg_field_pop_up = "flex";
                    }
                    if(str_contains($_REQUEST['knap'],"level1"))
                    {   
                        $split = explode("_", $_REQUEST['knap']);
                        $level_1_selected = $split[1];
                        $level_1 = "none";
                        $level_2 = "flex";
                        $display_create_time_reg_field_pop_up = "flex";
                    }
                    if(str_contains($_REQUEST['knap'],"level2"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $level_2_selected = $split[1];
                        $time_reg_id_c = $time_reg_id[$level_2_selected];
                        $time_reg_data = "";
                        $user_initials = !empty($_SESSION['logged_in_user_global']['initials']) ? $_SESSION['logged_in_user_global']['initials'] : "";
                        $myDate = date('d-m-Y');
                        $myTime = date('H:m:s');
                        $sql = $conn->prepare("insert into daily_reports (time_reg_field_id, time_reg_data, user_initials, date, time) values (?, ?, ?, ?, ?)");
                        $sql->bind_param("issss", $time_reg_id_c, $time_reg_data, $user_initials, $myDate, $myTime);
                        $sql->execute();

                        $level_1 = "none";
                        $level_2 = "none";
                    }
                }
            ?>

            <!-- SELVE TABELLEN -->
            <div class="profile_list">

                <?php
                    $sql = $conn->prepare("select * from daily_reports where date = ?");
                    $myDate = date('d-m-Y');

                    $sql->bind_param("s", $myDate);
                    $sql->execute();
                    $result = $sql->get_result();
                    $test = 0;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = array_search($row["time_reg_field_id"], $time_reg_id);
                            echo '<div class="daily_reports_data_row" >';
                                echo '<label for="fname">' . $time_reg_input_labels[$id] . '</label>';
                                echo '<input id="time_reg_field" name="time_reg_field_' . $test . '" type="' . $time_reg_input_types[$id] . '" value="'. $row["time_reg_data"] . '" class="pop_up_cancel">';
                                echo '<button type="submit" name="knap" value="slet_' . $row['id'] . '">Slet_' . $row['id'] . '</button>';
                            echo '</div>';
                            $test += 1;
                        }
                    }
                ?>
                <button type="submit" name="knap" value="read">Opret nyt tidsregistrerings felt!</button>
                <button type="submit" name="knap" value="Opdater" onclick="myFunction()">Gem ændringer</button>
            </div>

             <!---------------------------
                Add new employee pop-up
            ---------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_create_time_reg_field_pop_up ?>">
                <h3>Tilføj nyt tidsregistrerings felt</h3>
                
                <div class="dropdown">
                    <button type="submit" name="knap" value="level0">Tutorials</button>
                    <div style="display: <?php echo $level_1 ?>">
                         <ul>
                            <?php
                                for ($i = 0; $i < count($my_case_job_types); $i++) {
                                    echo '<li><button type="submit" name="knap" value="level1_' . $i . '">' . $my_case_job_types[$i] . '</button></li>';
                                }
                            ?>
                        </ul>
                    </div>
                    <div style="display: <?php echo $level_2 ?>">
                        <ul>
                            <?php
                                for ($i = 0; $i < count($time_reg_job_type); $i++) {
                                    if($time_reg_job_type[$i] == $my_case_job_types[$level_1_selected]){
                                        echo '<li><button type="submit" name="knap" value="level2_' . $i . '">' . $time_reg_input_labels[$i] . '</button></li>';
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Opret ny" class="pop_up_confirm">
                </div>
            </div>
            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal" style="display: <?php echo $display_delete_time_reg_field_pop_up ?>">
                <h3>Slet tidsregistrerings felt</h3>
                <div class="pop-up-btn-container">
                    <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                    <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                </div>
            </div>


        </form>
    </div>


    <script src="../javaScript/navbars.js"></script>
    <script type="text/JavaScript">
        window.onbeforeunload = ExitPage;
        var test = document.querySelectorAll('[id="time_reg_field"]');
        var opdate_cliecked = false;
        function myFunction(){
            opdate_cliecked = true;
        }
        function ExitPage()
        {
            if(!opdate_cliecked && checkForUnsavedWordk()){
                return "Are you sure you want to exit this page?";
            }
        }
        function checkForUnsavedWordk() {
            var unsavedWork = <?=json_encode($daily_report_data)?> ;
            for(let i = 0; i<test.length; i++){
                if(unsavedWork[i].split("_")[1] != test[i].value){
                    return true;
                }
            }
            return false;
        }
    </script>
</body>

</html>