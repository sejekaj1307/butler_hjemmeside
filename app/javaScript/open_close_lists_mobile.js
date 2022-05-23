//Media query
let screen_width = window.matchMedia("(max-width: 1200px)")

function open_close_lists_mobile(id, drop_down_classname) {
    if(screen_width.matches) {
        let case_dropdown_mobile = document.querySelectorAll("." + drop_down_classname)
        if (case_dropdown_mobile[id-1].style.display === "none" || case_dropdown_mobile[id-1].style.display === "") {
            case_dropdown_mobile[id-1].style.display = "flex";
        } else {
            case_dropdown_mobile[id-1].style.display = "none";
        }
    }
}
