

// The rest of your JavaScript code

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
    const stream = cameraFeed.srcObject;
    if (stream) {
      const tracks = stream.getTracks();
      tracks.forEach(track => track.stop());
      cameraFeed.srcObject = null;
    }else{
        alert('Please retry facial recognition.');
    }
  });

  const stopCameraFeed = () => {
    const stream = videoElement.srcObject;
    if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        videoElement.srcObject = null;
    }
};

closeCameraModal.addEventListener('click', stopCameraFeed);

  window.addEventListener('click', (event) => {
    if (event.target === cameraModal) {
      cameraModal.style.display = 'none';
  
      // Stop the video stream
      const stream = videoElement.srcObject;
      if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        cameraFeed.srcObject = null;
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

            // Find best match and create label
            let label = faceMatcher.findBestMatch(descriptor).toString();
            let isRecognized = !label.includes("unknown");
            let options = { label: isRecognized ? label : " Unknown Subject" };

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
                    console.error('User Registration Number is not defined');
                    return;
                }
                const recognizedRegisteredNumber = label.replace(/\(\d+\.\d+\)$/, '').trim();

                console.log('User Registration Number:', userRegistrationNumber);
                console.log('Recognized Registration Number:', recognizedRegisteredNumber);

                console.log('Comparing:', userRegistrationNumber, recognizedRegisteredNumber);

                const matchNumber = (userRegistrationNumber === recognizedRegisteredNumber) ? userRegistrationNumber : null;

                if (matchNumber) {
                    // Registered Number and ID Number match
                    sendDataToPHP(matchNumber);
                } else {
                    alert('Your face does not match the provided ID number. Please try again.');
                    location.reload();
                }

            } else {
                console.error('User Registration Number is not defined');
            }
        });

    }, 200); // Consider reducing the interval to 100ms for more responsive updates
};

function getSelectedCandidateId() {
    const selectedCandidateInput = document.querySelector('input[name^="position_"]:checked');
    return selectedCandidateInput ? selectedCandidateInput.value : null;
}

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
function sendDataToPHP(data) {
    const recognizedRegisteredNumber = data.trim();
    const candidateId = getSelectedCandidateId();
    // const userRegistrationNumber = getUserRegistrationNumbers();
    const voteForm = document.getElementById('voteForm');

    if (candidateId) {
        document.getElementById('candidate_id').value = candidateId;
        document.getElementById('userRegistrationNumber').value = recognizedRegisteredNumber;  // Update to recognized number
        voteForm.submit();
    }
}

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

document.getElementById('submitVoteBtn').addEventListener('click', (e) => {
    const confirmSubmit = confirm("Are you sure you want to submit your votes?");
    if (!confirmSubmit) e.preventDefault();
});


run();
