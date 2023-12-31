<?php 
    
    require __DIR__ . '/../vendor/autoload.php'; // Подключение библиотеки vk-php-sdk
    $access_token = ''; // Сюда нужно прописать твой токен доступа ВК, используется для получения данных пользователей авторизованных не через VK API


    $vk = new \VK\Client\VKApiClient();
    $result = array();
    
    $categoriesKeywords = [

        'IT' => [
            'it', 'программирование', 'разработка', 'информационные технологии', 'компьютеры', 'кодинг', 'блокчейн', 'кибербезопасность', 
            'дата-центр', 'сетевые технологии', 'машинное обучение', 'искусственный интеллект', 'веб-разработка', 'мобильная разработка', 
            'разработка игр', 'игровая индустрия', 'IT-стартап', 'облако', 'облачные технологии', 'big data', 'большие данные', 
            'анализ данных', 'frontend', 'backend', 'fullstack', 'DevOps', 'девопс', 'фронтенд', 'бэкенд', 'фуллстек', 'фулстек', 'джун', 'мидл', 'сеньор', 
            'junior', 'middle', 'senior', 'UX', 'UI', 'user interface', 'пользовательский интерфейс', 'UX/UI', 'UX/UI дизайн', 'тестирование по', 
            'системное администрирование', 'программист', 'сисадмин', 'reactos', 'blender', '3d', 'платежная система', '«МИР»', 'opensource', 
            'open source', 'ZX-Evolution', 'ATM-turbo', '/dev/null', 'ai', 'технарь', 'математик', 'pooh'
        ],

        'Искусственный интеллект' => [
            'искусственный интеллект', 'машинное обучение', 'big data', 'большие данные', 'анализ данных', 'нейронные сети', 'глубокое обучение',
            'TensorFlow', 'PyTorch', 'Keras', 'обработка естественного языка', 'NLP', 'компьютерное зрение', 'алгоритмы машинного обучения',
            'обучение с подкреплением', 'Scikit-learn', 'предсказательное моделирование', 'анализ данных', 'data mining', 'облачные вычисления',
            'нейросеть', 'deep learning', 'GANs (генеративно-состязательные сети)', 'SVM (метод опорных векторов)', 'регрессионный анализ',
            'классификация данных', 'кластеризация', 'аномалии в данных', 'деревья решений', 'случайный лес', 'градиентный бустинг',
            'XGBoost', 'нейронные сети LSTM', 'автоматическое обучение', 'feature engineering', 'PCA (метод главных компонент)', 'аналитика больших данных',
            'обработка изображений', 'распознавание образов', 'алгоритмы оптимизации', 'нейронные сети CNN', 'YOLO (для распознавания объектов)',
            'OpenCV', 'анализ временных рядов', 'прогнозирование временных рядов', 'RNN (рекуррентные нейронные сети)', 'Seq2Seq', 'автоматизация процессов',
            'интеллектуальный анализ данных', 'аналитика поведения', 'машинное обучение в реальном времени', 'адаптивное обучение', 'нейронные сети GPT',
            'BERT (модель для обработки текста)', 'AlphaGo'
        ],

        'Аналитика' => [
            'аналитика', 'big data', 'большие данные', 'анализ данных', 'аналитика', 'big data', 'большие данные', 'анализ данных', 'data science',
            'дата сайенс', 'BI (бизнес-аналитика)', 'business intelligence', 'SQL', 'Tableau', 'Power BI', 'Excel', 'Google Analytics', 'статистический анализ',
            'машинное обучение', 'предсказательная аналитика', 'data mining', 'добыча данных', 'ETL (Extract, Transform, Load)', 'обработка данных',
            'визуализация данных', 'информационные панели', 'dashboard', 'KPI (ключевые показатели эффективности)', 'метрики', 'статистика',
            'R (язык программирования)', 'Python', 'аналитика в реальном времени', 'когнитивная аналитика', 'корреляционный анализ', 'регрессионный анализ',
            'кластеризация данных', 'текстовая аналитика', 'NLP (обработка естественного языка)', 'оптимизация процессов', 'аналитика поведения пользователей',
            'web analytics', 'веб-аналитика', 'CRM-аналитика', 'аналитика продаж', 'финансовая аналитика', 'маркетинговая аналитика',
            'аналитика социальных сетей', 'SAS (аналитическое ПО)', 'SPSS', 'Hadoop', 'Apache Spark', 'NoSQL', 'OLAP (онлайн-аналитическая обработка)'
        ],

        'Производство' => [
            'производство', 'производство', 'промышленность', 'завод', 'фабрика', 'производственная линия', 'сборочный конвейер', 'автоматизация производства',
            'качество продукции', 'станки', 'технологические процессы', 'производственное оборудование', 'техническое обслуживание', 'промышленная безопасность',
            'оптимизация производства', 'планирование производства', 'управление производством', 'производственный менеджмент', 'производственная логистика',
            'инженер-технолог', 'производственный контроль', 'системы управления качеством', 'ISO', 'LEAN', 'качество сырья', 'эффективность производства',
            'производственные мощности', 'массовое производство', 'серийное производство', 'индивидуальное производство', 'производственная автоматизация',
            'CNC-станки', '3D-печать', 'аддитивные технологии', 'производственный инжиниринг', 'производственная экология', 'энергоэффективность',
            'промышленный дизайн', 'производственная безопасность', 'производственные стандарты', 'производственный аудит', 'производственная оптимизация',
            'производственная аналитика', 'производственные инновации', 'производственные технологии', 'производственное планирование',
            'производственный менеджмент', 'производственный мониторинг', 'производственный контроль качества', 'производственная автоматизация', 'производственная логистика'
        ],

        'Технологии' => [
            'arduino', 'raspberry pi', 'микроконтроллеры', 'электромеханика', 'самоделки', 'DIY', 'своими руками', 'роботы', 'автоматизация', 
            'электроника', 'схемотехника', 'паяльник', '3D-печать', '3D-принтер', 'CNC', 'фрезеровка', 'лазерная резка', 'смарт-дом', 'IoT', 
            'интернет вещей', 'сенсоры', 'датчики', 'моторы', 'сервоприводы', 'степперы', 'макетирование', 'прототипирование', 'моделирование', 
            'CAD', 'Open Source Hardware', 'беспроводные технологии', 'RFID', 'NFC', 'батареи', 'аккумуляторы', 'солнечные панели', 'энергонезависимость', 
            'микроэлектроника', 'оптоэлектроника', 'фотоника', 'мехатроника', 'бионика', 'кибернетика', 'умные устройства', 'носимая электроника', 
            'гаджеты', 'мини-компьютеры', 'одноплатные компьютеры', 'системы на кристалле', 'SoC', 'модули', 'расширения', 'шилды', 'программирование микроконтроллеров', 
            'embedded systems', 'встраиваемые системы', 'промышленная автоматизация', 'PLC', 'программируемые логические контроллеры', 'ретро-техника', 
            'винтажная электроника', 'ремонт электроники', 'электронный дизайн', 'PCB', 'печатные платы', 'сборка электроники', 'монтаж электроники', 
            'электронные компоненты', 'микросхемы', 'транзисторы', 'диоды', 'резисторы', 'конденсаторы', 'индуктивности', 'микропроцессоры', 'микрокомпьютеры'
        ],        

        'Геология' => [
            'геология', 'минералы', 'палеонтология', 'кристаллы', 'ископаемые', 'пласты', 'горные породы', 'седиментология', 'вулканология', 
            'сейсмология', 'геофизика', 'гидрогеология', 'петрология', 'минералогия', 'геохимия', 'геодинамика', 'палеогеография', 'стратиграфия', 
            'геологические карты', 'геологические исследования', 'геологоразведка', 'горное дело', 'нефтегазовая геология', 'геотермия', 
            'геологические экспедиции', 'геологические музеи', 'геологическое наследие', 'геопарки', 'геологические процессы', 'эрозия', 
            'осадочные породы', 'магматические породы', 'метаморфические породы', 'тектоника плит', 'землетрясения', 'геологические структуры', 
            'геологические отложения', 'фоссилии', 'добыча полезных ископаемых', 'геологическое картирование', 'геологические возрасты', 
            'геологические формации', 'геологические явления', 'геологические профили', 'геологические разрезы', 'геологические опросы', 
            'геологические изыскания', 'геологические маршруты', 'геологические экскурсии', 'геологические мониторинги', 'геологические лаборатории', 
            'геологические инструменты', 'геологические методы', 'геологические образцы', 'геологические коллекции', 'геологические архивы', 
            'геологические базы данных', 'геологические публикации', 'геологические исследователи', 'геологические общества', 'геологические конференции', 
            'геологические симпозиумы', 'геологические журналы', 'геологические выставки', 'геологические образовательные программы', 'геологические курсы', 
            'геологические тренинги', 'геологические воркшопы', 'геологические лекции', 'геологические семинары', 'геологические мастер-классы'
        ],        

        'Продажи' => [
            'продажи', 'торговля', 'маркетинг', 'CRM', 'ритейл', 'B2B', 'B2C', 'торговый представитель', 'активные продажи', 'клиентский менеджмент', 
            'технический продавец', 'реклама', 'продвижение', 'бренд', 'мерчандайзинг', 'телемаркетинг', 'прямые продажи', 'оптовые продажи', 
            'розничные продажи', 'сетевой маркетинг', 'переговоры', 'искусство убеждать', 'маркетинг', 'реклама', 'блогер', 'ценообразование', 'market',
            'заголовки', 'как оформлять посты', 'smm', 'таргетированная реклама', 'контекстная реклама', 'сбор данных', 'холодные звонки',
            'Cossa', 'Sostav', 'marketing'
        ],

        'Инженерия' => [
            'инженер', 'техника', 'механика', 'электроника', 'строительство', 'архитектура', 'проектирование', 'CAD', 'автоматизация', 'робототехника', 
            'энергетика', 'машиностроение', 'гражданское строительство', 'промышленное строительство', 'геодезия', 'геология', 'материаловедение', 
            'экологическая инженерия', 'биоинженерия', 'химическая инженерия'
        ],

        'Медицина' => [
            'медицина', 'здравоохранение', 'врач', 'медсестра', 'хирургия', 'терапия', 'педиатрия', 'стоматология', 'кардиология', 'онкология', 
            'неврология', 'психиатрия', 'фармация', 'медицинское оборудование', 'здоровый образ жизни', 'медицинские исследования', 
            'клинические испытания', 'пациентская помощь', 'скорая помощь', 'реабилитация', 'Биохакинг', 'спорт', 'анатомия', 'медиков', 'covid', 
            'covid-19', 'природа'
        ],

        'Образование' => [
            'образование', 'учеба', 'школа', 'высшее образование', 'дошкольное образование', 'преподавание', 'учитель', 'профессор', 'студент', 'курсы', 
            'обучение', 'тренинги', 'семинары', 'вебинары', 'дистанционное обучение', 'e-learning', 'образовательные технологии', 'языковое обучение', 
            'научные исследования', 'академическая деятельность', 'университет', 'букэп', 'читающие', 'познавариум', 'библиотека', 'психология', 
            'настольные игры', 'настольная игра', 'настолки', 'science', 'учим', 'китайский язык', 'А ты знал?', 'СтроюСам', 'cook', 'рецепты', 
            'книга', 'рекордов', 'рекорды', 'книга рекордов', 'гинес', 'гинеса', 'факт', 'лайфхак', 'lifehack', 'study', 'motivation', 'саморазвитие',
            'мысли', 'мыслей', 'school', 'цель', 'цели', 'целях', 'ценности', 'ценностях', 'ценность', 'вики', 'wiki', 'википедиа', 'wikipedia',
            'поэзия', 'Комитет', 'экономический факультет', 'case club', 'RUDN', 'MSU', 'HSE', 'саморазвитие', 'school', 'Erasmus', 'Erasmus+', 
            'student', 'a-student', 'скиф', 'гимназия', 'лицей', 'школа', 'norimyxxxo'
        ],

        'Психология' => [
            'психология', 'психотерапия', 'когнитивная психология', 'эмоциональный интеллект', 'поведенческая психология', 'нейропсихология', 
            'клиническая психология', 'развитие личности', 'психоанализ', 'гештальт-терапия', 'самопознание', 'межличностные отношения', 
            'психология общения', 'социальная психология', 'психология развития', 'детская психология', 'подростковая психология', 
            'психология взрослости', 'психология старения', 'психология труда', 'организационная психология', 'спортивная психология', 
            'позитивная психология', 'психология успеха', 'мотивация', 'самомотивация', 'стресс-менеджмент', 'управление эмоциями', 
            'психология личности', 'психосоматика', 'психологическое консультирование', 'психологическая помощь', 'психологическое тестирование', 
            'психологические тренинги', 'психологические воркшопы', 'психологические лекции', 'психологические семинары', 'психологические мастер-классы', 
            'психологические исследования', 'психологические теории', 'психологические эксперименты', 'психологические практики', 'психологические методы', 
            'психологические подходы', 'психологические стратегии', 'психологические техники', 'психологические инструменты', 'психологические концепции', 
            'психологические модели', 'психологические школы', 'психологические направления', 'психологические традиции', 'психологические исследователи', 
            'психологические общества', 'психологические конференции', 'психологические симпозиумы', 'психологические журналы', 'психологические выставки', 
            'психологические образовательные программы', 'психологические курсы', 'психологические факультеты', 'психологические институты', 
            'психологические лаборатории', 'психологические клиники', 'психологические центры', 'психологические консультации', 'психологические сессии', 
            'психологические встречи', 'психологические группы', 'психологические сообщества', 'психологические форумы', 'психологические блоги', 
            'психологические книги', 'психологические статьи', 'психологические исследования', 'психологические опросы', 'психологические анкеты', 
            'психологические диагностики', 'психологические ассессменты', 'психологические профили', 'психологические портреты', 'психологические кейсы', 
            'психологические сценарии', 'психологические проекты', 'психологические программы', 'психологические инициативы', 'психологические кампании', 
            'психологические акции', 'психологические мероприятия', 'психологические фестивали', 'психологические выступления', 'психологические доклады', 
            'психологические презентации', 'психологические дискуссии', 'психологические дебаты', 'психологические семинары', 'психологические вебинары'
        ],        

        'Музыка' => [
            'концерт', 'рок', 'джаз', 'поп-музыка', 'электронная музыка', 'классическая музыка', 'фестиваль', 'гитара', 'пианино', 'диджей', 
            'музыкальная группа', 'певец', 'певица', 'музыкальное образование', 'музыкальные инструменты', 'вокал', 'опера', 'блюз', 'рэп', 'хип-хоп', 
            'minimal', 'deep', 'techno', 'ea7', 'sound', 'уверенно', 'boulevard depo', 'guf', 'музыка', 'record', 'radio', 'вышел покурить', 'pharaoh', 
            'loqiemean', 'big russian boss', 'хованский', 'хованщина', 'регги', 'soundcloud', 'music', 'fast food music', 'rap', 'hip-hop', 'Zloy SoundCloud', 
            'no bad vibes', 'beats', 'beatz', 'Rihanna', 'Studio 21', 'kizaru', 'ЛСП', 'MC', 'Noize', 'Noize MC', 'Oxxxymiron', 'Gone.Fludd', 'Хаски'
        ],

        'Городские сообщества' => [
            'город', 'местные новости', 'городское событие', 'городская жизнь', 'муниципалитет', 'городской фестиваль', 'городская культура', 
            'общественный транспорт', 'городская инфраструктура', 'городской парк', 'городской туризм', 'городская архитектура', 'городское планирование', 
            'городская экология', 'городское сообщество', 'объявления', 'мвд', 'фсб', 'христианство', 'россия', 'национальные', 'национальный', 'пицца', 
            'китай', 'russia', 'russian', 'china', 'путин', 'информационно-развлекательный портал', 'Инцидент', 'Страйкбол', 'лазертаг', 'протест', 
            'протеста', 'страйкбол в', 'актуально', 'строитель', 'ДТП', 'ЧП', 'мск', 'спб', 'москва', 'санкт-петербург', 'ленинград', 'Питер', 'Петербург'
        ],

        'Машины' => [
            'автомобиль', 'машина', 'автосервис', 'автоспорт', 'автомеханик', 'автодилер', 'автомобильный рынок', 'автозапчасти', 'тюнинг', 'вождение', 
            'автострахование', 'автомобильные гонки', 'автомобильные выставки', 'автомобильные бренды', 'автомобильное обслуживание', 
            'автомобильные технологии', 'электромобили', 'автомобильный дизайн', 'автомобильная безопасность', 'автомобильные дороги', 'тюнинг', 
            'запчасти', 'корч', 'бпан', 'авто рынок', 'приора', 'лада', 'lada', 'бпаn', 'главная дорога', 'tesla', 'тесла', 'электрокар', 'гараж'
        ],

        'Бизнес' => [
            'бизнес', 'предпринимательство', 'стартап', 'бизнес-стратегия', 'маркетинг', 'управление бизнесом', 'бизнес-планирование', 'финансы', 
            'инвестиции', 'бизнес-аналитика', 'бизнес-образование', 'бизнес-сеть', 'бизнес-консалтинг', 'бизнес-события', 'бизнес-инновации', 
            'бизнес-этика', 'малый бизнес', 'семейный бизнес', 'международный бизнес', 'бизнес-процессы'
        ],

        'Компании' => [
            'компания', 'корпорация', 'бизнес-организация', 'коммерческая компания', 'промышленная компания', 'технологическая компания', 
            'финансовая компания', 'маркетинговая компания', 'консалтинговая компания', 'стартап-компания', 'международная компания', 
            'компания-разработчик', 'производственная компания', 'торговая компания', 'компания-поставщик', 'компания-дистрибьютор', 
            'компания-производитель', 'инновационная компания', 'компания-партнер', 'компания-лидер', 'вконтакте', 'durex', 'reddit', 
            'аврора', '808.media', 'хабр', 'tele2', 'apple', 'медуза', 'миф', 'onephrase', 'бургер кинг', 'burger king', 'додо', 'pornhub', 
            'лентач', 'firstvds', 'ruvds', 'opennet', 'опеннет', 'битрикс', 'твич', 'twitch', 'лепра', 'лайфхакер', 'market', 'store',
            'magazine', 'Readovka', 'WILDBERRIES', 'Вайлдберриз', 'ozon', 'Mash', 'Фонтанка', 'KFC', 'ROSTIC\'S', 'AliExpress'
        ],

        'Linux' => [
            'linux', 'manjaro', 'archlinux', 'arch', 'kali', 'fedora', 'astra', 'alt', 'rosa', 'x11', 'wayland', 'GNU', 'GNU/Linux', 
            'операционной системе', 'операционные системы', 'операционная система', 'программное обеспечение', 'kde', 'gnome', 'xfce', 'sway', 
            'i3wm', 'gentoo', 'ubuntu', 'debian', 'opensuse', 'pipewire', 'pulseaudio', 'make', 'linux from scratch'
        ],

        'Подслушано' => ['подслушано'],

        'Мобильные устройства' => [
            'телефон', 'смартфон', 'андроид', 'айфон', 'android', 'iphone', 'ios', 'aosp', 'прошивка', 'прошивки', '4pda'
        ],

        'Программирование' => [
            'программирование', 'кодер', 'кодинг', 'бэкенд', 'php', 'javascript', 'python', 'swift', 'assembler'
        ],

        'Фронтенд' => [
            'фронтенд', 'фронтендер', 'верстка', 'вёрстка', 'верстал', 'верстальщик', 'сверстал', 'сверстаю', 'дизайн', 'интерфейс', 'анимации', 
            'фронт', 'html', 'javascript', 'css', 'vue', 'react', 'node'
        ],

        'Бэкенд' => [
            'php', 'python', 'assembler', 'программист'
        ],

        'Искусство' => [
            'эстет', 'эстетика', 'эстетическое', 'удовольствие', 'аморального', 'аморальное', 'нелогичный', 'нелогичного', 'контроль над культурой', 
            'культура', 'искусство', 'культ', 'культа', 'фоточки', 'фото4ки', 'фотография', 'феля', 'фели', 'картинки', 'красота', 'уютно', 
            'комфортно', 'дзен', 'буддизм', 'искусством', 'видео папка', 'кот', 'коты', 'чего кот орет', 'perfо́rmance', 'куда тёк нил', 'Martadello', 
            'саус парк', 'саус парка', 'южный парк', 'south park', 'кинодоза', 'кино', 'фильмы', 'клинское', 'безалкогольное', 'безалкогольный', 
            'mom\'s spaghetti', 'lostfilm', 'пацаны и чай', 'либреспик', 'прошмандовки', 'китай', 'жизнь', 'стяжки', 'валера', 'валеры', 
            'черт', 'ведьма', 'ведьмы', 'китая', 'компартия', 'Maddyson', 'Шишуне́р', 'ПрАтеин', 'КВН', 'Атеист', 'шутки', 'кайф', 'Urbanturizm', 
            'поэты', 'вумен', 'Грубо? Простите', 'мультики', 'мультфильмы', 'MDK', 'позорно', 'Академия Выдающихся Парней', 
            'Что это за паблик?', 'Злой гений', 'Оптимист', 'эгоист', 'сарказм', 'юмор', 'Темная сторона', 'Страствуйте', 'татуировка', 'тату', 
            'мемы', 'мемов', 'мем', 'смех', 'смеха', 'социопат', 'татуировочка', 'Brodyaga', 'эскиз', 'tatoo', 'эскизы', 'поэт', 'душа', 'анекдоты', 
            'плохие', 'остряк', 'нибиру', 'Мужской клуб', 'дизайн', 'интерьер', 'стопкадр', 'кадр', 'мобильная фотография', 'designa', 'мам ну не читай',
            'медиа', 'pastel', 'hedonism', 'vibe', 'vibes', 'көҥүл сир', 'mzr', 'вкус', 'albertina', 'поэзия', 'Ocean despair', 'nafo$Ya', 'создание чего-то',
            'Сквозь время и пространство', 'путешествия', 'путешествие', 'природа', 'Симпсоны', 'Simpsons', 'ЗШ', 'Женские', 'творчества', 'етономешалка',
            'Пошлые', 'LOOK3', 'Я тебя хочу', 'папины дочки', 'Глеб Мокс', 'ivanfromthetab', 'ocean despair', 'как тебя зовут', 'кино', 'naked movie', 
            'наброски', 'bronze', 'примитив', 'уновис', 'тише', 'другое кино', 'Марат Сафин', 'артнаграда', 'обработка фото', 'photo', 'film', 'pro искусство.', 
            'la luna', 'литературные герои', 'book'
        ],

    ];
    