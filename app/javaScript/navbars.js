/*-------------------------
      Primary navbar
-------------------------*/
let navbar_bars = document.querySelector(".navbar_bars")
let navbar_cross = document.querySelector(".navbar_cross")
let main_navbar_dropown = document.querySelector(".navbar_container")

let main_nav_open = false


/*------------------------------------
     Secondary navbar variables
------------------------------------*/
let sec_navbar_ul_dropdown = document.querySelector(".sec_navbar_ul_dropdown")
let sec_nav_dropdown_arrow = document.querySelector(".sec_nav_dropdown_arrow")
let arrow_container = document.querySelector(".arrow_container") //the container you click to open and close

let sec_navbar_drop_open = false



/*------------------------------------
        open and close functions
------------------------------------*/


//Open af close primary navigation in mobile
let open_close_navbar = function () {
    if (!main_nav_open) {
        main_navbar_dropown.style.display = "block"
        sec_navbar_ul_dropdown.style.display = "none" //closes sec drop down menu and sets it's value to false (closed)
        sec_navbar_drop_open = false
        sec_nav_dropdown_arrow.style.transform = "rotate(90deg)"
        sec_nav_dropdown_arrow.style.display = "none"
    } else if (main_nav_open = true) {
        main_navbar_dropown.style.display = "none"
        sec_nav_dropdown_arrow.style.display = "flex"
    }
    main_nav_open = !main_nav_open
}
navbar_bars.addEventListener("click", open_close_navbar)
navbar_cross.addEventListener("click", open_close_navbar)


//Open and close secondary navbar in mobile version
let open_close_sec_dropdown = function () {
    if (!sec_navbar_drop_open) {
        sec_navbar_ul_dropdown.style.display = "flex"
        sec_nav_dropdown_arrow.style.transform = "rotate(0deg)"
    } else if (sec_navbar_drop_open = true) {
        sec_navbar_ul_dropdown.style.display = "none"
        sec_nav_dropdown_arrow.style.transform = "rotate(90deg)"
    }
    sec_navbar_drop_open = !sec_navbar_drop_open
}
arrow_container.addEventListener("click", open_close_sec_dropdown)
