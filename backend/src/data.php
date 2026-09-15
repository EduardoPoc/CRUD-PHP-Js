<?php

require_once __DIR__ . '/config/config.php';

function loadData(): array
{
    return json_decode(file_get_contents(DATA_FILE), true);
}

function saveData(array $data): void
{
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function findUserById(int $id): ?array
{
    $data = loadData();

    foreach ($data['users'] as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }

    return null;
}

function insertUser(array $user): array
{
    $data = loadData();

    $id = $data['nextId'] ?? 1;
    $data['nextId'] = $id + 1;

    $user['id'] = $id;
    $data['users'][] = $user;

    saveData($data);

    return $user;
}

function updateUser(int $id, array $fields): ?array
{
    $data = loadData();
    $users = $data['users'];

    for ($i = 0; $i < count($users); $i++) {
        if ($users[$i]['id'] === $id) {
            $data['users'][$i] = array_merge($users[$i], $fields);
            saveData($data);
            return $data['users'][$i];
        }
    }

    return null;
}

function deleteUser(int $id): ?array
{
    $data = loadData();
    $users = $data['users'];

    for ($i = 0; $i < count($users); $i++) {
        if ($users[$i]['id'] === $id) {
            $user = $users[$i];
            array_splice($users, $i, 1);
            $data['users'] = $users;
            saveData($data);
            return $user;
        }
    }

    return null;
}