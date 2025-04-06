async function fetchBackgroundImages() {
    try {
        const response = await fetch('get_backgrounds.php');
        return await response.json();
    } catch (error) {
        console.error('Fout bij ophalen van achtergrondafbeeldingen:', error);
        return [];
    }
}

async function setRandomBackgroundForMobile() {
    const backgroundImages = await fetchBackgroundImages();
    if (window.innerWidth <= 768 && backgroundImages.length) {
        const randomIndex = Math.floor(Math.random() * backgroundImages.length);
        document.body.style.backgroundImage = `url(${backgroundImages[randomIndex]})`;
    }
}

async function changeBackgroundForDesktop() {
    const backgroundImages = await fetchBackgroundImages();
    if (window.innerWidth > 768 && backgroundImages.length) {
        let currentIndex = Math.floor(Math.random() * backgroundImages.length);
        document.body.style.backgroundImage = `url(${backgroundImages[currentIndex]})`;

        setInterval(() => {
            currentIndex = (currentIndex + 1) % backgroundImages.length;
            document.body.style.backgroundImage = `url(${backgroundImages[currentIndex]})`;
        }, 5000);
    }
}
