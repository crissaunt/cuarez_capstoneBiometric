
    function validateForm() {
        // Get form inputs
        const registrationNumber = document.getElementById('registrationNumber').value;
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const gender = document.getElementById('gender').value;
        const program = document.getElementById('program').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // Clear previous error messages
        const errorMessages = document.querySelectorAll('.error');
        errorMessages.forEach(function (msg) {
            msg.remove();
        });

        // Validation checks
        let isValid = true;

        // Check if registration number is empty
        if (!registrationNumber) {
            showError('registrationNumber', 'ID Number is required');
            isValid = false;
        }else if(!/^\d{4}-\d{4}$/.test(registrationNumber)){
            showError('registrationNumber', 'ID Number must be in the format of XXXX-XXXX');
            isValid = false;
        }

        // Check if first name is empty
        if (!firstName) {
            showError('first_name', 'First Name is required');
            isValid = false;
        }

        // Check if last name is empty
        if (!lastName) {
            showError('last_name', 'Last Name is required');
            isValid = false;
        }

        // Check if gender is selected
        if (gender !== 'male' && gender !== 'female') {
            showError('gender', 'Gender is required');
            isValid = false;
        }

        // Check if program is empty
        if (!program) {
            showError('program', 'Program is required');
            isValid = false;
        }

        // Check if email is valid
        if (!email || !validateEmail(email)) {
            showError('email', 'Please enter a valid email address');
            isValid = false;
        }

        // Check if password meets minimum length
        if (!password || password.length < 6) {
            showError('password', 'Password must be at least 6 characters');
            isValid = false;
        }
        const imageInput = document.getElementById('capture-btn2-captured-image-input').value;
        if (!imageInput) {
          alert('Please capture an image before submitting.');
          return false; // Prevent form submission
        }
    
      

        return isValid;
    }

    function showError(inputId, message) {
        const inputField = document.getElementById(inputId);
        const errorMsg = document.createElement('span');
        errorMsg.style.fontSize = '11px';
        errorMsg.className = 'error';
        errorMsg.style.color = 'red';
        errorMsg.textContent = message;
        inputField.parentElement.appendChild(errorMsg);
    }

    function validateEmail(email) {
        // Basic email validation pattern
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return emailPattern.test(email);
    }



