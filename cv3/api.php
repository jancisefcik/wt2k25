<?php

require_once('../../config.php');  // $pdo
require_once('Laureate.class.php');

$laureate = new Laureate($pdo);

header("Content-Type: application/json");

// https://node8.webte.fei.stuba.sk/cv3/api/v0/laureates/1/prizes/1/
// POST, GET, PUT, DELETE - CRUD: Create, Read, Update, Delete

$method = $_SERVER['REQUEST_METHOD'];
$route = explode('/', $_GET['route']);

switch ($method) {
    case 'GET':
        if ($route[0] == 'laureates' && count($route) == 1) {
            http_response_code(200);
            echo json_encode($laureate->index());  // Get all laureates
            break;
        } elseif ($route[0] == 'laureates' && count($route) == 2 && is_numeric($route[1])) {
            $id = $route[1];
            $data = $laureate->show($id);
            if ($data) {
                http_response_code(200);
                echo json_encode($data);
                break;
            }
        }
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
        break;
    case 'POST':
        if ($route[0] == 'laureates' && count($route) == 1) {
            $data = json_decode(file_get_contents('php://input'), true);

            foreach ($data as $key => $value) {
                if (!isset($data[$key]) || $data[$key] == '') {
                    $data[$key] = null;
                }
            }

            $newID = $laureate->store($data['gender'], $data['birth'], $data['death'], $data['fullname'], $data['organisation']);

            if (!is_numeric($newID)) {
                http_response_code(400);
                echo json_encode(['message' => "Bad request", 'data' => $newID]);
                break;
            }

            $new_laureate = $laureate->show($newID);
            http_response_code(201);
            echo json_encode([
                'message' => "Created successfully",
                'data' => $new_laureate
            ]);
            break;

        }
        http_response_code(400);
        echo json_encode(['message' => 'Bad request']);
        break;
    case 'PUT':
        if ($route[0] == 'laureates' && count($route) == 2 && is_numeric($route[1])) {
            $currentID = $route[1];
            $currentData = $laureate->show($currentID);
            if (!$currentData) {
                http_response_code(404);
                echo json_encode(['message' => 'Not found']);
                break;
            }

            $updatedData = json_decode(file_get_contents('php://input'), true);
            $currentData = array_merge($currentData, $updatedData);

            $status = $laureate->update(
                $currentID,
                $currentData['gender'],
                $currentData['birth'],
                $currentData['death'],
                $currentData['fullname'],
                $currentData['organisation']
            );

            if ($status != 0) {
                http_response_code(400);
                echo json_encode(['message' => "Bad request", 'data' => $status]);
                break;
            }

            http_response_code(201);
            echo json_encode([
                'message' => "Updated successfully",
                'data' => $currentData
            ]);
            break;
        }
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
        break;
    case 'DELETE':
        if ($route[0] == 'laureates' && count($route) == 2 && is_numeric($route[1])) {
            $id = $route[1];
            $exist = $laureate->show($id);
            if (!$exist) {
                http_response_code(404);
                echo json_encode(['message' => 'Not found']);
                break;
            }

            $status = $laureate->destroy($id);

            if ($status != 0) {
                http_response_code(400);
                echo json_encode(['message' => "Bad request", 'data' => $status]);
                break;
            }

            http_response_code(201);
            echo json_encode(['message' => "Deleted successfully"]);
            break;

        }
        http_response_code(404);
        echo json_encode(['message' => 'Not found']);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}