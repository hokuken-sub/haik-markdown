<?php
$data = array();
$error = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
  $body = $_POST['body'];
  $data['html'] = $body;
}

$data['error'] = $error;

echo json_encode($data);

