<?php 
    include 'fonts/fonts.php';
?>

<!DOCTYPE html>
<html lang="en" class="pseudo">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Podcast Player Code Generator</title>
</head>
<body class="pseudo">
    <h1>Podcast Player Code Generator / Downloader</h1>
    <div class="content pseudo">
        <div class="fields-preview-row">
            <main class="column pseudo">
                <h3>Podcast Feed / Styling</h3>
                <form class="field-sections frame-shadow pseudo"> 
                    <section class="podcast-url-section">
                        <label for="url">Apple Podcast Or Direct RSS Url</label>
                        <input name="url" id="url" type="text">
                        <button id="clear-rss-button" class="clear-url-button pseudo">Clear Url Field</button>
                    </section>
                    <section class="font-field-section">
                        <label for="font">Font Family <br>(Google Fonts)</label>
                        <div class="font-field-container">
                            <input name="font" id="font" type="text">
                            <ol id="font-list-output"></ol>
                        </div>
                    </section>
                    <section>
                        <label for="mode">Background Color</label>
                        <input name="mode" id="mode" type="color" value="#262626">
                    </section>
                    <section>
                        <label for="color1">Theme Color</label>
                        <input name="color1" id="color1" type="color" value="#E3530F">
                    </section>
                    <section>
                        <label for="textcolor">Text Color</label>
                        <input name="textcolor" id="textcolor" type="color" value="#FFFFFF">
                    </section>
                    <section>
                        <label for="progressbarcolor">Progress Bar Color</label>
                        <input name="progressbarcolor" id="progressbarcolor" type="color" value="#333333">
                    </section>
                    <section>
                        <label for="buttoncolor">Play/Pause Button Color</label>
                        <input name="buttoncolor" id="buttoncolor" type="color" value="#FFFFFF">
                    </section>
                    <section>
                        <label for="highlightcolor">Selected Episode Highlight Color</label>
                        <input name="highlightcolor" id="highlightcolor" type="color" value="#7A7A7A">
                    </section>
                    <section>
                        <label for="scrollcolor">Scroll Bar Thumb Color</label>
                        <input name="scrollcolor" id="scrollcolor" type="color" value="#E3530F">
                    </section>
                    <button type="submit" id="generate-button" class="pseudo">
                        Generate Podcast Player
                    </button>
                </form> 
            </main>
            <aside class="column">
                <h3>Preview</h3>
                <div class="preview-window-container pseudo">
                    <h3 id="preview-window-message">Podcast player will output here once fields are filled and the "generate podcast player" button is clicked.</h3>
                    <iframe id="preview-window" class="frame-shadow" src="" frameborder="0">Enter</iframe>
                    <div id="loading-spinner">
                        <svg version="1.1" id="L7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                            <path fill="#fff" d="M31.6,3.5C5.9,13.6-6.6,42.7,3.5,68.4c10.1,25.7,39.2,38.3,64.9,28.1l-3.1-7.9c-21.3,8.4-45.4-2-53.8-23.3
                            c-8.4-21.3,2-45.4,23.3-53.8L31.6,3.5z">
                                <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
                            </path>
                            <path fill="#fff" d="M42.3,39.6c5.7-4.3,13.9-3.1,18.1,2.7c4.3,5.7,3.1,13.9-2.7,18.1l4.1,5.5c8.8-6.5,10.6-19,4.1-27.7
                            c-6.5-8.8-19-10.6-27.7-4.1L42.3,39.6z">
                                <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="-360 50 50" repeatCount="indefinite"></animateTransform>
                            </path>
                            <path fill="#fff" d="M82,35.7C74.1,18,53.4,10.1,35.7,18S10.1,46.6,18,64.3l7.6-3.4c-6-13.5,0-29.3,13.5-35.3s29.3,0,35.3,13.5
                            L82,35.7z">
                                <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
                            </path>
                        </svg>
                    </div>
                </div>
            </aside>
        </div>
        <div id="player-codes-container" class="remove">
            <h3>Podcast Player Codes</h3>
            <div class="field-sections frame-shadow pseudo output-codes">
                <section>
                    <h6>Direct Url</h6>
                    <a id="direct-url-copy-button" class="pseudo" target="_blank">Open Direct Url</a>
                </section>
                <section>
                    <h6>HTML Embed Code</h6>
                    <button id="embed-code-copy-button" class="pseudo">Copy Embed Code</button>
                </section>
            </div>
        </div>
        <div id="download-container" class="remove">
            <h3>Download Podcast</h3>
            <div class="field-sections frame-shadow pseudo output-codes">
                <section>
                    <h6>MP3 Files</h6>
                    <button id="download-button" class="pseudo">Download All Podcast Episodes</button>
                </section>
                <section id="download-items-generated" class="remove"></section>
            </div>
        </div>
    </div>
    <script>
        const fontList = <?php echo json_encode($fonts_list); ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
