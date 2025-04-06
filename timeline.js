function truncateText(text, maxLength) {
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

function showPostBalloon(sourceName, description, link, pubDate, postId, index) {
    const timelineContainer = document.getElementById('timelineContainer');
    const balloon = document.createElement('div');
    balloon.className = 'balloon';
    balloon.setAttribute('data-id', postId);

    const truncatedDescription = truncateText(description, 180);

    // Voeg een kleinere willekeurige marge-top toe aan de ballon
    const randomMarginTop = Math.random() > 0.5 ? 10 : -10;

    // Bepaal de horizontale positie van de ballon
    const leftPosition = index * 260; // 200px breedte + 60px marge

    balloon.innerHTML = `
        <div class="balloon-content">
            <div class="source-name">${sourceName}</div>
            <p>${truncatedDescription}</p>
            <div class="balloon-footer">
                <a href="${link}" target="_blank">Lees meer</a>
                <span class="timestamp">${pubDate}</span>
            </div>
        </div>
        <button class="close-button" onclick="hideBalloon(this)">X</button>
    `;
    balloon.style.marginTop = `${randomMarginTop * (index + 1)}px`;
    balloon.style.left = `${leftPosition}px`;
    timelineContainer.appendChild(balloon);
    setTimeout(() => {
        balloon.style.opacity = '1';
        balloon.style.transform = 'translateY(0)';
    }, 100);
}

function hideBalloon(button) {
    const balloon = button.closest('.balloon');
    balloon.style.opacity = '0';
    balloon.style.transform = 'translateY(-100px)';
    setTimeout(() => balloon.remove(), 500);
}

function displayPosts() {
    posts.forEach((post, index) => {
        const formattedDate = new Date(post.pubDate).toLocaleString('nl-NL', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        setTimeout(() => {
            showPostBalloon(post.sourceName, post.description, post.link, formattedDate, post.id, index);
        }, index * 100);
    });
}