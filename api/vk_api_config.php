<?php 
    
    require __DIR__ . '/../vendor/autoload.php'; // Подключение библиотеки vk-php-sdk
    $access_token = 'vk1.a.IYo6Jy1Vo6n4gZzqnybpt3-fmAegwSMwCGYgcb-kph78c_bb88Q0-OvxuinuqiJyhPqBuQEFCcposVbGg9AQ5hSQ2AnArP_NuB9LmC4fqhkDuYUZvEJOmO2Ho63zI_mxBa-lg_YcgeEOAoZBeuWL4t8aQY84Z4qssOtVREpEEVlsX5AryLZARywdyp2FF03oerxs4eAgOWN-sc8RboYT8g';


    $vk = new \VK\Client\VKApiClient();
    $result = array();
    
    $categoriesKeywords = [
        'Спорт' => ['футбол', 'баскетбол', 'волейбол'],
        'Музыка' => ['концерт', 'рок', 'джаз', 'поп-музыка'],
        'Технологии' => ['IT', 'компьютеры', 'новые технологии', '2SDP', 'Хабр'],
        'Логика' => ['настольные игры', 'головоломки', 'шахматы'],
        // Категории групп по названию и/или описанию, ключевые слова
    ];