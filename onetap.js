window.SuperAppKit.Config.init({
  appId: 51811999, // Тут нужно подставить ID своего приложения.

  appSettings: {
    agreements: '',
    promo: '',
    vkc_behavior: '',
    vkc_auth_action: '',
    vkc_brand: '',
    vkc_display_mode: '',
  },
});

 
const oneTapButton = window.SuperAppKit.Connect.buttonOneTapAuth({
  callback: (event) => {
    const { type } = event;

    if (!type) {
      return;
    }

    switch (type) {
      case window.SuperAppKit.ConnectEvents.OneTapAuthEventsSDK.LOGIN_SUCCESS: // = 'VKSDKOneTapAuthLoginSuccess'
        
        // console.log(event);
        console.log('Пытаюсь авторизовать пользователя через VK ID...');

        let form = document.getElementById('authForm');
        let loading = document.getElementById('loading');
        let profile = document.getElementById('profile_refresh_global');
        let profile_interface = document.getElementById('profile');
        let reqModal = new bootstrap.Modal(document.getElementById('reqModal'), {
            keyboard: false,
            backdrop: 'static'
        });

        // Добавляем задержку перед открытием модального окна
          setTimeout(() => {
            reqModal.show();
        }, 1500); // Задержка в 2 секунды

        form.style.display = 'none';
        loading.style.display = 'block';

        // Отправка запроса к API
        fetch('/api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'silent_token=' + event.payload.token + '&uuid=' + event.payload.uuid
        })
        .then(response => response.json())
        .then(data => {
            // Обработка ответа от API
            console.log(data);
            //console.log('access_token: ' + data.response.access_token);
            //console.log('user_id: ' + data.response.user_id);
            // Отправка запроса к API
            fetch('/api/get_user.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'access_token=' + data.response.access_token + '&username_to_watch=' + data.response.user_id
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);

                let responseData = data; // Разбор JSON-ответа
                const matchingGroups = findMatchingGroups(responseData.subscriptions, responseData.reposts);

                let contentHTML = '<h4 class="text-center py-5"><div class="spinner-grow spinner-grow-sm" role="status"><span class="visually-hidden"></span></div> обновляю список рекомендаций...</h4>';

                contentHTML += '<div class="d-flex flex-row overflow-auto">';
                contentHTML += createUserInfoSection(responseData.user_info);
                contentHTML += createListSection('Подписки', responseData.subscriptions, 'col-12 col-lg-4', matchingGroups);
                contentHTML += createListSection('Репосты', responseData.reposts, 'col-12 col-lg-4', matchingGroups, false);
                contentHTML += '</div>';

                profile.classList.remove("form-signin");
                // Очищаем и добавляем новое содержимое в profile_interface
                profile_interface.innerHTML = contentHTML;

                let ctx = document.getElementById('myChart').getContext('2d');
                myChart = new Chart(ctx, {
                    type: 'pie', // или 'doughnut' для кольцевой диаграммы
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Подписок из категории',
                            data: [],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(111, 20, 55, 0.2)',
                                'rgba(88, 77, 66, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(111, 20, 55, 1)',
                                'rgba(88, 77, 66, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false
                    }
                });
                
                // Скрываем анимацию загрузки и показываем profile_interface
                loading.style.display = 'none';
                profile_interface.style.display = 'block';

                // Обновляем категории у подписок
                function processGroup(index, groups, callback) {
                    let groupIds = Object.keys(groups); // Получаем массив ключей (ID групп) из объекта
                    if (index < groupIds.length-1) { // здесь -1 для того чтобы пропустить массив order
                        loadCategory(groupIds[index]);
                        setTimeout(() => processGroup(index + 1, groups, callback), 1000);
                    } else if (callback) {
                        callback(); // Вызов callback после завершения всех итераций
                    }
                }

                function processReposts(index, reposts, callback) {
                    // Проверяем, является ли reposts объектом
                    if (typeof reposts === 'object' && reposts !== null) {
                        let repostIds = Object.keys(reposts); // Получаем массив ключей (ID групп) из объекта
                        if (index < repostIds.length) {
                            if (repostIds[index] !== 'order') { // Пропускаем ключ 'order', если он есть
                                getGroup(repostIds[index]); // Предполагается, что функция getGroup определена
                            }
                            setTimeout(() => processReposts(index + 1, reposts, callback), 1000);
                        } else if (callback) {
                            callback(); // Вызов callback после завершения всех итераций
                        }
                    } else if (typeof reposts === 'string') {
                        // Обработка случая, когда reposts - это строка
                        console.error('Анализ: Не найдено ни одного репоста на стене пользователя, пропускаю.');
                        if (callback) {
                            callback();
                        }
                    }
                }

                // Сначала обрабатываем репосты, затем подписки
                processReposts(0, responseData.reposts, function() {
                    processGroup(0, responseData.subscriptions);
                });


            })
            .catch(error => {
                console.error('Ошибка:', error);
                errorUserNotFound(profile_interface);
            });

        });
        return;
      // Для этих событий нужно открыть полноценный VK ID чтобы
      // пользователь дорегистрировался или подтвердил телефон
      case window.SuperAppKit.ConnectEvents.OneTapAuthEventsSDK.FULL_AUTH_NEEDED: //  = 'VKSDKOneTapAuthFullAuthNeeded'
      case window.SuperAppKit.ConnectEvents.OneTapAuthEventsSDK.PHONE_VALIDATION_NEEDED: // = 'VKSDKOneTapAuthPhoneValidationNeeded'
      case window.SuperAppKit.ConnectEvents.ButtonOneTapAuthEventsSDK.SHOW_LOGIN: // = 'VKSDKButtonOneTapAuthShowLogin'
        // url - строка с url, на который будет произведён редирект после авторизации.
        // state - состояние вашего приложение или любая произвольная строка, которая будет добавлена к url после авторизации.
        return window.SuperAppKit.Connect.redirectAuth({ url: 'https://ata.poohprod.ru', state: 'dj29fnsadjsd82qwe'});
        // Пользователь перешел по кнопке "Войти другим способом"
      case window.SuperAppKit.ConnectEvents.ButtonOneTapAuthEventsSDK.SHOW_LOGIN_OPTIONS: // = 'VKSDKButtonOneTapAuthShowLoginOptions'
        // Параметр screen: phone позволяет сразу открыть окно ввода телефона в VK ID
        // Параметр url: ссылка для перехода после авторизации. Должен иметь https схему. Обязательный параметр.
        return window.SuperAppKit.Connect.redirectAuth({ screen: 'phone', url: 'https://ata.poohprod.ru' });
    }

    return;
  },
  // Не обязательный параметр с настройками отображения OneTap
  options: {
    showAlternativeLogin: false,
    showAgreements: true,
    displayMode: 'default',
    langId: 0,
    buttonSkin: 'flat',
    buttonStyles: {
      borderRadius: 8,
      height: 50,
    },
  },
});

// Получить iframe можно с помощью метода getFrame()
const oneTapButtonContainer = document.getElementById('vkOneTapButtonContainer');
if (oneTapButton && oneTapButtonContainer) {
    oneTapButtonContainer.appendChild(oneTapButton.getFrame());
}

// Удалить iframe можно с помощью OneTapButton.destroy();
