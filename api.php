<?php
/*
Please be respectful and do not remove any credits when using this code, as it was created by djkeiran.co.uk. Additionally, please do not claim this work as your own. Instead, simply enjoy using it. Thank you.

Repo: https://github.com/keiranchippendaleuk/radio-api
website: https://djkeiran.co.uk
*/
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  header('HTTP/1.1 405 Method Not Allowed');
  header('Allow: GET');
  exit();
}

$azuraUrl = "https://azura.example.co.uk/api/nowplaying/1"; // set your azuracast link here 
$azuraData = @json_decode(file_get_contents($azuraUrl), true);
if ($azuraData === null) {
  header('HTTP/1.1 500 Internal Server Error');
  exit();
}

if ($azuraData['live']['is_live'] === false) {
  $azuraData['live']['streamer_name'] = "AutoDJ";
}

$songTitle = urlencode($azuraData['now_playing']['song']['title']);
$songArtist = urlencode($azuraData['now_playing']['song']['artist']);
$elevateUrl = "https://tools.elevatehosting.co.uk/api/v2/lookup/song?title=$songTitle&artist=$songArtist";
$elevateData = @json_decode(file_get_contents($elevateUrl), true);
if ($elevateData === null) {
  header('HTTP/1.1 500 Internal Server Error');
  exit();
}

/*DONT REMOVE*/
$credits = array(
  "meta data provided by" => "https://tools.elevatehosting.co.uk/api/v2/lookup/song?title=$songTitle&artist=$songArtist",
  "API made by" => 'https://github.com/keiranchippendaleuk/radio-api',
  "version" => '1.0.0',
);
/*END OF DONT REMOVE*/
$spotify = array(
  "uri" => $elevateData['result']['uri'],
  "preview" => $elevateData['result']['preview'],
  "spotify_id" => $elevateData['result']['spotify_id'],
);

$song = array(
  "title" => $elevateData['result']['title'],
  "artist" => $elevateData['result']['artist'],
  "covers" => $elevateData['result']['covers'],
  "color" => $elevateData['result']['color'],
  "spotify" => $spotify,
);

$response = array(
  "song" => $song,
  "live" => $azuraData['live'],
  "info" => $credits,
);

header("Content-Type: application/json");
echo json_encode($response);
?>
