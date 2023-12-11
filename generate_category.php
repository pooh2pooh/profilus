<?php

    require 'vk_api_config.php';

    $categoriesKeywords = [
        'Спорт' => ['футбол', 'баскетбол', 'волейбол'],
        'Музыка' => ['концерт', 'рок', 'джаз', 'поп-музыка'],
        'Технологии' => ['IT', 'компьютеры', 'новые технологии', '2SDP', 'Хабр'],
        'Логика' => ['настольные игры', 'головоломки', 'шахматы'],
        // Категории групп по названию и/или описанию, ключевые слова
    ];


    $group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : '199072251'; // Если не передан ID который нужно анализировать

    try {

        $result = '';

        $response = $vk->groups()->getById($access_token, [
            'group_id' => $group_id,
            'fields' => ['market', 'description', 'name']
        ]);

        if (!empty($response)) {
            // Объединяем описание и название группы для поиска ключевых слов
            $combinedText = '';
            if (isset($response[0]['description'])) {
                $combinedText .= $response[0]['description'] . ' ';
            }
            if (isset($response[0]['name'])) {
                $combinedText .= $response[0]['name'];
            }

            $lowercaseCombinedText = mb_strtolower($combinedText, 'UTF-8');

            foreach ($categoriesKeywords as $category => $keywords) {
                foreach ($keywords as $keyword) {
                    $lowercaseKeyword = mb_strtolower($keyword, 'UTF-8');
                    if (strpos($lowercaseCombinedText, $lowercaseKeyword) !== false) {
                        $result = $category;
                    }
                }
            }
        }

        echo $result;

    } catch (Exception $e) {
        exit('Ошибка при получении категории группы: ' . $e->getMessage());
    }
