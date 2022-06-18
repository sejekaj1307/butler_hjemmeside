
function toggle_password_type(password_input_classname, password_icon_id) {
    //Get password input field by the class name
    let password_input_element = document.getElementsByClassName(password_input_classname)
    let toggle_password_icon_element = document.getElementById(password_icon_id)
    //toggle_password_icon
    //Becacuse we use getElementByClassName we get the hole object. This object can have multiple attributes, but we know it only has one which is "type"
    //Therefor we can access this by getting the first "child" of the object by using "[0]".
    if (password_input_element[0].type === "password") {
        password_input_element[0].type = "text";
        toggle_password_icon_element.src = "../img/toggle_password_type_icon2.png";
    } else {
        password_input_element[0].type = "password";
        toggle_password_icon_element.src = "../img/toggle_password_type_icon.png";
    }
}