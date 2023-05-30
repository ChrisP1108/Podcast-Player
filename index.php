<?php 

    // Get Url Parameters

    $url_origin = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url_origin);

    parse_str($url_components['query'], $params);

    // RSS Url

    $rss_url = $params['url'];

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

            $parsed_track = intval($params['track']) - 1 ?? 0;

            $episode_selected = $parsed_track ? $episodes[$parsed_track] : $episodes[0];

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

    $audio_timing_divider = 16.0182204082;
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
        body::before {
            background-image: url(<?php echo $podcast_image; ?>);
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
                                    .cls-1 { fill: none; }      
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
                                <defs>
                                    <style>
                                    .cls-1 {
                                        fill: #fff;
                                    }
                                    </style>
                                </defs>
                                <path class="cls-1" d="M-132,798.77a72.61,72.61,0,0,1-72.6,72.6,72.6,72.6,0,0,1-72.6-72.6,72.59,72.59,0,0,1,72.6-72.6A72.6,72.6,0,0,1-132,798.77Zm-84-38.3h-19.41v75.79H-216Zm41.3,0h-19.41v75.79h19.41Z" transform="translate(277.21 -726.17)"/>
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
                                <?php 
                                    $time = ($audio_data['length'] / $audio_timing_divider) + 900;

                                    // Convert milliseconds to hours, minutes, and seconds

                                    $hours = floor($time / 3600000);

                                    if ($hours > 0) {
                                        $time = $time - ($hours * 3600000);
                                    }

                                    $minutes = floor($time / 60000);

                                    if ($minutes > 0) {
                                        $time = $time - ($minutes * 60000);
                                    }

                                    $seconds = floor($time / 1000);
                                    if ($seconds > 0) {
                                        $time = $time - ($seconds * 1000);
                                    }

                                    echo '<h5 id="current-episode-time" style="width: ';

                                    if ($hours > 0) {
                                        $output = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                                        echo '7ch;">00:00:00';
                                    } else {
                                        $output = sprintf("%02d:%02d", $minutes, $seconds);
                                        echo '5ch;">00:00';
                                    } 

                                    echo '</h5><h5> / </h5>';

                                    echo '<h5 id="current-episode-duration">'.$output.'</h5>';
                                ?>
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
                        <img src="<?php echo $podcast_image; ?>" alt="<?php echo $episode->title; ?>">
                        <div class="episode-list-title-description">
                            <h5><?php echo $episode->title; ?></h5>
                            <p><?php echo $episode->description; ?></p>
                        </div>
                    </li>

                <?php endforeach; ?>
            </ol>
        </main>
        <script>
            const audioDivider = <?php echo $audio_timing_divider; ?>;
            const rssData = <?php echo $rss_data; ?>;
        </script>
        <script src="script.js"></script>
    <?php endif; ?>
</body>
</html>
