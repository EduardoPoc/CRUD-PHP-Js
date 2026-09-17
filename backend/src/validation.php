<?php

function validateUserId(mixed $id): ?string
{
    if ($id === null || $id === '') {
        return 'User id is required';
    }

    // Vem sempre da query string, então é string: só aceitamos dígitos.
    if (!is_string($id) || !ctype_digit($id)) {
        return 'User id must be a positive integer';
    }

    return null;
}

function validateRequiredFields(array $input, array $fields): ?string
{
    $missing = [];

    foreach ($fields as $field) {
        if (!isset($input[$field])) {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        $verb = count($missing) === 1 ? ' is required' : ' are required';

        return implode(', ', $missing) . $verb;
    }

    return null;
}

function validateUserFields(array $input): ?string
{
    if (isset($input['name'])) {
        if (!is_string($input['name'])) {
            return 'Name must be a string';
        }

        $name = trim($input['name']);

        if ($name === '') {
            return 'Name cannot be empty';
        }

        // mb_strlen conta caracteres; strlen contaria bytes e cortaria acentos cedo demais.
        if (mb_strlen($name) > 100) {
            return 'Name must be at most 100 characters';
        }
    }

    if (isset($input['age'])) {
        if (!is_numeric($input['age'])) {
            return 'Age must be a number';
        }

        $age = (int) $input['age'];

        if ($age < 1 || $age > 150) {
            return 'Age must be between 1 and 150';
        }
    }

    if (isset($input['email'])) {
        if (!is_string($input['email'])) {
            return 'Email must be a string';
        }

        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format';
        }
    }

    return null;
}
