async function findFolderByInput() {
    const userInput = document.getElementById('userRegistrationNumber').value.trim(); // Get user input
    if (!userInput) {
        alert('Please enter a registration number');
        return;
    }

    try {
        // Fetch folder names from the server
        const response = await fetch('../php/list_folders.php');
        const folders = await response.json();

        console.log('Fetched Folders:', folders);

        // Check if input matches any folder name
        const matchedFolder = folders.find(folder => folder === userInput);

        if (matchedFolder) {
            alert('Yes, folder found: ' + matchedFolder);
        } else {
            alert('No folder matches the entered registration number.');
        }
    } catch (error) {
        console.error('Error fetching folder names:', error);
        alert('An error occurred while searching for the folder.');
    }
}

// Attach function to a button for testing
document.getElementById('checkFolderButton').addEventListener('click', findFolderByInput);
