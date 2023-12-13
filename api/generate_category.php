<?php

    require 'vk_api_config.php';


    $group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : '199072251'; // Если не передан ID который нужно анализировать

    try {
        $response = $vk->groups()->getById($access_token, [
            'group_id' => $group_id,
            'fields' => ['market', 'description', 'name']
        ]);

        if (!empty($response)) {
            $result['name'] = $response[0]['name'];
            $result['category'] = 'Неизвестная категория';

            // Словарь для подсчета количества ключевых слов по категориям
            $categoryCounts = array_fill_keys(array_keys($categoriesKeywords), 0);
    
            // Обработка названия группы
            if (isset($response[0]['name'])) {
                $lowercaseName = mb_strtolower($response[0]['name'], 'UTF-8');
                foreach ($categoriesKeywords as $category => $keywords) {
                    foreach ($keywords as $keyword) {
                        $lowercaseKeyword = mb_strtolower($keyword, 'UTF-8');
                        if (strpos($lowercaseName, $lowercaseKeyword) !== false) {
                            $categoryCounts[$category] += 3; // +3 за каждое ключевое слово в названии
                        }
                    }
                }
            }
    
            // Обработка описания группы
            if (isset($response[0]['description'])) {
                $lowercaseDescription = mb_strtolower($response[0]['description'], 'UTF-8');
                foreach ($categoriesKeywords as $category => $keywords) {
                    foreach ($keywords as $keyword) {
                        $lowercaseKeyword = mb_strtolower($keyword, 'UTF-8');
                        if (strpos($lowercaseDescription, $lowercaseKeyword) !== false) {
                            $categoryCounts[$category]++; // +1 за каждое ключевое слово в описании
                        }
                    }
                }
            }
    
            // Определяем категорию с наибольшим количеством найденных ключевых слов
            $maxCount = max($categoryCounts);
            $result['category'] = array_search($maxCount, $categoryCounts);
        }

        echo json_encode($result);

    } catch (Exception $e) {
        exit(json_encode(['status' => 'error', 'message' => 'Ошибка при получении данных группы: ' . $e->getMessage()]));
    }
