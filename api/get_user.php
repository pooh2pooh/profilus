<?php

    require 'vk_api_config.php';


    //$_POST['username_to_watch'] = '411408000';
    //$_POST['access_token'] = 'vk1.a.lPqo5DR941MAY5LpFq8nMjsL1CS82XEtSS5BpmXTkLyQv0jm2d0HpA3Rykg4O2RvXJFku1n6IPGNaS5OwFaEuvdyPYfNHRrm_VX1COVaWgxxeOokyDuxDkBo76QzONnJPZ6gP6JgG7zLXWapO0skQ0ZFVluSIO6cGAp97PfNPMyk1UQtzd0534RGdDdYZNMEMP1qqdHIdBLWdp4FCRDDdg';

    // Если пользователь авторизовался по кнопке VK ID,
    // сразу проводим анализ его профиля с его токеном
    if (!empty($_POST['access_token'])) {
        $access_token = $_POST['access_token'];
    }
    $username = !empty($_POST['username_to_watch']) ? $_POST['username_to_watch'] : 'pooh2pooh'; // Если не передан ID который нужно анализировать

    try {
        // Определяем, является ли входное значение числовым ID, username или URL
        if (is_numeric($username) || strpos($username, 'id') === 0) {
            // Если это числовой ID или начинается с 'id', извлекаем числовую часть
            $user_id = is_numeric($username) ? $username : substr($username, 2);
        } elseif (preg_match('/https?:\/\/vk\.com\/(id[0-9]+)/', $username, $matches)) {
            // Если это URL, извлекаем ID из URL
            $user_id = substr($matches[1], 2);
        } else {
            // Иначе предполагаем, что это username
            $user_id = $username;
        }


        // Получение информации о пользователе
        $user_response = $vk->users()->get($access_token, [
            'user_ids' => [$user_id],
            'fields' => ['career', 'education', 'first_name', 'last_name', 'photo_100', 'online', 'city', 'screen_name', 'bdate']
        ]);
    } catch (\VK\Exceptions\Api\VKApiException $e) {
        // Обработка исключений, связанных с API
        exit(json_encode(['status' => 'error', 'message' => 'Ошибка VK API: ' . $e->getMessage()]));
        // Дополнительная обработка ошибок API
    } catch (\VK\Exceptions\VKClientException $e) {
        // Обработка исключений, связанных с клиентом VK
        exit(json_encode(['status' => 'error', 'message' => 'Ошибка клиента VK: ' . $e->getMessage()]));
        // Дополнительная обработка ошибок клиента
    } catch (\Exception $e) {
        // Обработка всех остальных исключений
        exit(json_encode(['status' => 'error', 'message' => 'Общая ошибка: ' . $e->getMessage()]));
        // Дополнительная обработка общих исключений
    }

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

        // Рассчитываем возраст пользователя
        if (isset($user_response[0]['bdate'])) {
            $bdate = explode('.', $user_response[0]['bdate']);
            if (count($bdate) === 3) {
                // Полная дата рождения (день, месяц, год)
                $birthDate = DateTime::createFromFormat('d.m.Y', $user_response[0]['bdate']);
                $currentDate = new DateTime();
                $age = $currentDate->diff($birthDate)->y;
                $result['user_info']['Возраст'] = $age;
            } else {
                // Только день и месяц
                $result['user_info']['День_Рождения'] = $user_response[0]['bdate'];
            }
        } else {
            $result['user_info']['День_Рождения'] = 'Не указан';
        }


        try {
            // Получение подписок пользователя
            $group_response = $vk->groups()->get($access_token, [
                'user_id' => $user_response[0]['id'],
                'extended' => 1,
                'count' => 100
            ]);
        } catch (\VK\Exceptions\Api\VKApiPrivateProfileException $e) {
            // Обработка исключений, это закрытый профиль
            exit(json_encode(['status' => 'error', 'message' => 'Ошибка, это закрытый профиль.']));
            // Дополнительная обработка ошибок API
        }


        if (!empty($group_response['items'])) {
            $result['subscriptions'] = array();
            $result['subscriptions']['order'] = array(); // Массив для сохранения порядка ID групп
        
            foreach ($group_response['items'] as $group) {
                // Сохраняем как название группы, так и её ID
                $result['subscriptions'][$group['id']] = array(
                    'name' => $group['name']
                );
                $result['subscriptions']['order'][] = $group['id']; // Сохраняем порядок ID групп
            }
        } else {
            $result['subscriptions'] = "У пользователя нет подписок на группы.";
        }
        




        // Получение постов со стены пользователя
        $posts_response = $vk->wall()->get($access_token, [
            'owner_id' => $user_response[0]['id'],
            'count' => 100
        ]);


        if (!empty($posts_response['items'][0]['copy_history'])) {
            $result['reposts'] = array();
            $result['reposts']['order'] = array(); // Массив для сохранения порядка ID групп
        
            foreach ($posts_response['items'] as $post) {
                if (isset($post['copy_history']) && !empty($post['copy_history'])) {
                    $repost = $post['copy_history'][0];
                    if (isset($repost['owner_id']) && $repost['owner_id'] < 0) {
                        $group_id = abs($repost['owner_id']);
        
                        // Проверяем, существует ли уже запись для этой группы
                        if (!array_key_exists($group_id, $result['reposts'])) {
                            $result['reposts'][$group_id] = array('dates' => array());
                            $result['reposts']['order'][] = $group_id; // Сохраняем порядок ID групп
                        }
        
                        // Добавляем дату репоста в массив дат для данной группы
                        $result['reposts'][$group_id]['dates'][] = date('Y-m-d H:i:s', $repost['date']);
                    }
                }
            }
        } else {
            $result['reposts'] = 'Репосты из групп не найдены.';
        }        
    } else {
        exit(json_encode(['status' => 'error', 'message' => 'Пользователь ' . $username . ' не найден.']));
    }

    // var_dump($result);
    echo json_encode($result);
