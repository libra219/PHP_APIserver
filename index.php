<?php

// composerで読み込んだライブラリ
require './vendor/autoload.php';

require './env.php';
require './article_controller.php';
require './user_controller.php';

header("Content-Type: application/json; charset=UTF-8");



// echo json_encode([
//     "message" => "hogehoge"
// ]);
// クライアントからのリクエストデータ(json)をデコードして、phpのオブジェクトに格納
$request_json = file_get_contents('php://input');
$request_data = json_decode($request_json, TRUE);
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// mysqlのコネクション
$conn = new mysqli(DB_SERVER_NAME, USERNAME, PASSWORD, DB_NAME);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (preg_match('/^\/login$/', $request_uri) && $request_method === 'POST') {
    echo login_user($conn, $request_data);
} else if (preg_match('/^\/register$/', $request_uri) && $request_method === 'POST') {
    echo register_user($conn, $request_data);
} else if (preg_match('/^\/articles$/', $request_uri)) {
    switch ($request_method) {
        case 'GET':
            echo get_articles($conn);
            break;
        case 'POST':
            echo create_article($conn, $request_data, token_verify($conn));
            break;
    }
} else if (preg_match('/^\/articles\/([0-9]+)$/', $request_uri, $matches)) {
    $article_id = $matches[1];
    switch ($request_method) {
        case 'GET':
            echo get_article_detail($conn, $article_id);
            break;
        case 'PUT':
            echo update_article($conn, $article_id, $request_data);
            break;
        case 'DELETE':
            echo delete_article($conn, $article_id);
            break;
    }
} 

// if (preg_match('/^\/articles$/', $request_uri)) {
//     switch ($request_method) {
//         case 'GET':
//             echo get_articles($conn);
//             break;
//         case 'POST':
//             echo create_article($conn, $request_data);
//             break;
//     }
// }

// // /articles/{id}の時だけ
// else if (preg_match('/^\/articles\/([0-9]+)$/', $request_uri, $matches)) {
//     $article_id = $matches[1];
//     switch ($request_method) {
//         case 'GET':
//             echo get_article_detail($conn, $article_id);
//             break;
//         case 'PUT':
//             echo update_article($conn, $article_id, $request_data);
//             break;
//         case 'DELETE':
//             echo delete_article($conn, $article_id);
//             break;
//     }
// } 

// elseif (preg_match('/^\/register$/', $request_uri, $matches)) {
//     # code...
// }
// // /articles/{id}/comments の時だけ
// if (preg_match('/^\/articles\/([0-9]+)\/comments$/', $request_uri, $matches)) {
//     $id = $matches[1]; // $matches[1]に([0-9]+)のキャプチャ結果が⼊ってくる！
// }