document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.querySelector(".sidebar");
    const closeBtn = document.querySelector("#btn");
    const searchBtn = document.querySelector(".bx-search");

    if (closeBtn) {
        closeBtn.addEventListener("click", function() {
            sidebar.classList.toggle("open");
            menuBtnChange();
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener("click", function() {
            sidebar.classList.toggle("open");
            menuBtnChange();
        });
    }

    function menuBtnChange() {
        if (sidebar.classList.contains("open")) {
            closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else {
            closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
    }
});


function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this record?")) {
        window.location.href = "voter/delete.php?id=" + id;
        if(window.location.href){
            alert(  'Deleted Succesfully');
        }
    }
}


function deletePosition(id) {
    if (confirm("Are you sure you want to delete this record?")) {
        window.location.href = "position/delete.php?id=" + id;
        if(window.location.href){
            alert(  'Deleted Succesfully');
        }
    }
}

function deleteCandidate(id) {
    if (confirm("Are you sure you want to delete this record?")) {
        window.location.href = "candidate/delete.php?id=" + id;
        if(window.location.href){
            alert(  'Deleted Succesfully');
        }
    }
}


















