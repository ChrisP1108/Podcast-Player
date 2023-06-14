// DOM ELEMENTS

// Episode Header

const header = document.querySelector("header");

// Episode List

const episodeListContainer = document.querySelector("#episodes-list");

// Episode List Items

const episodeListItemElements = document.querySelectorAll("#episodes-list li");

// Progress Bar Filler

const progressFiller = document.querySelector("#progress-duration-filler");

// Episode Play Time

const episodeTime = document.querySelector("#current-episode-time");

// Episode Total Time Duration 

const episodeDuration = document.querySelector("#current-episode-duration");

// Play / Pause Button Container

const playButtonIcon = document.querySelector('#play-pause-button-icons');

// Audio Tag

let audio = document.querySelector("#audio-play");

// Progress Bar Handling

const progressBar = document.querySelector("#play-progress-bar");

// Episode List Items

let episodesListItems;

if (fullPlayer) {
    episodesListItems = episodeListContainer.querySelectorAll("li");
}

// Episode Selected Title

const episodeSelectedTitle = document.querySelector("#episode-selected-title");