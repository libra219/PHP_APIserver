<?php

function get_articles($conn) {
    $sql = "SELECT * FROM articles";
    $result = $conn->query($sql);
    $response_data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $response_data[] = [
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'title' => $row['title'],
                'body' => $row['body']
            ];
        }
    }
    return json_encode($response_data);
}

function create_article($conn, $request) {
    $login_user_id = 1; // 仮です
    $title = $request['title'];
    $body = $request['body'];
    // バリデーション
    if (empty($title) || empty($body)) {
        return json_encode([
            'message' => '記事のタイトルと本文は、必須項目です'
        ]);
    }
    // "プリペアードステートメント"でググる
    $stmt = $conn->prepare("INSERT INTO articles (user_id, title, body) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $login_user_id, $title, $body);
    $stmt->execute();
    $stmt->close();
    http_response_code(201);
    return;
}
