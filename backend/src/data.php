<?php

require_once __DIR__ . '/../config/config.php';

function emptyData(): array
{
    return ['users' => [], 'nextId' => 1];
}

function loadData(): array
{
    if (!is_file(DATA_FILE)) {
        return emptyData();
    }

    $content = file_get_contents(DATA_FILE);

    if ($content === false) {
        return emptyData();
    }

    $data = json_decode($content, true);

    if (!is_array($data) || !isset($data['users'], $data['nextId'])) {
        return emptyData();
    }

    return $data;
}

function saveData(array $data): void
{
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Serializa um ciclo ler → alterar → gravar.
 *
 * Sem isso, duas requisições simultâneas leem o mesmo estado e a segunda
 * gravação apaga a primeira. O flock faz a segunda requisição esperar.
 */
function withDataLock(callable $operation): mixed
{
    // 'c' cria o arquivo se não existir e não apaga o conteúdo.
    $lock = fopen(DATA_FILE, 'c');

    if ($lock === false) {
        throw new RuntimeException('Could not open the data file');
    }

    try {
        if (!flock($lock, LOCK_EX)) {
            throw new RuntimeException('Could not lock the data file');
        }

        return $operation();
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function insertUser(array $user): array
{
    return withDataLock(function () use ($user): array {
        $data = loadData();

        $id = $data['nextId'];
        $data['nextId'] = $id + 1;

        $user['id'] = $id;
        $data['users'][] = $user;

        saveData($data);

        return $user;
    });
}

function updateUser(int $id, array $fields): ?array
{
    return withDataLock(function () use ($id, $fields): ?array {
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
    });
}

function deleteUser(int $id): ?array
{
    return withDataLock(function () use ($id): ?array {
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
    });
}
