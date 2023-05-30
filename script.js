// Progress Bar Filler

const progressFiller = document.querySelector("#progress-duration-filler");

// Episode Play Time

const episodeTime = document.querySelector("#current-episode-time");

// Play / Pause Event Handling

const playButtonIcon = document.querySelector('#play-pause-button-icons');

let audio = document.querySelector("#audio-play");

let playing = false;

let progressBarCounter = null;

playButtonIcon.addEventListener('click', () => {
    playing = !playing;
    if (playing) {
        audio.play().then(() => {
            playButtonIcon.classList.add("playing-active");
        });
        progressBarCounter = setInterval(() => {
            progressFiller.style.right = `${100 - ((audio.currentTime / audio.duration) * 100)}%`;
            let totalDurationTime = audio.currentTime;
            let hours = Math.floor(totalDurationTime / 3600);
            hours = hours < 10 ? `0${hours}` : hours;
            totalDurationTime = totalDurationTime - (Number(hours) * 3600);
            let minutes = Math.floor(totalDurationTime / 60);
            minutes = minutes < 10 ? `0${minutes}` : minutes;
            totalDurationTime = totalDurationTime - (Number(minutes) * 60);
            let seconds = Math.round(totalDurationTime);
            seconds = seconds < 10 ? `0${seconds}` : seconds;
            if (hours > 59) {
                hours = `00`;
            }
            if (minutes > 59) {
                minutes = `00`;
                hours = Number(hours) + 1;
            }
            if (seconds > 59) {
                seconds = `00`;
                minutes = Number(minutes) <= 59 ? `00` : Number(minutes) + 1;
            }
            episodeTime.innerText = hours > 0 ? `${hours}:${minutes}:${seconds}` : minutes > 0 ? `${minutes}:${seconds}` : `00:${seconds}`;
        }, 250);
    } else {
        playButtonIcon.classList.remove("playing-active");
        audio.pause();
        clearInterval(progressBarCounter);
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

        }, 22)
    }, 5000)

}

window.addEventListener("load", initDescriptionScrollText);
