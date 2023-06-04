<?php 

    // Sample RSS Feed URLs

    // Get Url Parameters

    $url_origin = $_SERVER['REQUEST_URI'];

    $url_components = parse_url($url_origin);

    parse_str($url_components['query'], $params);

    // Url Parameters

    $rss_url = $params['url'];

    // Styling Parameters

    $style_mode = $params['mode'] ?? null;

    if ($style_mode === 'dark' || $style_mode === null) {
        $style_mode = '#000';
    } else {
        $style_mode = '#fff';
    }

    $style_color_1 = $params['color1'] ?? null;

    if (!$style_color_1) {
        if ($style_mode === '#000') {
            $style_color_1 = '#999999';
        } else {
            $style_color_1 = '#aaa';
        }
    } else {
        $style_color_1 = '#' . $style_color_1;
    }

    $style_progress_bar_color = $params['progressbarcolor'] ?? null;

    if (!$style_progress_bar_color) {
        if ($style_mode === '#000') {
            $style_progress_bar_color = '#616161';
        } else {
            $style_progress_bar_color = '#777';
        }
    } else {
        $style_progress_bar_color = '#' . $style_progress_bar_color;
    }

    $style_play_button = $params['buttoncolor'] ?? null;

    if (!$style_play_button) {
        if ($style_mode === '#000') {
            $style_play_button = '#fff';
        } else {
            $style_play_button = '#666';
        }
    } else {
        $style_play_button = '#' . $style_play_button; 
    }

    $style_highlight = $params['highlightcolor'] ?? null;

    if (!$style_highlight) {
        if ($style_mode === '#000') {
            $style_highlight = '#fff';
        } else {
            $style_highlight = '#666';
        }
    } else {
        $style_highlight = '#' . $style_highlight; 
    }

    $style_scrollbar = $params['scrollcolor'] ?? null;

    if (!$style_scrollbar) {
        if ($style_mode === '#000') {
            $style_scrollbar = '#fff';
        } else {
            $style_scrollbar = '#666';
        }
    } else {
        $style_scrollbar = '#' . $style_scrollbar; 
    }

    $style_font = $params['font'] ?? null;

    if (!$style_font) {
        $style_font = 'Poppins';
    }

    $style_text_color = $params['textcolor'] ?? null;

    if (!$style_text_color) {
        if ($style_mode === '#000') {
            $style_text_color = '#fff';
        } else {
            $style_text_color = '#000';
        }
    } else {
        $style_text_color = '#' . $style_text_color; 
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
