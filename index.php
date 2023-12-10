<?php

require __DIR__ . '/vendor/autoload.php'; // Подключение библиотеки vk-php-sdk

$access_token = 'vk1.a.IYo6Jy1Vo6n4gZzqnybpt3-fmAegwSMwCGYgcb-kph78c_bb88Q0-OvxuinuqiJyhPqBuQEFCcposVbGg9AQ5hSQ2AnArP_NuB9LmC4fqhkDuYUZvEJOmO2Ho63zI_mxBa-lg_YcgeEOAoZBeuWL4t8aQY84Z4qssOtVREpEEVlsX5AryLZARywdyp2FF03oerxs4eAgOWN-sc8RboYT8g';

$vk = new \VK\Client\VKApiClient();

// Предполагается, что $access_token уже получен
$username = !empty($_POST['username_to_watch']) ? $_POST['username_to_watch'] : 'sega_as'; // Замените на username пользователя
$result = '';

try {
    // Определяем, является ли входное значение числовым ID или username
    if (is_numeric($username) || strpos($username, 'id') === 0) {
        // Если это числовой ID или начинается с 'id', извлекаем числовую часть
        $userId = is_numeric($username) ? $username : substr($username, 2);
    } else {
        // Иначе предполагаем, что это username
        $userId = $username;
    }

    // Получение информации о пользователе
    $user_response = $vk->users()->get($access_token, [
        'user_ids' => [$userId],
        'fields' => ['career', 'education', 'first_name', 'last_name', 'photo_100', 'online', 'city', 'screen_name']
    ]);
} catch (\VK\Exceptions\Api\VKApiException $e) {
    // Обработка исключений, связанных с API
    exit("Ошибка VK API: " . $e->getMessage());
    // Дополнительная обработка ошибок API
} catch (\VK\Exceptions\VKClientException $e) {
    // Обработка исключений, связанных с клиентом VK
    exit("Ошибка клиента VK: " . $e->getMessage());
    // Дополнительная обработка ошибок клиента
} catch (\Exception $e) {
    // Обработка всех остальных исключений
    exit("Общая ошибка: " . $e->getMessage());
    // Дополнительная обработка общих исключений
}

$result = array(); // Используйте массив для хранения результатов

if (!empty($user_response)) {
    $result['user_info'] = array(
        "Аватарка" => $user_response[0]['photo_100'], // URL аватарки
        "ID" => $user_response[0]['id'],
        "userame" => $user_response[0]['screen_name'],
        "Имя" => $user_response[0]['first_name'] . ' ' . $user_response[0]['last_name'],
        "Онлайн" => $user_response[0]['online'] == 1 ? "Онлайн" : "Офлайн",
        "Город" => isset($user_response[0]['city']) ? $user_response[0]['city']['title'] : "Не указан",
        // ... Другие поля ...
    );


    // Получение подписок пользователя
    $group_response = $vk->groups()->get($access_token, [
        'user_id' => $user_response[0]['id'],
        'extended' => 1,
        'count' => 100
    ]);

    if (!empty($group_response['items'])) {
        $result['subscriptions'] = array();
        foreach ($group_response['items'] as $group) {
            // Сохраняем как название группы, так и её ID
            array_push($result['subscriptions'], array(
                'name' => $group['name'],
                'id' => $group['id']
            ));
        }
    } else {
        $result['subscriptions'] = "У пользователя нет подписок на группы.";
    }
    

    // Получение постов со стены пользователя
    $posts_response = $vk->wall()->get($access_token, [
        'owner_id' => $user_response[0]['id'],
        'count' => 100
    ]);

    if (!empty($posts_response['items'])) {
        $result['reposts'] = array();
        foreach ($posts_response['items'] as $post) {
            if (isset($post['copy_history']) && !empty($post['copy_history'])) {
                // Ваш код для обработки репостов
                $repost = $post['copy_history'][0];
                if (isset($repost['owner_id']) && $repost['owner_id'] < 0) {
                    // var_dump($repost);
                    // exit;
                    $group_id = abs($repost['owner_id']);
                    if (!in_array($group_id, $result['reposts'])) {
                        array_push($result['reposts'], $group_id);
                    }
                }
            }
        }
    } else {
        $result['reposts'] = "Репосты из групп не найдены.";
    }

} else {
    $result['error'] = "Пользователь с username '$username' не найден.";
}

echo json_encode($result);
