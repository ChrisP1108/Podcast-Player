<?php

    // Get List Of Google Fonts from google_fonts.json

    $google_fonts_json = json_decode(@file_get_contents("fonts/google_fonts.json"), true);

    $fonts_list = [];

    foreach($google_fonts_json['items'] as $font) {
        $font_name = $font['family'];
        array_push($fonts_list, $font_name);
    }

    sort($fonts_list);