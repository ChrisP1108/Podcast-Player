// Size Handling

// Episode Header

const header = document.querySelector("header");

// Episode List

const episodeListContainer = document.querySelector("#episodes-list");

function setSectionHeights() {
    const main = document.querySelector("main");
    main.style.height = `${window.innerHeight}px`;
    const padding = 16;
    const headerMarginBottom = 16;
    header.style.marginBottom = `${headerMarginBottom}px`;
    header.style.paddingTop = `${padding}px`;
    const remainderHeight = (window.innerHeight - header.clientHeight) - headerMarginBottom;
    episodeListContainer.style.height = `${remainderHeight - padding}px`;
}

setSectionHeights();

window.addEventListener("resize", setSectionHeights);

// Episode Output List

// Episodes Data

const episodes = rssData.channel.item;

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

let startDescriptionScroll;

let descriptionDelayStart;

const episodeDescription = document.querySelector("#player-episode-description");

function initDescriptionScrollText() {
    descriptionDelayStart = setTimeout(() => {
        let descriptionTextOffset = 0;

        const descriptionWidth = episodeDescription.scrollWidth;
        const containerWidth = episodeDescription.parentNode.clientWidth;

        startDescriptionScroll = setInterval(() => {

            descriptionTextOffset--;

            episodeDescription.style.left = `${descriptionTextOffset}px`;

            if ((descriptionTextOffset * -1) >= (descriptionWidth + 24)) {
                descriptionTextOffset = containerWidth + 24;
                episodeDescription.style.left = `${descriptionTextOffset}px`
            }

        }, 28)
    }, 5000);
}

window.addEventListener("load", initDescriptionScrollText);

// Click On Episode List Item Event Handler

const episodesListItems = episodeListContainer.querySelectorAll("li");

const episodeSelectedTitle = document.querySelector("#episode-selected-title");

episodesListItems.forEach(item => {
    item.addEventListener("click", () => {
        const clickedData = episodes.find(episode => episode.guid === item.dataset.episodeid);

        // Stop Existing Scrolling Of Description Text

        clearInterval(startDescriptionScroll);

        clearTimeout(descriptionDelayStart);

        // Set Title Text

        episodeSelectedTitle.innerText = clickedData.title;

        // Set Description Text

        episodeDescription.innerText = clickedData.description;

        episodeDescription.style.left = `0px`;

        console.log(clickedData.enclosure['@attributes'].url);

        audio.src = clickedData.enclosure['@attributes'].url;

        playing = true;

        togglePlay();
        
        initDescriptionScrollText();
    });
});
