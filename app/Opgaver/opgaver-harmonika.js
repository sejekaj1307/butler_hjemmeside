
function open_close_tasks_service(id, drop_down_classname) {
    let tasks_service_data_row_all_info = document.querySelectorAll("." + drop_down_classname)

    for(let i = 0; i < tasks_service_data_row_all_info.length; i++){
        if(tasks_service_data_row_all_info[i].id == id){
             if (tasks_service_data_row_all_info[i].style.display === "none" || tasks_service_data_row_all_info[i].style.display === "") {
                tasks_service_data_row_all_info[i].style.display = "flex";
            } else {
                tasks_service_data_row_all_info[i].style.display = "none";
            }
        }
    }
}

// mobile_employee_information.addEventListener("click", open_close_employee_info)