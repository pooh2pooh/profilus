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
            $result['category'] = '';
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
                        $result['category'] = $category;
                    }
                }
            }
        }

        echo json_encode($result);

    } catch (Exception $e) {
        exit('Ошибка при получении имени группы из репоста: ' . $e->getMessage());
    }