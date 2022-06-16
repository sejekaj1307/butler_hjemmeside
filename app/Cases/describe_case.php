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

    $this_row_employees_json = json_decode($row['employees'], true);
    $this_row_employee_leader_json = json_decode($row['employee_leader'], true);
    $this_case_machines_json = json_decode($row['machines'], true);
    $this_case_job_types_json = json_decode($row['job_type'], true);
    
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

    //This is for the employee drop down menu
    $employees_initials_list = array();
    $sql = "select initials from employees";
    $result = $conn->query($sql);
                    
    //if og while her 
    if($result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            //employees on case
            if (!array_key_exists($row['initials'], $this_row_employees_json)){
                $this_row_employees_json[$row['initials']] = false;
            }
            //boreformand
            if (!array_key_exists($row['initials'], $this_row_employee_leader_json)){
                $this_row_employee_leader_json[$row['initials']] = false;
            }
            array_push($employees_initials_list, $row["initials"]);
        }
    }

    //This is for the machine drop down menu
    $machine_name_list = array();
    $sql = "select name from machines";
    $result = $conn->query($sql);
                    
    //if og while her 
    if($result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            if (!array_key_exists($row['name'], $this_case_machines_json)){
                $this_case_machines_json[$row['name']] = false;
            }
            array_push($machine_name_list, $row["name"]);
        }
    }
    //This is for the job_type drop down menu
    //case_job_types_list is defined in the data file
    
    for($i=0; $i<count($case_job_types_list); $i++)
    {
        if (!array_key_exists($case_job_types_list[$i], $this_case_job_types_json)){
            $this_case_job_types_json[$case_job_types_list[$i]] = false;
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
    <link rel="stylesheet" href="drop_down_select.css">
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
            <li><a href="../Time_registration/internal_case.php">Tidsregistrering</a></li>
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
                <li><a href="../Cases/cases.php" class="active_site_dropdown">Sager liste</a></li>
                <li><a href="../Cases/archived_cases.php">Arkiverede sager</a>
            </ul>
        </div>


    <!-- -----------------------------
                Describe case CRUD
    ------------------------------ -->
    <form action="describe_case.php?case_nr=<?php echo $case_nr;?>" method="post">
        <?php
            //function to validate id, it returns a true $result if there's $rows in database
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
                //create, pop up
                if($_REQUEST['knap'] == "Opret ny sag")
                {
                    $display_create_case_pop_up = "flex";
                }
                //read
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
                    //employees
                    for($i=0; $i<count($employees_initials_list); $i++){
                        $this_row_employees_json[$employees_initials_list[$i]] = !empty($_REQUEST["employee_checkbox_".$i]) ? true : false;
                    }
                    $new_employee_data = json_encode($this_row_employees_json);

                    //Boreformand
                    for($i=0; $i<count($employees_initials_list); $i++){ 
                        $this_row_employee_leader_json[$employees_initials_list[$i]] = !empty($_REQUEST["employee_leader_checkbox_".$i]) ? true : false;
                    }
                    $new_employee_leader_data = json_encode($this_row_employee_leader_json);

                    //machines
                    for($i=0; $i<count($machine_name_list); $i++){
                        $this_case_machines_json[$machine_name_list[$i]] = !empty($_REQUEST["machine_checkbox_".$i]) ? true : false;
                    }
                    $new_machine_data = json_encode($this_case_machines_json);

                    //job types
                    for($i=0; $i<count($case_job_types_list); $i++){
                        $this_case_job_types_json[$case_job_types_list[$i]] = !empty($_REQUEST["job_type_checkbox_".$i]) ? true : false;
                    }
                    $new_job_type_data = json_encode($this_case_job_types_json);

                    $id = $this_case->get_id();
                    
                    if(is_numeric($id) && is_integer(0 + $id))
                    {
                        if(findes($id, $conn)) //updatea all of the chosen objects elements to database
                        {
                            $sql = $conn->prepare("update cases set client = ?, client_case_nr = ?, case_responsible = ?, location = ?, zip_code = ?, job_type = ?, machines = ?, employees = ?, comment_road_info = ?, comment_extra_work = ?, status = ?, est_start_date = ?, est_end_date = ? where id = ?");
                            $sql->bind_param("sssssssssssssi", 
                                $_REQUEST['client'],
                                $_REQUEST['client_case_nr'],
                                $_REQUEST['case_responsible'],
                                $_REQUEST['location'],
                                $_REQUEST['zip_code'],
                                $new_job_type_data,
                                $new_machine_data,
                                $new_employee_data,
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
                        $_GET['case_nr'],
                        $_REQUEST['case_responsible'],
                        $_REQUEST['location'],
                        $_REQUEST['zip_code'],
                        $new_machine_data,
                        $new_employee_data,
                        $_REQUEST['comment_road_info'],
                        $_REQUEST['comment_extra_work'],
                        $_REQUEST['status'],
                        $_REQUEST['est_start_date'],
                        $_REQUEST['est_end_date']
                    );
                }
                
                //cancel 
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

        <!-----------------------------
            Describe case content
        ------------------------------>
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
                        <div class="small_inputs"><p>Kunde :</p><input autocomplete="off" name="client" maxlength="200" type="text" value="<?php echo $this_case->get_client();?>"></div>
                        <div class="small_inputs"><p>Kundesag nr :</p><input autocomplete="off" name="client_case_nr" maxlength="50" type="text" value="<?php echo $this_case->get_client_case_nr();?>"></div>
                        <div class="small_inputs">
                            <p>Ansvarlig :</p>
                            <!-- <input autocomplete="off" name="case_responsible" type="text" value="<?php echo $this_case->get_case_responsible();?>"> -->
                            <select name="case_responsible">
                                <?php
                                    foreach($case_responsible_initials_list as $case_responsible_initials){ 
                                        echo "<option " . ($this_case->get_case_responsible() == $case_responsible_initials ? 'selected' : '') . " value=" . $case_responsible_initials . ">" . $case_responsible_initials . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div id="list1" class="dropdown-check-list" tabindex="100">
                            <div class="small_inputs tester"><p>Boreforemand :</p>
                                <div class="dropdown list1">
                                    <p class="dropbtn">Boreformand</p>
                                    <div id="myDropdown" class="dropdown-content">
                                        <?php
                                            for($i=0; $i<count($employees_initials_list); $i++){ //employee_leader = boreformand
                                                echo '<li><input name="employee_leader_checkbox_' . $i . '" type="checkbox" ' . ($this_row_employee_leader_json[$employees_initials_list[$i]] ? 'checked' : '') . '/>' . $employees_initials_list[$i] . '</li>';
                                            }
                                        ?> 
                                    </div>
                                </div>
                            </div>  
                        </div>  
                        <div class="large_inputs"><p>Tilkørsel - pladsforhold, adgang, tid, støj, mm.</p><textarea name="comment_road_info" type="subject"><?php echo $this_case->get_comment_road_info();?></textarea></div>
                    </div>
                    <div class="top_inputs_container">
                        <div class="small_inputs"><p>Adresse :</p><input autocomplete="off" name="location" type="text" maxlength="200" value="<?php echo $this_case->get_location();?>"></div>
                        <div class="small_inputs"><p>Postnummer :</p><input autocomplete="off" name="zip_code" type="text" maxlength="50" value="<?php echo $this_case->get_zip_code();?>"></div>
                        <div id="list1" class="dropdown-check-list" tabindex="100">
                            <div class="small_inputs"><p>Maskiner :</p>
                                <div class="dropdown list1">
                                    <p class="dropbtn">Maskiner</p>
                                    <div id="myDropdown" class="dropdown-content">
                                        <?php
                                            for($i=0; $i<count($machine_name_list); $i++){
                                                echo '<li><input name="machine_checkbox_' . $i . '" type="checkbox" ' . ($this_case_machines_json[$machine_name_list[$i]] ? 'checked' : '') . '/>' . $machine_name_list[$i] . '</li>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>  
                        </div>  
                        <div id="list1" class="dropdown-check-list" tabindex="100">
                            <div class="small_inputs tester"><p>Medarbejdere :</p>
                                <div class="dropdown list1">
                                    <p class="dropbtn">Medarbejdere</p>
                                    <div id="myDropdown" class="dropdown-content">
                                        <?php
                                            for($i=0; $i<count($employees_initials_list); $i++){
                                                echo '<li><input name="employee_checkbox_' . $i . '" type="checkbox" ' . ($this_row_employees_json[$employees_initials_list[$i]] ? 'checked' : '') . '/>' . $employees_initials_list[$i] . '</li>';
                                            }
                                        ?> 
                                    </div>
                                </div>
                            </div>  
                        </div>  

                        <label for="combo1" class="combo-label">Multiselect with comma-separated values</label>
                        <div class="combo js-csv">
                            <div role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-owns="listbox1" class="input-wrapper">
                            <input
                                aria-activedescendant=""
                                aria-autocomplete="list"
                                id="combo1"
                                class="combo-input"
                                type="text">
                            </div>
                            <div class="combo-menu" role="listbox" aria-multiselectable="true" id="listbox1"></div>
                        </div>

                        <div class="large_inputs"><p>Ekstra arbejde/ventetid</p><textarea name="comment_extra_work" type="subject"><?php echo $this_case->get_comment_extra_work();?></textarea></div>
                    </div>
                </div>
                <div class="bottom_inputs_container">
                    <div class="date_and_status">
                        <div class="small_inputs"><p>Forventet start d. :</p><input autocomplete="off" name="est_start_date" type="date" value="<?php echo $this_case->get_est_start_date();?>"></div>
                        <div class="small_inputs"><p>Forventet slut d. :</p><input autocomplete="off" name="est_end_date" type="date" value="<?php echo $this_case->get_est_end_date();?>"></div>
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
                        <div id="list1" class="dropdown-check-list" tabindex="100">
                            <div class="small_inputs jobtypes"><p>Jobtyper :</p>
                                <div class="dropdown list1">
                                    <p class="dropbtn">Jobtyper</p>
                                    <div id="myDropdown" class="dropdown-content">
                                        <?php
                                            for($i=0; $i<count($case_job_types_list); $i++){
                                                echo '<li><input name="job_type_checkbox_' . $i . '" type="checkbox" ' . ($this_case_job_types_json[$case_job_types_list[$i]] ? 'checked' : '') . '/>' . $case_job_types_list[$i] . '</li>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        <div>



        <?php 
            //closing connection to database for security reasons
            $conn->close();
        ?>
    </form>



    </div>

    <!-- Javascript import -->
    <script src="../javaScript/open_close_lists_mobile.js"></script>
    <script src="../javaScript/navbars.js"></script>
    <script src="open_close_functions.js"></script>
    <script src="drop_down_select.js"></script>
</body>

</html>