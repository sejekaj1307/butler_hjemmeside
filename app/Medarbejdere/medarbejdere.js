//Media query
let screen_width = window.matchMedia("(max-width: 1200px)")


function open_close_employee_info(id, drop_down_classname) {
    if(screen_width.matches) {
        let employee_dropdown_mobile = document.querySelectorAll("." + drop_down_classname)
        if (employee_dropdown_mobile[id-1].style.display === "none" || employee_dropdown_mobile[id-1].style.display === "") {
            employee_dropdown_mobile[id-1].style.display = "flex";
        } else {
            employee_dropdown_mobile[id-1].style.display = "none";
        }
    }
}

// mobile_employee_information.addEventListener("click", open_close_employee_info)