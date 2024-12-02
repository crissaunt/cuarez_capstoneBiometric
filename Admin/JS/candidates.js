// Function to validate the form before submission
function validateForm() {
    // Get form values
    const firstName = document.getElementById('addfirst_name').value.trim();
    const lastName = document.getElementById('addlast_name').value.trim();
    const platform = document.getElementById('addplatform').value.trim();
    const program = document.getElementById('program').value.trim();
    const yearLevel = document.getElementById('addyear_level').value.trim();
    const position = document.getElementById('position_id').value;
    const image = document.getElementById('addimage').files[0];

    // Check if all required fields are filled
    if (!firstName || !lastName || !platform || !program || !yearLevel || !position) {
        alert("Please fill in all fields.");
        return false; // Prevent form submission
    }

    // Check if the image is uploaded and has a valid type (JPEG, PNG, GIF)
    if (image) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(image.type)) {
            alert("Please upload a valid image (JPEG, PNG, or GIF).");
            return false; // Prevent form submission
        }
    } else {
        alert("Please upload an image.");
        return false; // Prevent form submission
    }

    return true; // Form can be submitted
}

// Attach the validation function to the form's submit event
document.getElementById('addForm').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault(); // Stop form submission if validation fails
    }
});
