
let mobile_employee_information = document.getElementsByClassName("mobile_employee_information")
let employee_dropdown_mobile = document.querySelector(".employee_dropdown_mobile")

let employee_info_opened = false

function open_close_employee_info() {
    if (!employee_info_opened) {
        employee_dropdown_mobile.style.display = "flex";
        console.log("Maja")
    } else if (employee_info_opened) {
        employee_dropdown_mobile.style.display = "none";
        console.log("nope")
    }
    employee_info_opened = !employee_info_opened;
}

// mobile_employee_information.addEventListener("click", open_close_employee_info)