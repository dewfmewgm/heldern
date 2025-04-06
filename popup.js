function showPopup() {
    const popup = document.getElementById("seoPopup");
    popup.style.display = "block"; // Toon de popup

    // Laat de popup na 5 seconden verdwijnen
    setTimeout(() => {
        closePopup();
    }, 5000); // 5000 milliseconden = 5 seconden
}

function closePopup() {
    const popup = document.getElementById("seoPopup");
    popup.style.display = "none"; // Verberg de popup
}

// Optioneel: sluit de popup wanneer iemand buiten de popup klikt
window.onclick = function(event) {
    const popup = document.getElementById("seoPopup");
    if (event.target === popup) {
        closePopup(); // Verberg de popup als je buiten klikt
    }
}
