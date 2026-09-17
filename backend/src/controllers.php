<?php

require_once __DIR__ . '/services.php';

function respond(array $result): void
{
    http_response_code($result['status']);

    if (isset($result['error'])) {
        echo json_encode(['error' => $result['error']]);
    } else {
        echo json_encode($result['data']);
    }
}

function respondServerError(\Throwable $e): void
{
    // O detalhe do erro vai para o log do servidor, nunca para o cliente.
    error_log((string) $e);

    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

function readJsonBody(): ?array
{
    $input = json_decode(file_get_contents('php://input'), true);

    return is_array($input) ? $input : null;
}

function handleGet(): void
{
    try {
        respond(getAllUsers());
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handlePost(): void
{
    try {
        respond(createUser(readJsonBody()));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handlePut(): void
{
    try {
        respond(editUser($_GET['id'] ?? null, readJsonBody()));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handlePatch(): void
{
    try {
        respond(editUser($_GET['id'] ?? null, readJsonBody(), partial: true));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handleDelete(): void
{
    try {
        respond(removeUser($_GET['id'] ?? null));
    } catch (\Throwable $e) {
        respondServerError($e);
    }
}

function handleMethodNotAllowed(): void
{
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
