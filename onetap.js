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
 
const oneTapButton = window.SuperAppKit.Connect.floatingOneTapAuth({
  callback: (event) => {
    const { type } = event;

    if (!type) {
      return;
    }

    switch (type) {
      case ConnectEvents.OneTapAuthEventsSDK.LOGIN_SUCCESS:
        return console.info(event);
      default:
        // Обработка остальных событий.
    }

    return;
  },
  options: {
    styles: {
      zIndex: 999,
    },
    skipSuccess: false,
  },
});

if (oneTapButton) {
  document.body.appendChild(oneTapButton.getFrame());
}
