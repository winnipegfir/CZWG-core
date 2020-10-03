<?php

function decimal_to_hm($decimal) {
    $seconds = ($decimal * 3600);
    $hours = floor($decimal);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);

    return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT);
}
