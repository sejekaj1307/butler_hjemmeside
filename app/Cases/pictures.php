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

    //Get case_nr from the URL
    $case_nr = $_GET['case_nr'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/web/styles.css">
    <title>Billeder</title>
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
                <li><a href="../Cases/cases.php">Sager liste</a></li>
                <li><a href="../Cases/archived_cases.php">Arkiverede sager</a>
                <li><a href="../Cases/describe_case.php" class="active_site_dropdown">Beskriv sag</a>
                </li>
            </ul>
        </div>


    <!-- -----------------------------
                Pictures
    ------------------------------ -->
    <form action="describe_case.php" method="post"> <!-- Skal den her være cases? -->
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

            //variables to show or hide pop-up-modals
            $display_describe_case_pop_up = "none";
        ?>
        <?php
            // CRUD, create, read, update, delete - og confirm og cancel knap til delete
            if($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                //create pop up
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
                    $id = $_SESSION["selected_task"];
                    $case_nr = $_REQUEST['case_nr_u'];
                    $case_responsible = $_REQUEST['case_responsible_u'];
                    $status = $_REQUEST['status_u'];
                    $location = $_REQUEST['location_u'];

                    if(is_numeric($id) && is_integer(0 + $id))
                    {
                        if(findes($id, $conn)) 
                        {
                            $sql = $conn->prepare("update cases set case_nr = ?, case_responsible = ?, status = ?, location = ? where id = ?");
                            $sql->bind_param("ssssi", $case_nr, $case_responsible, $status, $location, $id);
                            $sql->execute();    
                        }
                    }
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

        <!-- ----------------------
                pictures TABLE
        ------------------- ------>
        <div class="case_list_page">
            <div class="describe_case_navbar">
                <div class="button_container">
                    <button><a href="describe_case.php?case_nr=<?php echo $case_nr;?>">Sagsinfo</a></button>
                    <button class="describe_case_navbar_active"><a href="pictures.php?case_nr=<?php echo $case_nr;?>">Billeder</a></button>
                    <button><a href="files.php?case_nr=<?php echo $case_nr;?>">Filer</a></button>
                </div>
                <input type="submit" name="knap" value="Opdater" class="describe_case_update">
            </div>
            <div class="describe_case_info_container">
                <h1>Billeder - sag nr <?php echo $case_nr?></h1>
                <div class="input_container">
            </div>
        <div>



        <?php 
            //closing connection to database for security reasons
            $conn->close();
        ?>

        <!----------------------------
                Edit profile pop-op
        ----------------------------->
        <div class="pop_up_modal" style="display: <?php echo $display_describe_case_pop_up ?>">
            Opdater sagen?
        </div>
        
    </form>



    </div>

    <!-- Javascript import -->
    <script src="../javaScript/navbars.js"></script>
</body>

</html>