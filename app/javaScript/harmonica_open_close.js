
function harmonica_open_close(id, drop_down_classname) {
    let harmonica_data_row_all_info = document.querySelectorAll("." + drop_down_classname)

    for(let i = 0; i < harmonica_data_row_all_info.length; i++){
        if(harmonica_data_row_all_info[i].id == id){
             if (harmonica_data_row_all_info[i].style.display === "none" || harmonica_data_row_all_info[i].style.display === "") {
                harmonica_data_row_all_info[i].style.display = "flex";
            } else {
                harmonica_data_row_all_info[i].style.display = "none";
            }
        }
    }
}
