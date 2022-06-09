<?php
    //Start session and create connection to datebase
    session_start();
    $conn = new mysqli("localhost:3306", "pass", "pass", "butler_db");

    //Initilize arrays to store my cases and the job types to each case
    $my_cases = array();
    $my_case_job_types = array();

    //Fetch the data for the two arrays from the "cases" table
    $sql = "select * from cases";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $this_row_employees_json = json_decode($row['employees'], true);      
            if(!empty($this_row_employees_json)){
                //Check if the logged in user initials is assosiated with true or false in the table.
                if($this_row_employees_json[$_SESSION['logged_in_user_global']['initials']]){
                    array_push($my_cases, $row["case_nr"]);
                    array_push($my_case_job_types, $row["job_type"]);
                }
            }
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
    <title>2022 intern sag</title>
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
                <div><img src="../img/person-login.png" alt="Employee icon" class="employee_icon"> <?php echo $_SESSION['logged_in_user_global']['last_name'] . ', ' . $_SESSION['logged_in_user_global']['first_name'];?> </div>
                <div class="navbar_bars"></div>
            </div>
            <h2 class="sec-navbar-mobile-header">Tidsregistrering<div class="arrow_container"><img src="../img/arrow.png" alt="arrow" class="sec_nav_dropdown_arrow"></div></h2>
            <ul class="sec_navbar_ul_dropdown">
                <?php
                    //Loop through my_cases array and create a tab for each. Highlight the selected case that is currently displaying
                    for($i=0; $i<count($my_cases); $i++){
                        $selected_case = !empty($_GET['cases_selected']) ? $_GET['cases_selected'] : '';
                        if($my_cases[$i] == $selected_case){
                            echo '<li><a href="time_registration.php?cases_selected=' . $my_cases[$i] . '" class="active_site_dropdown">'. $my_cases[$i] .'</a></li>';
                        }
                        else {
                            echo '<li><a href="time_registration.php?cases_selected=' . $my_cases[$i] . '">'. $my_cases[$i] .'</a></li>';
                        }
                    }
                    //No matter what cases the user is associated with, they should always see the internal_case tab. If it is the only one, select it
                    if(empty($selected_case)){
                        echo '<li><a href="../Time_registration/internal_case.php" class="active_site_dropdown">2022 intern sag</a></li>';
                    }
                    else {
                        echo '<li><a href="../Time_registration/internal_case.php">2022 intern sag</a></li>';
                    }
                ?>
            </ul>
        </div>





    <form action="internal_case.php" method="post">
            <?php 
            //Function to validate if the id exsists in the "daily_reports" table. 
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
                //Variables to show or hide pop-up modals
                $display_create_time_reg_field_pop_up = "none";
                $display_delete_time_reg_field_pop_up = "none";
            ?>



            <!-- ------------------------------
                    Time registration
            -------------------------------- -->
            <div class="time_registration">
                <div class="time_registration_navbar_container">
                    <div class="time_registration_navbar">
                        <button><a href="internal_case.php">2022 intern</a></button>
                        <button class="active_time_registration_page"><a href="#">Forbrug</a></button>
                    </div>
                    <button class="update_button" type="submit" name="knap" value="Opdater" onclick="myFunction()">Gem ændringer</button>
                </div>           

                <div class="time_reg_basics_container">


                <button class="add_new_time_reg" type="submit" name="knap" value="read"><img src="../img/time_reg_cross.png" alt="Tilføj ny linje"></button>
                </div>
            </div>


            <!---------------------------
                Add new time reg field
            ---------------------------->
            <!--    IN DEVELOPMENT    -->

            <!------------------------
                    delete pop up
            ------------------------->
            <!--   IN DEVELOPMENT  -->

        </form>
    </div>


    <script src="../javaScript/navbars.js"></script>

</body>

</html>