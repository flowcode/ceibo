<?php

function getImageUrl($url) {
    $base = flowcode\mvc\kernel\Config::get("files", "images_path");
    return $base . "/" . $url;
}

function getUploadUrl($url) {
    $base = flowcode\mvc\kernel\Config::get("files", "upload_path");
    return $base . "/" . $url;
}

function getStyleUrl($url) {
    $base = flowcode\mvc\kernel\Config::get("files", "css_path");
    return $base . "/" . $url;
}

function getJavascriptUrl($url) {
    $base = flowcode\mvc\kernel\Config::get("files", "js_path");
    return $base . "/" . $url;
}

function getUrl($url) {
    $base = flowcode\mvc\kernel\Config::get("url", "full");
    return $base . "/" . $url;
}

?>
