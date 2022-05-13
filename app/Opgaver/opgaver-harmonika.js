
function open_close_tasks_service(id, drop_down_classname) {
    let tasks_service_dropdown_mobile = document.querySelectorAll("." + drop_down_classname)
    if (tasks_service_dropdown_mobile[id-1].style.display === "none" || tasks_service_dropdown_mobile[id-1].style.display === "") {
        tasks_service_dropdown_mobile[id-1].style.display = "flex";
    } else {
        tasks_service_dropdown_mobile[id-1].style.display = "none";
    }
}

// mobile_employee_information.addEventListener("click", open_close_employee_info)