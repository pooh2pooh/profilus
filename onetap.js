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
        console.log(event);
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
