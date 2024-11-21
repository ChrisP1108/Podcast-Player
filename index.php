<?php require 'data_params/data_params.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if (!$error_loading_rss): ?>

        <!-- Podcast SEO Data START -->

        <meta name="Podcast" content="<?php echo $channel->title; ?>">
        <meta name="<?php echo $channel->title; ?> Podcast" content="<?php echo $channel->description; ?>">
        <?php foreach($episodes as $episode): ?>
            
            <meta name="<?php echo $episode->title; ?>" content="<?php echo $episode->description; ?>">

        <?php endforeach; ?>

        <!-- Podcast SEO Data END -->

    <?php endif; ?>

    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/jpeg" href="<?php echo $podcast_image; ?>">
    <title><?php echo $parsed_rss_feed->channel->title; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=<?php echo $style_font; ?>:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        * {
            color: <?php echo $style_text_color; ?>;
            font-family: '<?php echo $style_font; ?>' , sans-serif;
        }
        body::before {
            background-image: url(<?php echo $podcast_image; ?>);
        }
        body::after {
            opacity: <?php echo $style_mode === '#fff' ? '0.88' : '0.8'; ?>;
        }
        :root {
            --transition: 0.25s;
            --theme: <?php echo $style_mode; ?>;
            --color1: <?php echo $style_color_1; ?>;
            --progressBarColor: <?php echo $style_progress_bar_color; ?>;
            --highlightcolor: <?php echo $style_highlight; ?>;
            --scrollbarcolor : <?php echo $style_scrollbar; ?>; 
        }
        .play-icon *, .pause-icon * {
            fill: <?php echo $style_play_button; ?> !important;
        }
    </style>
</head>
<body>

    <!-- Play Icon SVG Symbol START   -->

    <svg style="display: none">
        <symbol id="play-icon-symbol">    
        <defs>                        
            <clipPath id="clip-path" transform="translate(-264.41 -245.59)">
                <rect class="cls-1" x="264.41" y="245.59" width="145.2" height="145.2"></rect>
            </clipPath>
            <clipPath id="clip-path-3" transform="translate(-264.41 -245.59)">
                <rect class="cls-1" x="255.41" y="238.59" width="163.2" height="153.2"></rect>
            </clipPath>
        </defs>
        <path d="M378.93,318.19,311,357.4V279Zm30.68,0a72.6,72.6,0,1,0-72.6,72.6,72.6,72.6,0,0,0,72.6-72.6" transform="translate(-264.41 -245.59)"></path>
    </symbol> 
    </svg>

    <!-- Play Icon SVG Symbol END   -->

    <?php if ($error_loading_rss): ?>

        <div class="error-msg">
            <h1>Error Loading Podcast</h1>
            <h4><?php echo $err_msg; ?></h4>
    </div>

    <?php else: ?>

        <main>
            <header>
                <a href="<?php echo $channel->link?>" target="_blank" rel="nofollow">
                    <img src="<?php echo $podcast_image; ?>" alt="<?php echo $channel->title?>">
                </a>
                <div class="player-control-container">
                    <div class="player-control-title-links">
                        <h3><?php echo $channel->title?></h3>
                    </div>
                    <div class="play-episode-container">
                        <div id="play-pause-button-icons">
                            <!-- Play Icon SVG Code START -->

                            <svg class="play-icon" viewBox="0 0 145.2 145.2">
                                <use href="#play-icon-symbol"></use>
                            </svg>

                            <!-- Play Icon SVG Code END -->

                            <!-- Pause Icon SVG Code START -->

                            <svg class="pause-icon" viewBox="0 0 145.2 145.2">
                                <path class="pbi-1" d="M-132,798.77a72.61,72.61,0,0,1-72.6,72.6,72.6,72.6,0,0,1-72.6-72.6,72.59,72.59,0,0,1,72.6-72.6A72.6,72.6,0,0,1-132,798.77Zm-84-38.3h-19.41v75.79H-216Zm41.3,0h-19.41v75.79h19.41Z" transform="translate(277.21 -726.17)"/>
                            </svg>

                            <!-- Pause Icon SVG Code END -->

                            <!-- Audio Play -->
                            <audio id="audio-play" src="<?php echo $audio_data->url ?>">
                        </div>
                        <div class="play-title-time-text">
                            <h5 id="episode-selected-title">
                                <?php echo $episode_selected->title ?>
                            </h5>
                            <div id="episode-selected-time">
                                <h5 id="current-episode-time"></h5>
                                <h5> / </h5>
                                <h5 id="current-episode-duration"></h5>
                            </div>
                        </div>
                    </div>
                    <div class="player-episode-description-container">
                        <h6 id="player-episode-description">
                            <?php echo $episode_selected->description; ?>
                        </h6>
                    </div>
                    <div id="play-progress-bar">
                        <div id="progress-duration-filler" style="right: 100%;"></div>
                    </div>
                </div>
            </header>
            <?php if ($single_episode === 'false'): ?>
                <ol id="episodes-list">
                    <?php foreach($episodes as $episode): ?>
                    
                        <li data-episodeid="<?php echo $episode->guid; ?>">
                            <div class="episode-list-image-play">
                                <img src="<?php echo $podcast_image; ?>" alt="<?php echo $episode->title; ?>">
                                <svg class="list-item-play-icon" viewBox="0 0 145.2 145.2">
                                    <use href="#play-icon-symbol"></use>
                                </svg>
                            </div>
                            <div class="episode-list-title-description">
                                <h5><?php echo $episode->title; ?></h5>
                                <?php echo !strlen(trim($episode->description)) ? '' : '<p>'. $episode->description . '</p>'; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </main>
        <script>
            const startingEpisodeId = "<?php echo $starting_episode_id; ?>";
            const rssData = <?php echo $rss_data; ?>;
            const fullPlayer = "<?php echo $single_episode; ?>" === "false";
        </script>
        <script src="scripts/rss_data.js"></script>
        <script src="scripts/element_selectors.js"></script>
        <script src="scripts/event_handlers.js"></script>
    <?php endif; ?>
</body>
</html>
