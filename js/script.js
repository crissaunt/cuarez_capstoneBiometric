

// The rest of your JavaScript code

// const e = require("express");

// Function to fetch registration numbers from the server
async function fetchRegistrationNumbers() {
    try {
        const response = await fetch('../php/list_registration_numbers.php');
        const data = await response.json();
        console.log('Fetched Registration Numbers:', data);
        return data; // Return the array of registration numbers
    } catch (error) {
        console.error('Error fetching registration numbers:', error);
        return []; // Return an empty array on error
    }
}
const cameraModal = document.getElementById('cameraModal');
const startCameraButton = document.getElementById('startCamera');
const videoElement = document.getElementById('video-feed');
const closeCameraModal = document.getElementById('closeCameraModal')
const canvas = document.getElementById('canvas');

closeCameraModal.addEventListener('click', () => {
    cameraModal.style.display = 'none';
  
    // Stop the video stream
    const stream = videoElement.srcObject;
    if (stream) {
      const tracks = stream.getTracks();
      tracks.forEach(track => track.stop());
      videoElement.srcObject = null;
    }else{
        alert('Please retry facial recognition.');
    }
  });

  const stopvideoElement = () => {
    const stream = videoElement.srcObject;
    if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        videoElement.srcObject = null;
    }
};

closeCameraModal.addEventListener('click', stopvideoElement);

  window.addEventListener('click', (event) => {
    if (event.target === cameraModal) {
      cameraModal.style.display = 'none';
  
      // Stop the video stream
      const stream = videoElement.srcObject;
      if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        videoElement.srcObject = null;
      }
    }
  });

const run = async () => {
    // Fetch registration numbers
    images = await fetchRegistrationNumbers();
    if (images.length === 0) {
        console.error('No registration numbers found.');
        return; // Exit if no registration numbers are available
    }


 


    
    function startCamera(){
        cameraModal.style.display = 'block';

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({video: true})
                .then(stream => {
                    
                    videoElement.srcObject = stream;
                    console.log('Camera started successfully');
                })
                .catch(error => {
                    console.log('Error accessing the Camera: ', error);
                    alert('Could not Access the Camera. Please Check the permission');
                });
        } else {
            alert('getUserMedia is not supported in this browser');
        }
    }

    startCameraButton.addEventListener('click', startCamera);

    try {
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri('../models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('../models'),
            faceapi.nets.faceRecognitionNet.loadFromUri('../models'),
            faceapi.nets.ageGenderNet.loadFromUri('../models'),
            faceapi.nets.faceExpressionNet.loadFromUri('../models'),
        ]);

        console.log('Models loaded successfully');
    } catch (error) {
        console.error('Error loading models', error);
    }

 

    // Set canvas size to match video feed size
    videoElement.addEventListener('loadedmetadata', () => {
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        canvas.style.position = 'absolute';
        canvas.style.left = videoElement.offsetLeft + 'px';
        canvas.style.top = videoElement.offsetTop + 'px';
    });

    // Get labeled face descriptions
    labeledDescriptors = await getLabeledFaceDescriptions();
    const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);

    // Facial detection with points
    setInterval(async () => {
        if (!videoElement.videoWidth || !videoElement.videoHeight) {
            console.warn('Video not ready yet. Skipping this frame.');
            return; // Skip processing if video dimensions are invalid
        }
        // Get the video feed and hand it to detectAllFaces method
        let faceAIData = await faceapi
            .detectAllFaces(videoElement)
            .withFaceLandmarks() 
            .withFaceDescriptors()
            .withAgeAndGender()
            .withFaceExpressions();

        // Draw faces on the canvas
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear previous drawings

        // Resize results and draw on canvas
        faceAIData = faceapi.resizeResults(faceAIData, { width: canvas.width, height: canvas.height });
        
        faceapi.draw.drawDetections(canvas, faceAIData);
        faceapi.draw.drawFaceLandmarks(canvas, faceAIData);
        faceapi.draw.drawFaceExpressions(canvas, faceAIData);

        // Draw age and gender
        faceAIData.forEach(face => {
            const { detection, descriptor } = face;
        
            // Find best match
            let bestMatch = faceMatcher.findBestMatch(descriptor);
            let recognizedLabel = bestMatch.label;
            let isRecognized = !recognizedLabel.includes("unknown");
        
            // Draw the detection box with label
            const options = { label: isRecognized ? recognizedLabel : "Unknown Subject" };
            const drawBox = new faceapi.draw.DrawBox(detection.box, options);
            drawBox.draw(canvas);
        
            // Draw age and gender
            const ageText = `${Math.round(face.age)} years`;
            const genderText = `${face.gender} - ${Math.round(face.genderProbability * 100) / 100}`;
            const textField = new faceapi.draw.DrawTextField([genderText, ageText], detection.box.topRight);
            textField.draw(canvas);
        
            if (isRecognized) {
                const userRegistrationNumber = getUserRegistrationNumbers();
                if (!userRegistrationNumber) {
                    alert('Your face was not recognized.');
                    location.reload();
                    return;
                }

                console.log(`Recognized Label: ${recognizedLabel}, Expected: ${userRegistrationNumber}`);
        
                if (recognizedLabel.trim() === userRegistrationNumber.trim()) {
                    // If the recognized face matches the entered registration number
                    sendDataToPHP(userRegistrationNumber);
                } else {
                    alert('Your face was not recognized.');
                    console.error(`Mismatch: Detected ${recognizedLabel}, Expected ${userRegistrationNumber}`);
                    window.location.href = '../php/index.php';
                }
            } else {
                alert('Your face was not recognized.');
                window.location.href = '../php/index.php';
                console.error('Your face is not recognized.');
            }
        });
        

    }, 200); // Consider reducing the interval to 100ms for more responsive updates
};

