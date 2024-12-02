
async function openCamera(buttonId) {
  try {
    // Access the webcam
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const videoElement = document.getElementById(buttonId + '-video');
    
    videoElement.style.display = 'block'; // Make video visible
    videoElement.srcObject = stream;

    // Create a capture button dynamically
    const captureButton = document.createElement('button');
    captureButton.textContent = 'Capture Image';
    captureButton.style.marginTop = '10px';
    videoElement.parentNode.appendChild(captureButton);

    captureButton.onclick = async () => {
      const capturedImageDataUrl = captureImage(videoElement);

      // Stop the video stream
      stream.getTracks().forEach(track => track.stop());
      videoElement.style.display = 'none';
      videoElement.parentNode.removeChild(captureButton);

      // Display the captured image
      const imgElement = document.getElementById(buttonId + '-captured-image');
      imgElement.src = capturedImageDataUrl;
      imgElement.style.display = 'block';

      // Perform face detection on the captured image
      await detectFace(capturedImageDataUrl, buttonId);

      // Update the hidden input with the captured image data
      const hiddenInput = document.getElementById(buttonId + '-captured-image-input');
      hiddenInput.value = capturedImageDataUrl;
    };
  } catch (error) {
    console.error('Error accessing webcam:', error);
    alert('Unable to access the webcam. Please check your permissions.');
  }
}

function captureImage(video) {
  const canvas = document.createElement('canvas');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const context = canvas.getContext('2d');
  context.drawImage(video, 0, 0, canvas.width, canvas.height);
  return canvas.toDataURL('image/png');
}

// Load face-api.js models
async function loadModels() {
  const MODEL_URL = '/models'; // Ensure models are placed in this folder
  await faceapi.loadSsdMobilenetv1Model(MODEL_URL);
  await faceapi.loadFaceLandmarkModel(MODEL_URL);
  await faceapi.loadFaceRecognitionModel(MODEL_URL);
}

// Detect a face from the captured image
async function detectFace(imageDataUrl, buttonId) {
  const img = new Image();
  img.src = imageDataUrl;

  // Wait for the image to load before processing
  img.onload = async () => {
    const canvas = document.createElement('canvas');
    canvas.width = img.width;
    canvas.height = img.height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0);

    const detections = await faceapi
      .detectSingleFace(canvas)
      .withFaceLandmarks()
      .withFaceDescriptor();

    if (detections) {
      console.log('Face detected:', detections);
      alert('Face detected!');
    } else {
      console.log('No face detected.');
      alert('No face detected. Please try again.');
    }
  };
}

// Call this to ensure models are loaded when the page is ready
window.onload = loadModels;

