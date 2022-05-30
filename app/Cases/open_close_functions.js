

var checkList = document.querySelectorAll(".list1");
for(let i = 0; i<checkList.length; i++){
    checkList[i].getElementsByClassName('dropbtn')[0].onclick = function(evt) {
        let test = checkList[i].getElementsByClassName('dropdown-content')[0];
        
        if (test.style.display == "none" || test.style.display == "")
            test.style.display = "block";
        else
            test.style.display = "none";
    }
}