function getSelectedCandidateId() {
    // Select the radio button with name starting with "position_"
    const selectedCandidateInput = document.querySelector('input[name^="position_"]:checked');
    
    // Check if any candidate is selected and get its value
    const selectedCandidateId = selectedCandidateInput ? selectedCandidateInput.value : null;
    const form = document.getElementById('voteForm');

    console.log('Selected Candidate:', selectedCandidateId);
    
    if (!selectedCandidateId) {
        console.warn('Please select a candidate to proceed.');  
        const errorBox = document.getElementById('error-message'); // Ensure this element exists in your HTML
        errorBox.textContent ='Please select a candidate to proceed.';
        // showErrorMessage('Please select a candidate to proceed.'); // Custom function for non-blocking feedback
      
        return null; // Prevent further processing if no candidate is selected
    }
    
    return selectedCandidateId; // Return the selected candidate ID if valid
}

// function showErrorMessage(message) {
//     const errorBox = document.getElementById('error-message'); // Ensure this element exists in your HTML
//     errorBox.textContent = message;
//     errorBox.style.display = '';
// }

function getUserRegistrationNumbers() {
    const userRegistrationNumberInput = document.getElementById('userRegistrationNumber');
    if (userRegistrationNumberInput) {
        return userRegistrationNumberInput.value;
    } else {
        console.error('userRegistrationNumberInput element not found');
        return null;
    }
}

// JavaScript function to send data to PHP
// function sendDataToPHP(data) {
//     const recognizedRegisteredNumber = data.trim();
//     const candidateId = getSelectedCandidateId();
//     const form = document.getElementById('voteForm');

   
//         if (candidateId) {
//             document.getElementById('candidate_id').value = candidateId;
//             document.getElementById('userRegistrationNumber').value = recognizedRegisteredNumber;  // Update to recognized number
//             form.submit();
        
//         }
    
// }

function sendDataToPHP(data) {
    const recognizedRegisteredNumber = data.trim();
    const candidateId = getSelectedCandidateId();
    const form = document.getElementById('voteForm'); // Retrieve form reference

    if (!form || form.tagName !== 'FORM') {
        console.error('Form with id="voteForm" not found or incorrect element type.');
        return;
    }

    if (candidateId) {
        document.getElementById('candidate_id').value = candidateId;
        document.getElementById('userRegistrationNumber').value = recognizedRegisteredNumber;
        form.submit(); // Safely submit the form
    } else {
        console.warn('Candidate ID not selected.');
    }
}






// Get labeled face descriptions
// Get labeled face descriptions

async function getLabeledFaceDescriptions() {
    const labeledDescriptors = [];

    for (const label of images) {
        const descriptions = [];
        console.log('Processing label:', label); // Log which label is being processed

        for (let i = 1; i <= 2; i++) { // Adjust if you have more than 2 images
            try {
                const img = await faceapi.fetchImage(`../images/${label}/${i}.png`);
                const detections = await faceapi
                    .detectSingleFace(img)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detections) {
                    descriptions.push(detections.descriptor);
                } else {
                    console.log(`No face detected in ${label}/${i}.png`);
                }
            } catch (error) {
                console.error(`Error processing ${label}/${i}.png:`, error);
            }
        }

        if (descriptions.length > 0) {
            labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(label, descriptions));
        }
    }

    return labeledDescriptors;
}










run();
