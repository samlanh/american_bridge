
function closeMe()
{
var win = window.open("","_self"); /* url = "" or "about:blank"; target="_self" */
win.close();
}
//for create pupop
function show_popup(id) {
    if (document.getElementById){
        obj = document.getElementById(id);
        if (obj.style.display == "none") {
            obj.style.display = "";
        }
    }
}
function hide_popup(id){
    if (document.getElementById){
        obj = document.getElementById(id);
        if (obj.style.display == ""){
            obj.style.display = "none";
        }
    }
}
