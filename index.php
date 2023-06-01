<?php 

    // Get Url Parameters

    $url_origin = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url_origin);

    parse_str($url_components['query'], $params);

    // Url Parameters

    $rss_url = $params['url'];

    $style_color_1 = $params['color1'] ?? null;
    $style_color_2 = $params['color2'] ?? null;
    $style_color_2 = $params['color3'] ?? null;
    $style_theme = $params['theme'] ?? '#000';
    $style_play_button = $params['buttoncolor'] ?? null;

    if (!$style_play_button) {
        if ($style_theme === '#000' || $style_theme === 'dark') {
            $style_play_button = '#fff';
        } else {
            $style_play_button = '#000';
        }
    }

    $track_selected = intval($params['track']) - 1 ?? 0;

    // Variables For Error Handling

    $error_loading_rss = false;

    $err_msg = null;

    // Check That RSS Url Parameter Exists. If Not, Show Error Msg

    if (!$rss_url) {
        $err_msg = 'An RSS url parameter must be provided in order to retrieve a podcast.';
    }

    // Get RSS Feed Data

    $rss_feed = @file_get_contents($rss_url);

    // Check For Error.  If No Error, Parse RSS Feed Data

    if (!$rss_feed) {
        $error_loading_rss = true;
    } else {
        $parsed_rss_feed = @simplexml_load_string($rss_feed);

        // Check That RSS Was Parsed Correctly.  If Not, Throw Error

        if (!$parsed_rss_feed) {
            $error_loading_rss = true;
        } else {

            if (!$parsed_rss_feed->channel) {
                $error_loading_rss = true;
            } else {

                $parsed_rss_feed->channel->rssUrl = $rss_url;

                $parsed_rss_feed->channel->description = strip_tags($parsed_rss_feed->channel->description);

                foreach($parsed_rss_feed->channel->item as $index => $item) {
                    $description_text = strip_tags($item->description);
                    $item->description = $description_text;
                }

                $channel = $parsed_rss_feed->channel;

                $episodes = $parsed_rss_feed->channel->item;

                // Episode Selected

                $episode_selected = $track_selected ? $episodes[$track_selected] : $episodes[0];

                $starting_episode_id = $episode_selected->guid;

                $rss_data = json_encode($parsed_rss_feed);

                echo '<script>console.log('.$rss_data.');</script>';
            }
        }
    }

    if ($error_loading_rss) {
        if (!$err_msg) {
            $err_msg = 'The RSS url parameter provided was not able to retrieve a podcast RSS feed. Check the RSS url parameter.';
        }
    } else {
        $audio_data = $episode_selected->enclosure->attributes() ? $episode_selected->enclosure->attributes() : null;
    }

    $podcast_image = !$error_loading_rss ? $parsed_rss_feed->channel->image->url : 'will-francis-ZDNyhmgkZlQ-unsplash.jpg';

    // Sample Url - url=https://feeds.soundcloud.com/users/soundcloud:users:287325177/sounds.rss
?>

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
    <title>Podcast Player</title>
    <style>
        * {
            color: <?php echo $style_theme === 'light' ? '#000' : '#fff'; ?>;
            font-family: 'Poppins', sans-serif;
        }
        body::before {
            background-image: url(<?php echo $podcast_image; ?>);
        }
        :root {
            --transition: 0.25s;
            --color1: #CAB653;
            --color2: #CAB653;
            --color3: #666666;
            --theme: <?php echo $style_theme === 'light' ? '#fff' : '#000'; ?>
        }
        .play-icon path, .pause-icon path {
            fill: <?php echo $style_play_button; ?> !important;
        }
    </style>
</head>
<body>
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

                            <svg class="play-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1" data-name="Layer 1" viewBox="0 0 145.2 145.2"><defs>
                                <style>
                                    <!-- .cls-1 { fill: none; }       -->
                                    .cls-2 { clip-path: url(#clip-path); }      
                                    .cls-3 { opacity: 1; }      
                                    .cls-4 { clip-path: url(#clip-path-3); }         
                                </style>
                                <clipPath id="clip-path" transform="translate(-264.41 -245.59)">
                                    <rect class="cls-1" x="264.41" y="245.59" width="145.2" height="145.2"></rect>
                                </clipPath>
                                <clipPath id="clip-path-3" transform="translate(-264.41 -245.59)">
                                    <rect class="cls-1" x="255.41" y="238.59" width="163.2" height="153.2"></rect>
                                </clipPath></defs>
                                <g class="cls-2">
                                <g class="cls-2">
                                <g class="cls-3">
                                <g class="cls-4">
                                <path style="fill: #fff" class="cls-5" d="M378.93,318.19,311,357.4V279Zm30.68,0a72.6,72.6,0,1,0-72.6,72.6,72.6,72.6,0,0,0,72.6-72.6" transform="translate(-264.41 -245.59)"></path>
                                </g></g></g></g>
                            </svg>

                            <!-- Play Icon SVG Code END -->

                            <!-- Pause Icon SVG Code START -->

                            <svg class="pause-icon" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 145.2 145.2">
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
            <ol id="episodes-list">
                <?php foreach($episodes as $episode): ?>
                
                    <li data-episodeid="<?php echo $episode->guid; ?>">
                        <div class="episode-list-image-play">
                            <img src="<?php echo $podcast_image; ?>" alt="<?php echo $episode->title; ?>">
                            <svg class="list-item-play-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1" data-name="Layer 1" viewBox="0 0 145.2 145.2"><defs>
                                <style>
                                    .lpi-1 { fill: none; }      
                                    .lpi-2 { clip-path: url(#clip-path); }      
                                    .lpi-3 { opacity: inherit; }      
                                    .lpi-4 { clip-path: url(#clip-path-3); }         
                                </style>
                                <clipPath id="clip-path" transform="translate(-264.41 -245.59)">
                                    <rect class="lpi-1" x="264.41" y="245.59" width="145.2" height="145.2"></rect>
                                </clipPath>
                                <clipPath id="clip-path-3" transform="translate(-264.41 -245.59)">
                                    <rect class="lpi-1" x="255.41" y="238.59" width="163.2" height="153.2"></rect>
                                </clipPath></defs>
                                <g class="lpi-2">
                                <g class="lpi-2">
                                <g class="lpi-3">
                                <g class="lpi-4">
                                <path style="fill: #fff" class="cls-5" d="M378.93,318.19,311,357.4V279Zm30.68,0a72.6,72.6,0,1,0-72.6,72.6,72.6,72.6,0,0,0,72.6-72.6" transform="translate(-264.41 -245.59)"></path>
                                </g></g></g></g>
                            </svg>
                        </div>
                        <div class="episode-list-title-description">
                            <h5><?php echo $episode->title; ?></h5>
                            <p><?php echo $episode->description; ?></p>
                        </div>
                    </li>

                <?php endforeach; ?>
            </ol>
        </main>
        <script>
            const startingEpisodeId = "<?php echo $starting_episode_id; ?>";
            const rssData = <?php echo $rss_data; ?>;
        </script>
        <script src="/scripts/rss_data.js"></script>
        <script src="/scripts/element_selectors.js"></script>
        <script src="/scripts/event_handlers.js"></script>
    <?php endif; ?>
</body>
</html>
