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

function progressBarTimeCalc() {
    return `${100 - ((audio.currentTime / audio.duration) * 100)}%`
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
            progressFiller.style.right = progressBarTimeCalc();
            setCurrentPlayTime();
        }, 250);
    } else {
        playButtonIcon.classList.remove("playing-active");
        audio.pause();
        clearInterval(playCounter);
    }
}

// Play Button Click Handler

playButtonIcon.addEventListener("click", () => togglePlay(false));

// Pause/Play On Space Bar Or Advance Play Time From Left Or Right Arrows

window.addEventListener("keyup", e => {

    switch(e.code) {

        // Play Pause Space Bar

        case 'Space':
            togglePlay();
            break;
    }
});

window.addEventListener("keydown", e => {

    switch(e.code) {
        
        // Arrow Left Rewind One Second

        case 'ArrowLeft':
            audio.currentTime = audio.currentTime - 1;
            progressBarTimeCalc();
            setCurrentPlayTime();
            break;

        // Arrow Right Advance One Second

        case 'ArrowRight':
            audio.currentTime = audio.currentTime + 1;
            progressBarTimeCalc();
            setCurrentPlayTime();
            break;
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

// Handle Click, Mouse Down, Mouse Move And Mouse Up On Progress Bar

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

function initDescriptionScrollText() {

    // Stop Existing Scrolling Of Description Text

    clearInterval(startDescriptionScroll);

    clearTimeout(descriptionDelayStart);

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

    if (currentEpisodeData !== null && currentEpisodeData !== undefined) {

        // Highlight List Item That Is Set To Be Played

        episodeListItemElements.forEach(element => {
            if (element.dataset.episodeid === currentEpisodeData.guid) {
                element.classList.add("list-item-active");
            } else {
                element.classList.remove("list-item-active");
            }
        });

        // Set Title Text

        episodeSelectedTitle.innerText = currentEpisodeData.title;

        // Set Description Text

        episodeDescription.innerText = typeof currentEpisodeData.description === 'object' ? '' : currentEpisodeData.description;

        episodeDescription.style.left = `0px`;

        audio.src = currentEpisodeData.enclosure['@attributes'].url;

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

            let totalSeconds;

            if (Math.round(totalDurationTime) === 60) {
                totalSeconds = `00`;
                totalMinutes++;
            } else {
                totalSeconds = totalDurationTime < 10 ? `0${Math.round(totalDurationTime)}` : Math.round(totalDurationTime);
            }

            totalMinutes = totalMinutes < 10 ? `0${totalMinutes}` : totalMinutes;

            const hasHours = Number(totalHours) > 0;

            episodeDuration.innerText = hasHours ? `${totalHours}:${totalMinutes}:${totalSeconds}` : `${totalMinutes}:${totalSeconds}`;

            episodeTime.style.width = hasHours ? '7.3ch' : '5ch';

            if (event === 'init') {
                episodeTime.innerText = hasHours ? `00:00:00` : `00:00`;
            }

            episodeDescription.style.left = '0px';

            togglePlay(event === 'init' ? false : true);
            
            initDescriptionScrollText();
        });
    }
}

if (fullPlayer) {

    episodesListItems.forEach(item => {
        item.addEventListener("click", () => {
            setTotalEpisodeTime(item, 'click')
        });
    });

}

setTotalEpisodeTime(null, 'init');

// Advance To Next Episode When Current One Finishes

    audio.addEventListener("ended", () => {
        if (fullPlayer) {
            const nextEpisodeIndex = episodes.findIndex(episode => episode.guid === currentEpisodeData.guid) + 1;
            if ((episodes.length - 1) >= nextEpisodeIndex) {
                setTimeout(() => {
                    setTotalEpisodeTime(episodes[nextEpisodeIndex], 'autoadvance');
                }, 1000)
            }
        } else {
            playButtonIcon.classList.remove("playing-active");
            playing = false;
            audio.currentTime = 0;
        }
    });
