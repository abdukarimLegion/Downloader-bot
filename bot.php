<?php

$token = "7937520967:AAHc3_UAcohHzahC4ZjBjqnODOFiPhR3EqE"; // Bot tokenini o'rnating
$apiUrl = "https://api.telegram.org/bot$token/";

function sendMessage($chatId, $message) {
    global $apiUrl;
    file_get_contents($apiUrl . "sendMessage?chat_id=$chatId&text=" . urlencode($message));
}

function downloadVideo($videoUrl, $chatId) {
    $apiEndpoint = "https://instagram-video-reels-stories-downloader.p.rapidapi.com/rapid-api-downloader/param?url=" . urlencode($videoUrl);
    $apiKey = "9c6770e9a6msh854e7baadda43dep19ef03jsn3238dcedc9e4"; // RapidAPI kalitini o'rnating

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: instagram-video-reels-stories-downloader.p.rapidapi.com",
            "x-rapidapi-key: $apiKey"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        sendMessage($chatId, "cURL Xato: " . $err);
    } else {
        $videoData = json_decode($response, true);
        if (isset($videoData['video_url'])) {
            $downloadLink = $videoData['video_url'];
            $videoContent = file_get_contents($downloadLink);
            file_put_contents('video.mp4', $videoContent);
            sendMessage($chatId, "Video muvaffaqiyatli yuklab olindi: [video.mp4](video.mp4)");
        } else {
            sendMessage($chatId, "Videoni yuklab olishda xato: " . $response);
        }
    }
}

$update = json_decode(file_get_contents("php://input"), true);
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];

    if (filter_var($text, FILTER_VALIDATE_URL)) {
        downloadVideo($text, $chatId);
    } else {
        sendMessage($chatId, "Iltimos, video URL manzilini kiriting.");
    }
}

?>
