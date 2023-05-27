<?php
$filePath = 'IDstorage.json';
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // Read token IDs
  if (file_exists($filePath)) {
    $data = json_decode(file_get_contents($filePath), true);
  }
  echo json_encode($data);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Add or remove token IDs
  $requestBody = json_decode(file_get_contents('php://input'), true);

  if (isset($requestBody['account']) && isset($requestBody['tokenIds'])) {
    $account = $requestBody['account'];
    $tokenIds = $requestBody['tokenIds'];

    if (file_exists($filePath)) {
      $data = json_decode(file_get_contents($filePath), true);
    }

    if (!isset($data[$account])) {
      $data[$account] = [];
    }

    if (isset($requestBody['add']) && $requestBody['add']) {
      // Add token IDs
      $data[$account] = array_unique(array_merge($data[$account], $tokenIds));
    } else {
      // Remove token IDs
      $data[$account] = array_diff($data[$account], $tokenIds);
    }

    file_put_contents($filePath, json_encode($data));
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Invalid request body']);
  }
} else {
  http_response_code(405); // Method Not Allowed
}
