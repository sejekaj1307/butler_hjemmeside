
function open_close_tasks_service(id, drop_down_classname) {
    let tasks_service_dropdown_mobile = document.querySelectorAll("." + drop_down_classname)

    for(let i = 0; i < tasks_service_dropdown_mobile.length; i++){
        if(tasks_service_dropdown_mobile[i].id == id){
             if (tasks_service_dropdown_mobile[i].style.display === "none" || tasks_service_dropdown_mobile[i].style.display === "") {
                tasks_service_dropdown_mobile[i].style.display = "flex";
            } else {
                tasks_service_dropdown_mobile[i].style.display = "none";
            }
        }
    }
}

// mobile_employee_information.addEventListener("click", open_close_employee_info)