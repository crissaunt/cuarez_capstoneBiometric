window.addEventListener("load", () => {
    const loader = document.querySelector(".loader");

    setTimeout(() => { // Delays the hiding of the loader
        loader.classList.add("loader-hidden");
    }, 1000); // Extended to 5 seconds

    loader.addEventListener("transitionend", () => {
        document.body.removeChild(loader); // Corrected syntax
    });
});
