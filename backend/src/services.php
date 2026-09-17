<?php

require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/data.php';

function getAllUsers(): array
{
    $data = loadData();

    return ['data' => ['users' => $data['users']], 'status' => 200];
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

function editUser(mixed $id, ?array $input, bool $partial = false): array
{
    $error = validateUserId($id);
    if ($error) {
        return ['error' => $error, 'status' => 400];
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

    if (empty($fields)) {
        return ['error' => 'At least one of name, age or email must be sent', 'status' => 400];
    }

    if (isset($fields['name'])) {
        $fields['name'] = trim($fields['name']);
    }

    if (isset($fields['age'])) {
        $fields['age'] = (int) $fields['age'];
    }

    $user = updateUser((int) $id, $fields);

    if ($user === null) {
        return ['error' => 'User not found', 'status' => 404];
    }

    return ['data' => $user, 'status' => 200];
}

function removeUser(mixed $id): array
{
    $error = validateUserId($id);
    if ($error) {
        return ['error' => $error, 'status' => 400];
    }

    $user = deleteUser((int) $id);

    if ($user === null) {
        return ['error' => 'User not found', 'status' => 404];
    }

    return ['data' => ['deleted' => $user], 'status' => 200];
}
