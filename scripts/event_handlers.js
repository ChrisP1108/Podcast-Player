// Size Handling.  Height Of Header And List To Fill Screen Height Accordingly

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

// Set Current Play Time

let playCounter = null;

function setCurrentPlayTime() {
    if (audio.currentTime >= audio.duration) {
        clearInterval(playCounter);
        return;
    }
    let totalCurrentTime = audio.currentTime;
    let hours = Math.floor(totalCurrentTime / 3600);
    hours = hours < 10 ? `0${hours}` : hours;
    if (hours > 59) {
        hours = `00`;
    }
    totalCurrentTime = totalCurrentTime - (Number(hours) * 3600);
    let minutes = Math.floor(totalCurrentTime / 60);
    minutes = minutes < 10 ? `0${minutes}` : minutes;
    if (minutes > 59) {
        minutes = `00`;
        hours = Number(hours) + 1;
    }
    totalCurrentTime = totalCurrentTime - (Number(minutes) * 60);
    let seconds = Math.round(totalCurrentTime);
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

let playing = true;

function togglePlay(starting) {
    if (starting) {
        playing = true;
    } else {
        playing = !playing;
    }
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

playButtonIcon.addEventListener("click", () => togglePlay(false));
window.addEventListener("keyup", e => {
    if (e.code === "Space") {
        togglePlay();
    }
});

// Set Progress Bar

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

// Initialize On Load / Click On Episode List Item Event Handler

let currentEpisodeData;

function setTotalEpisodeTime(item, event) {

    switch(event) {
        case 'init':
            currentEpisodeData = episodes.find(episode => episode.guid === startingEpisodeId);
            break;
        case 'click':
            currentEpisodeData = episodes.find(episode => episode.guid === item.dataset.episodeid);
            break;
        case 'autoadvance':
            currentEpisodeData = item;
            break;
        default:
            currentEpisodeData = null;
    }

    if (currentEpisodeData !== null) {

        // Set Title Text

        episodeSelectedTitle.innerText = currentEpisodeData.title;

        // Set Description Text

        episodeDescription.innerText = currentEpisodeData.description;

        episodeDescription.style.left = `0px`;

        audio.src = currentEpisodeData.enclosure['@attributes'].url;

        clearInterval(startDescriptionScroll);

        clearTimeout(descriptionDelayStart);

        // Set Time Of Episode

        audio.addEventListener("loadedmetadata", () => {

            let totalDurationTime = audio.duration;

            let totalHours = Math.floor(totalDurationTime / 3600);

            if (totalHours > 0) {
                totalDurationTime = totalDurationTime - (totalHours * 3600);
            }

            totalHours = totalHours < 10 ? `0${totalHours}` : totalHours;

            let totalMinutes = Math.floor(totalDurationTime / 60);

            if (totalMinutes > 0) {
                totalDurationTime = totalDurationTime - (totalMinutes * 60);
            }

            totalMinutes = totalMinutes < 10 ? `0${totalMinutes}` : totalMinutes;

            const totalSeconds = totalDurationTime < 10 ? `0${Math.round(totalDurationTime)}` : Math.round(totalDurationTime) ;

            const hasHours = Number(totalHours) > 0;

            episodeDuration.innerText = hasHours ? `${totalHours}:${totalMinutes}:${totalSeconds}` : `${totalMinutes}:${totalSeconds}`;

            episodeTime.style.width = hasHours ? '7ch' : '5ch';

            if (event === 'init') {
                episodeTime.innerText = hasHours ? `00:00:00` : `00:00`;
            }

            // Stop Existing Scrolling Of Description Text

            clearInterval(startDescriptionScroll);

            clearTimeout(descriptionDelayStart);

            togglePlay(event === 'init' ? false : true);
            
            initDescriptionScrollText();
        });
    }
}

episodesListItems.forEach(item => {
    item.addEventListener("click", () => {
        setTotalEpisodeTime(item, 'click')
    });
});

setTotalEpisodeTime(null, 'init');

// Advance To Next Episode When Current One Finishes

audio.addEventListener("ended", () => {
    const nextEpisodeIndex = episodes.findIndex(episode => episode.guid === currentEpisodeData.guid) + 1;
    if ((episodes.length - 1) >= nextEpisodeIndex) {
        setTimeout(() => {
            setTotalEpisodeTime(episodes[nextEpisodeIndex], 'autoadvance');
        }, 1000)
    }
});
