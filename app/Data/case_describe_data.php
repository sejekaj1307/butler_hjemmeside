<?php 
    class Case_describe_data{
        private $id;

        private $client;
        private $client_case_nr;
        
        private $case_nr;
        private $case_responsible;
        
        private $location;
        private $zip_code;

        private $machines;
        private $employees;
                
        private $comment_road_info;
        private $comment_extra_work;

        private $status;
        private $est_start_date;
        private $est_end_date;


        function __construct($id, $client, $client_case_nr, $case_nr, $case_responsible, $location, $zip_code, $machines, $employees, $comment_road_info, $comment_extra_work, $status, $est_start_date, $est_end_date){
            $this->id = $id;
            $this->client = $client; //client er key og $client er value
            $this->client_case_nr = $client_case_nr;
            $this->case_nr = $case_nr;
            $this->case_responsible = $case_responsible;
            $this->location = $location;
            $this->zip_code = $zip_code;
            $this->machines = $machines;
            $this->employees = $employees;
            $this->comment_road_info = $comment_road_info;
            $this->comment_extra_work = $comment_extra_work;
            $this->status = $status;
            $this->est_start_date = $est_start_date;
            $this->est_end_date = $est_end_date;
        }    

        /*----------------------
                Getters
        ----------------------*/
        //getters, det er også en funktion. getters er typisk på en linje. Hvad skal vi returnerer?
        //man kan returnere hvad som helst, den kan også indeholde en masse kode, såsom if sætninger, men typisk er det kun én linje kode.
        //De variabler der ikke har en getter, de kan ikke læses
        //Hvis man vil have fat i variablerne individuelt
        //hvis man har en variable, som ikke skal have en getter, kan man skrive den alligevel og så udkommentere den, så man viser andre programmøre at man bevidst har valgt ikke at skrive den, og at man ikke blot har glemt den        
        public function get_id() { return $this->id; }
        public function get_client() { return $this->client; }
        public function get_client_case_nr() { return $this->client_case_nr; }
        public function get_case_nr() { return $this->case_nr; }
        public function get_case_responsible() { return $this->case_responsible; }
        public function get_location() { return $this->location; }
        public function get_zip_code() { return $this->zip_code; }
        public function get_machines() { return $this->machines; }
        public function get_employees() { return $this->employees; }
        public function get_comment_road_info() { return $this->comment_road_info; }
        public function get_comment_extra_work() { return $this->comment_extra_work; }         
        public function get_status() { return $this->status; }
        public function get_est_start_date() { return $this->est_start_date; }
        public function get_est_end_date() { return $this->est_end_date; }
    

        /*----------------------
                Setters
        ----------------------*/
        //setters, man giver noget en ny værdi. Man skal have getters for at kunne have setters
        //Typisk bruges de til at give noget en ny værdi.
        //Hvis variablerne oppe i toppen var public, behøvede vi ikke at gøre det her
        //Vi sætter den fra private til public
        //$this->id kan ikke ændres direkte.
        // public function setId($id) { $this->id = $id; } //Den her kan ikke ændres?
        public function set_id($id) { $this->id = $id; }
        public function set_client($client) { $this->client = $client; }
        public function set_client_case_nr($client_case_nr) { $this->client_case_nr = $client_case_nr; }
        public function set_case_nr($case_nr) { $this->case_nr = $case_nr; }
        public function set_case_responsible($case_responsible) { $this->case_responsible = $case_responsible; }
        public function set_location($location) { $this->location = $location; }
        public function set_zip_code($zip_code) { $this->zip_code = $zip_code; }
        public function set_machines($machines) { $this->machines = $machines; }
        public function set_employees($employees) { $this->employees = $employees; }
        public function set_comment_road_info($comment_road_info) { $this->comment_road_info = $comment_road_info; }
        public function set_comment_extra_work($comment_extra_work) { $this->comment_extra_work = $comment_extra_work; }
        public function set_status($status) { $this->status = $status; }
        public function set_est_start_date($est_start_date) { $this->est_start_date = $est_start_date; }
        public function set_est_end_date($est_end_date) { $this->est_end_date = $est_end_date; }

    }
?>