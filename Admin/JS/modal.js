// Function to open the Edit modal and pre-fill the form fields with voter data
function openEditModal(voter) {
    document.getElementById('editVoterId').value = voter.id;
    document.getElementById('editFirstName').value = voter.first_name;
    document.getElementById('editLastName').value = voter.last_name;
    document.getElementById('editGender').value = voter.gender;
    document.getElementById('editProgram').value = voter.program;
    document.getElementById('editEmail').value = voter.email;
    document.getElementById('editPassword').value = voter.password;
    document.getElementById('editVoterModal').style.display = 'flex'; // Show the modal

}


document.addEventListener("DOMContentLoaded", function() {
    var addModal = document.getElementById("addPositionModal");
    var editModal = document.getElementById("editVoterModal"); // Edit modal reference

    var btn = document.getElementById("openModal"); // Button to open Add modal
    var closeAddModal = document.getElementsByClassName('close')[0]; // Close button for Add modal
    var closeEditModal = document.getElementsByClassName('close')[1]; // Close button for Edit modal

    // Open the Add modal
    if (btn && addModal) {
        btn.onclick = function() {
            addModal.style.display = "flex";
        };
    }

    // Close the Add modal
    closeAddModal.onclick = function() {
        addModal.style.display = "none";
    };

    // Close the Edit modal
    closeEditModal.onclick = function() {
        editModal.style.display = "none";
    };

    // Close modals if user clicks outside of them
    window.onclick = function(event) {
        if (event.target == addModal) {
            addModal.style.display = "none";
        } else if (event.target == editModal) {
            editModal.style.display = "none";
        }
    };

    // Close modals when the close button is clicked (generic handling for any modal)
    document.querySelectorAll('.close').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').style.display = "none";
        });
    });
});

