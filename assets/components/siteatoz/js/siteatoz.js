function switchid(id) {
    hideallids();
    showdiv(id);
}
function hideallids() {
    //loop through the array and hide each element by id
    for (var i = 0; i < ids.length; i++) {
        hidediv(ids[i]);
    }
}
function hidediv(id) {
    //safe function to hide an element with a specified id
    var el = document.getElementById(id);
    if (el) {
        el.style.display = 'none';
    }
}
function showdiv(id) {
    //safe function to show an element with a specified id
    var el = document.getElementById(id);
    if (el) {
        el.style.display = 'block';
    }

}