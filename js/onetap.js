// Извлечение параметров из URL
const urlParams = new URLSearchParams(window.location.search);
const payload = urlParams.get('payload');
const state = urlParams.get('state');

if (payload) {
    try {
        const payloadData = JSON.parse(decodeURIComponent(payload));
        const silentToken = payloadData.token;
        const uuid = payloadData.uuid;
        let reqModal = new bootstrap.Modal(document.getElementById('reqModal'), {
            keyboard: false,
            backdrop: 'static'
        });

        // Отправка данных на сервер
        fetch('/api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `silent_token=${silentToken}&uuid=${uuid}`
        })
        .then(response => response.json())
        .then(data => {
            // Обработка ответа от сервера
            console.log(data);
            // Добавляем задержку перед открытием модального окна
              setTimeout(() => {
                reqModal.show();
            }, 1500); // Задержка в 2 секунды
            // Открываем анализ профиля пользователя
            renderProfile(data);
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    } catch (e) {
        console.error('Ошибка при парсинге payload:', e);
    }
}


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
            // Открываем анализ профиля пользователя
            console.log(data);
            renderProfile(data);

        });
        return;
      // Для этих событий нужно открыть полноценный VK ID чтобы
      // пользователь дорегистрировался или подтвердил телефон
      case window.SuperAppKit.ConnectEvents.OneTapAuthEventsSDK.FULL_AUTH_NEEDED: //  = 'VKSDKOneTapAuthFullAuthNeeded'
      case window.SuperAppKit.ConnectEvents.OneTapAuthEventsSDK.PHONE_VALIDATION_NEEDED: // = 'VKSDKOneTapAuthPhoneValidationNeeded'
      case window.SuperAppKit.ConnectEvents.ButtonOneTapAuthEventsSDK.SHOW_LOGIN: // = 'VKSDKButtonOneTapAuthShowLogin'
        // url - строка с url, на который будет произведён редирект после авторизации.
        // state - состояние вашего приложение или любая произвольная строка, которая будет добавлена к url после авторизации.
        console.log('Пытаюсь авторизовать пользователя через VK ID... (VKSDKButtonOneTapAuthShowLogin)');
        return window.SuperAppKit.Connect.redirectAuth({ url: 'https://ata.poohprod.ru', state: 'dj29fnsadjsd82qwe'});
        // Пользователь перешел по кнопке "Войти другим способом"
      case window.SuperAppKit.ConnectEvents.ButtonOneTapAuthEventsSDK.SHOW_LOGIN_OPTIONS: // = 'VKSDKButtonOneTapAuthShowLoginOptions'
        // Параметр screen: phone позволяет сразу открыть окно ввода телефона в VK ID
        // Параметр url: ссылка для перехода после авторизации. Должен иметь https схему. Обязательный параметр.
        console.log('Пытаюсь авторизовать пользователя через VK ID... (VKSDKButtonOneTapAuthShowLoginOptions)');
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
