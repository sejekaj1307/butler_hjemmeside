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
    //$_SESSION['logged_in_user_global'] = array("initials"=>'BL');

    $my_cases = array();
    $my_case_job_types = array();
    $sql = "select * from cases";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            
            $this_row_employees_json = json_decode($row['employees'], true);      
            if(!empty($this_row_employees_json)){
                if($this_row_employees_json[$_SESSION['logged_in_user_global']['initials']]){
                    array_push($my_cases, $row["case_nr"]);
                    array_push($my_case_job_types, $row["job_type"]);
                }
            }
        }
    }
    if(!empty($_GET['cases_selected'])){
        $my_case_job_types = json_decode($my_case_job_types[array_search($_GET['cases_selected'], $my_cases)], true);
    }
    else {
        $my_case_job_types = json_decode($my_case_job_types[0], true);
    }

    $my_case_job_types["Generel"] = true;
    $my_case_job_types = array_keys(array_filter($my_case_job_types));

    $level_1 = "none";
    $level_2 = "none";
    $level_1_selected = 0;

   
    $sql = $conn->prepare("select * from daily_reports where date = ? AND case_nr = ?");
    $myDate = date('d-m-Y');
    $myTime = date('h:i');
    $this_case_nr = !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : '';
    $sql->bind_param("ss", $myDate, $this_case_nr);
    $sql->execute();
    $result = $sql->get_result();
    
    $daily_report_data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($daily_report_data, $row["id"] . '_' . $row["time_reg_data"]);
        }
    }
    //If it is a new day we need to generate the basic fields for the daily report
    else {
        $general_time_reg_fields_ids = array(14);
        $this_case_nr = !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : '';
        foreach($general_time_reg_fields_ids as $general_time_reg_fields_id){
            $sql = $conn->prepare("insert into daily_reports (time_reg_field_id, time_reg_data, user_initials, date, time, case_nr) values (?, '', ?, ?, ?, ?)");
            $sql->bind_param("issss", $general_time_reg_fields_id, $_SESSION['logged_in_user_global']['initials'], $myDate, $myTime, $this_case_nr);
            $sql->execute();   
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Time_registration</title>
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
            <li><a href="../Cases/cases.php">Sager</a></li>
            <li><a href="../Time_registration/time_registration.php" class="active-main-site">Tidsregistrering</a></li>
            <li><a href="../Tasks/tasks.php">Opgaver</a></li>
            <li><a href="../Storage/storage.php">Lager styring</a></li>
        </ul>
        <div class="log_out_container"><a href="../Data/log_out.php">Log ud</a></div>
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
                        $selected_case = !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : '';
                        if($my_cases[$i] == $selected_case){
                            echo '<li><a href="time_registration.php?cases_selected=' . $my_cases[$i] . '" class="active_site_dropdown">'. $my_cases[$i] .'</a></li>';
                        }
                        else {
                            echo '<li><a href="time_registration.php?cases_selected=' . $my_cases[$i] . '">'. $my_cases[$i] .'</a></li>';
                        }
                    }
                    if(empty($selected_case)){
                        echo '<li><a href="../Time_registration/internal_case.php" class="active_site_dropdown">2022 intern sag</a></li>';
                    }
                    else {
                        echo '<li><a href="../Time_registration/internal_case.php">2022 intern sag</a></li>';
                    }
                ?>
            </ul>
        </div>





    <form action="time_registration.php?cases_selected=<?php echo !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : ''?>" method="post">
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
                        $level_1 = "flex";
                        $level_2 = "none";  
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
                                $sql = $conn->prepare("select * from daily_reports where id = ?");
                                $sql->bind_param("i", $id);
                                $sql->execute();
                                $result = $sql->get_result();
                                
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc(); 
                                    $my_time_reg_id = array_search($row["time_reg_field_id"], $time_reg_id);
                                    $_SESSION["tidsregistreringsFeltTilDeleteText"] = $time_reg_input_labels[$my_time_reg_id];
                                    $_SESSION["tidsregistreringsFeltTilDelete"] = $id;
                                }
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
                        $myTime = date('h:i');
                        $this_case_nr = !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : '';
                        $sql = $conn->prepare("insert into daily_reports (time_reg_field_id, time_reg_data, user_initials, date, time, case_nr) values (?, ?, ?, ?, ?, ?)");
                        $sql->bind_param("isssss", $time_reg_id_c, $time_reg_data, $user_initials, $myDate, $myTime, $this_case_nr);
                        $sql->execute();

                        $level_1 = "none";
                        $level_2 = "none";
                    }
                    if(str_contains($_REQUEST['knap'],"get_time"))
                    {
                        $split = explode("_", $_REQUEST['knap']);
                        $id = $split[2];
                        $myTime = date('h:i');
                        if(is_numeric($id) && is_integer(0 + $id))
                        {
                            if(findes($id, $conn)) //sætter manuelt alle knapper til deres modsatte værdi
                            {   
                                $sql = $conn->prepare("update daily_reports set time_reg_data = ? where id = ?");
                                $sql->bind_param("si", $myTime, $id);
                                $sql->execute();  
                            }
                        }
                    }
                }
            ?>


            <!-- ------------------------------
                    Time registration
            -------------------------------- -->
            <div class="time_registration">
                <div class="time_registration_navbar_container">
                    <div class="time_registration_navbar">
                        <button class="active_time_registration_page?cases_selected=<?php echo !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : ''?>"><a href="#">Tidsregistrering</a></button>
                        <button><a href="time_registration_spending.php?cases_selected=<?php echo !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : ''?>">Forbrug</a></button>
                    </div>
                    <button class="update_button" type="submit" name="knap" value="Opdater" onclick="myFunction()">Gem ændringer</button>
                </div>           

                <div class="time_reg_basics_container">
                <?php
                    $this_case_nr = !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : '';
                    $sql = $conn->prepare("select * from daily_reports where date = ? AND case_nr = ?");
                    $myDate = date('d-m-Y');

                    $sql->bind_param("ss", $myDate, $this_case_nr);
                    $sql->execute();
                    $result = $sql->get_result();
                    $temp_id = 0;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = array_search($row["time_reg_field_id"], $time_reg_id);
                            echo '<div class="daily_reports_data_row" >';
                                echo '<div class="label_and_input_container">';
                                    echo '<p class="row_label" for="fname">' . $time_reg_input_labels[$id] . '</p>';
                                    echo '<div class="input_container">';
                                        echo '<button class="row_input" type="submit" name="knap" value="get_time_' . $row["id"] . '"><p>Tid nu</p></button>';
                                        echo '<input class="row_input_other" id="time_reg_field" name="time_reg_field_' . $temp_id . '" type="' . $time_reg_input_types[$id] . '" value="'. $row["time_reg_data"] . '" class="pop_up_cancel">';
                                    echo '</div>';
                                echo '</div>';
                                echo '<button class="row_delete" type="submit" name="knap" value="slet_' . $row['id'] . '"><img src="../img/trash.png" alt="Employee icon" class="edit_icons"></button>';
                            echo '</div>';
                            $temp_id += 1;
                        }
                    }
                ?>

                <button class="add_new_time_reg" type="submit" name="knap" value="read"><img src="../img/time_reg_cross.png" alt="Tilføj ny linje"></button>
                </div>
            </div>


             <!---------------------------
                Add new time reg field
            ---------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_create_time_reg_field_pop_up ?>">
                <div class="pop_up_modal">
                    <h3>Tilføj nyt tidsregistrerings felt</h3>    
                    <div class="dropdown">
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
            </div>
            <!------------------------
                    delete pop up
            ------------------------->
            <div class="pop_up_modal_container" style="display: <?php echo $display_delete_time_reg_field_pop_up ?>">
                <div class="pop_up_modal" >
                    <h3>Slet tidsregistrerings felt</h3>
                    <p class="pop_up_selected_information"><i>"<?php echo $_SESSION["tidsregistreringsFeltTilDeleteText"];?>"</i></p>
                    <div class="pop-up-btn-container">
                        <input type="submit" name="knap" value="Annuller" class="pop_up_cancel">
                        <input type="submit" name="knap" value="Slet" class="pop_up_confirm">
                    </div>
                </div>
            </div>


        </form>
    </div>


    <script src="../javaScript/navbars.js"></script>

    <!-- This cannot be moved to a .js file because we are looking for a php variable -->
    <script type="text/JavaScript">
        window.onbeforeunload = ExitPage;
        var all_time_reg_fields = document.querySelectorAll('[id="time_reg_field"]');
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
            for(let i = 0; i<all_time_reg_fields.length; i++){
                if(unsavedWork[i].split("_")[1] != all_time_reg_fields[i].value){
                    return true;
                }
            }
            return false;
        }
    </script>
</body>

</html>