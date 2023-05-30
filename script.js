// Episodes Data

const episodes = rssData.channel.item;

console.log(episodes);

// Progress Bar Filler

const progressFiller = document.querySelector("#progress-duration-filler");

// Episode Play Time

const episodeTime = document.querySelector("#current-episode-time");

// Play / Pause Event Handling

const playButtonIcon = document.querySelector('#play-pause-button-icons');

let audio = document.querySelector("#audio-play");

let playing = false;

let playCounter = null;

function setCurrentPlayTime() {
    if (audio.currentTime >= audio.duration) {
        clearInterval(playCounter);
        return;
    }
    let totalDurationTime = audio.currentTime;
    let hours = Math.floor(totalDurationTime / 3600);
    hours = hours < 10 ? `0${hours}` : hours;
    if (hours > 59) {
        hours = `00`;
    }
    totalDurationTime = totalDurationTime - (Number(hours) * 3600);
    let minutes = Math.floor(totalDurationTime / 60);
    minutes = minutes < 10 ? `0${minutes}` : minutes;
    if (minutes > 59) {
        minutes = `00`;
        hours = Number(hours) + 1;
    }
    totalDurationTime = totalDurationTime - (Number(minutes) * 60);
    let seconds = Math.round(totalDurationTime);
    seconds = seconds < 10 ? `0${seconds}` : seconds;
    if (seconds > 59) {
        seconds = `00`;
        minutes++;
        minutes = minutes < 10 ? `0${minutes}` : minutes;
        if (minutes > 59) {
            minutes = `00`;
            hours = Number(hours) + 1;
        }
    }
    hours = Number(hours) < 10 ? `0${Number(hours)}` : hours;
    episodeTime.innerText = audio.duration > 3600 ? `${hours}:${minutes}:${seconds}` : audio.duration > 60 ? `${minutes}:${seconds}` : `00:${seconds}`;
}

// Toggle Play

function togglePlay() {
    playing = !playing;
    if (playing) {
        audio.play().then(() => {
            playButtonIcon.classList.add("playing-active");
        });
        playCounter = setInterval(() => {
            progressFiller.style.right = `${100 - ((audio.currentTime / audio.duration) * 100)}%`;
            setCurrentPlayTime();
        }, 250);
    } else {
        playButtonIcon.classList.remove("playing-active");
        audio.pause();
        clearInterval(playCounter);
    }
}

playButtonIcon.addEventListener("click", togglePlay);
window.addEventListener("keyup", e => {
    if (e.code === "Space") {
        togglePlay();
    }
});

// Progress Bar Handling

const progressBar = document.querySelector("#play-progress-bar");

function setProgressBar(e) {
    const clickPoint = e.offsetX;
    const totalBarWidth = progressBar.clientWidth;
    const barPercentage = clickPoint / totalBarWidth;
    progressFiller.style.right = `${100 - (barPercentage * 100)}%`;
    const playPosition = barPercentage * audio.duration;
    audio.currentTime = playPosition;
    setCurrentPlayTime();
}

progressBar.addEventListener("click", setProgressBar);
progressBar.addEventListener("mousedown", (e) => {
    progressBar.addEventListener("mousemove", setProgressBar, true)
});
window.addEventListener("mouseup", () => {
    progressBar.removeEventListener("mousemove", setProgressBar, true);
});


// Episode Description Text Scroll Across Handling

function initDescriptionScrollText() {

    setTimeout(() => {
        let descriptionTextOffset = 0;

        const episodeDescription = document.querySelector("#player-episode-description");
        const descriptionWidth = episodeDescription.scrollWidth;
        const containerWidth = episodeDescription.parentNode.clientWidth;

        setInterval(() => {

            descriptionTextOffset--;

            episodeDescription.style.left = `${descriptionTextOffset}px`;

            if ((descriptionTextOffset * -1) >= (descriptionWidth + 24)) {
                descriptionTextOffset = containerWidth + 24;
                episodeDescription.style.left = `${descriptionTextOffset}px`
            }

        }, 28)
    }, 5000)

}

window.addEventListener("load", initDescriptionScrollText);
