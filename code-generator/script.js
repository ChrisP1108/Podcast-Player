// Url Origin

const podcastPlayerOrigin = 'https://www.partnerwithmagellan.com/podcast-player';

const getRssRoute = 'https://www.partnerwithmagellan.com/wp-json/podcast-player/get-rss-data';

// Data Handling

let rssUrl = null;

let outputUrl = '';

const errHandler = { err: false, msg: null };

let formData = { };


// ELEMENT SELECTORS

// Preview Window Selectors

const previewWindow = document.querySelector("#preview-window");

const previewWindowMessage = document.querySelector("#preview-window-message");

// Loading Spinner Selector

const loadingSpinner = document.querySelector("#loading-spinner");

// Monitor Font Field Typing And Render List Selectors

const fontField = document.querySelector("#font");

const fontListOutput = document.querySelector("#font-list-output");

const submitButton = document.querySelector("#generate-button");

// Output Codes Field Selectors

const codesContainer = document.querySelector("#player-codes-container");

const copyUrlButton = document.querySelector("#direct-url-copy-button");

const copyEmbedButton = document.querySelector("#embed-code-copy-button");


// EVENT HANDLERS

// Embed Code Button Clipboard Handling

function copyCodeToClipboard() {
    if (outputUrl) {
        let outputToClipboard = '';
        outputToClipboard = `<iframe style="width: 100%; min-height: 37.5rem; max-height: 100%;" src="${outputUrl}"></iframe>`;
        navigator.clipboard.writeText(outputToClipboard);
    }
}

copyEmbedButton.addEventListener("click", copyCodeToClipboard);

// Font Dropdown Clearing

function clearFontListDropdown() {
    fontListOutput.classList.remove("show");
    fontListOutput.innerHTML = '';
    submitButton.classList.remove("hide");
    fontFieldActive = false;
}

// Font List Dropdown Handler

let fontFieldActive = false;

fontField.addEventListener("input", () => {
    const fontOutput = fontList.filter(item => item.toLowerCase().startsWith(fontField.value.toLowerCase()));
    if (fontOutput.length > 0 && fontField.value !== '') {
        fontListOutput.classList.add("show");
        fontFieldActive = true;
        submitButton.classList.add("hide");
        fontListOutput.innerHTML = fontOutput.map(font => `<li>${font}</li>`).join("");
        const fontlistRendered = document.querySelectorAll("#font-list-output li");
        fontlistRendered.forEach(item => {
            item.addEventListener("click", () => {
                fontField.value = item.innerText;
                clearFontListDropdown();
            });
        });
    } else {
        clearFontListDropdown()
    }
});  

// Render Podcast Player To Preview

async function rssRender(url) {
    codesContainer.classList.add("remove");
    loadingSpinner.classList.add("show");
    submitButton.classList.add("hide");
    previewWindowMessage.innerText = '';
    const res = await fetch(getRssRoute, {
        method: 'POST',
        headers: {
            'content-type': 'application/json'
        },
        body: JSON.stringify({ url })
    });

    if (res.ok) {
        errHandler.err = false;
        rssUrl = await res.json();
        const fieldsArr = Object.entries(formData).filter(([key, value]) => key !== "url");
        const urlParams = fieldsArr .map(([key, value], index) => {
            const outputText = `${key}=${value}`;
            if (index + 1 === fieldsArr.length) {
                return outputText;
            } else return outputText + "&";
        }).join("");
        outputUrl = `${podcastPlayerOrigin}?url=${rssUrl}&${urlParams}`;
        previewWindow.src = outputUrl;
        previewWindow.addEventListener("load", () => {
            loadingSpinner.classList.remove("show");
            submitButton.classList.remove("hide");
            copyUrlButton.href= outputUrl;
            codesContainer.classList.remove("remove");
        });
    } else {
        loadingSpinner.classList.remove("show");
        codesContainer.classList.add("remove");
        submitButton.classList.remove("hide");
        errHandler.err = true;
        const errMsg = await res.json();
        errHandler.msg = errMsg.msg;
    }

    if (errHandler.err) {
        loadingSpinner.classList.remove("show");
        codesContainer.classList.add("remove");
        submitButton.classList.remove("hide");
        previewWindow.src = "";
        previewWindowMessage.innerText = errHandler.msg;
    }
}

// Form Submit Handler

function submitHandler() {
    const formFields = document.querySelectorAll("form input, form select");
    formFields.forEach(field => {
        const value = field.value[0] === "#" ? field.value.slice(1) : field.value;
        formData[field.name] = value;
    });
    if (!formData.font) {
        formData.font = 'Roboto';
    }
    const { url } = formData;
    if (url && url.includes("/podcasts.apple.com/") && url.includes("/id")) {
        rssRender(url);
    } else {
        codesContainer.classList.add("remove");
        const fieldsErr = "Please enter a valid apple podcast url.";
        previewWindowMessage.innerText = fieldsErr;
        previewWindow.src = '';
    }
}

// Submit Button Handler

document.querySelector("form").addEventListener("submit", e => {
    e.preventDefault();
    if (e.submitter.id === "generate-button") {
        submitHandler();
    }
});

// Key Press Event Handling

window.addEventListener("keyup", e => {
    if (e.key === "Enter") {
        submitHandler();
    }
    if (e.key === "Tab") {
        if (!fontList.some(font => font.toLowerCase() === fontField.value.toLowerCase())) {
            fontField.value = '';
        }
        clearFontListDropdown();
    }
});

// Clear Url Field Button Click

const rssUrlField = document.querySelector("#url");

document.querySelector("#clear-rss-button").addEventListener("click", () => {
    rssUrlField.value = '';
    codesContainer.classList.add("remove");
});

// Window Click Event Handling

window.addEventListener("click", e => {
    if (e.target.id !== 'font') {
        if (!fontList.some(font => font.toLowerCase() === fontField.value.toLowerCase())) {
            fontField.value = '';
        }
        clearFontListDropdown();
    }
});