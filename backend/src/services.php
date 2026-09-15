<?php

require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/data.php';

function getAllUsers(): array
{
    $data = loadData();
    return ['users' => $data['users']];
}

function createUser(?array $input): array
{
    if (!is_array($input)) {
        return ['error' => 'Invalid JSON body', 'status' => 400];
    }

    $error = validateRequiredFields($input, ['name', 'age', 'email']);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    $error = validateUserFields($input);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    $user = insertUser([
        'name' => trim($input['name']),
        'age' => (int) $input['age'],
        'email' => $input['email'],
    ]);

    return ['data' => $user, 'status' => 201];
}
function editUser(?int $id, ?array $input, bool $partial = false): array
{
    if ($id === null) {
        return ['error' => 'User id is required', 'status' => 400];
    }

    if (!is_array($input)) {
        return ['error' => 'Invalid JSON body', 'status' => 400];
    }

    if (!$partial) {
        $error = validateRequiredFields($input, ['name', 'age', 'email']);
        if ($error) {
            return ['error' => $error, 'status' => 400];
        }
    }

    $error = validateUserFields($input);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    $allowed = ['name', 'age', 'email'];
    $fields = array_intersect_key($input, array_flip($allowed));

    if (isset($fields['name'])) {
        $fields['name'] = trim($fields['name']);
    }

    if (isset($fields['age'])) {
        $fields['age'] = (int) $fields['age'];
    }

    $user = updateUser($id, $fields);

    if ($user === null) {
        return ['error' => 'User not found', 'status' => 404];
    }

    return ['data' => $user, 'status' => 200];
}
function removeUser(?int $id): array
{
    if ($id === null) {
        return ['error' => 'User id is required', 'status' => 400];
    }

    $user = deleteUser($id);

    if ($user === null) {
        return ['error' => 'User not found', 'status' => 404];
    }

    return ['data' => ['deleted' => $user], 'status' => 200];
}