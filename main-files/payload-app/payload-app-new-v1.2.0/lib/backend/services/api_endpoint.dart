import 'package:payloadui/extensions/custom_extensions.dart';

class ApiEndpoint {
  static const String mainDomain = "PUT-YOUR-OWN-DOMAIN";
  static const String baseUrl = "$mainDomain/api/v1";

  //-> Auth
  static String registerURL = '/register'.addBaseURl();
  static String loginWithPasswordAndOtpURL = '/login'.addBaseURl();
  static String loginOtpVerifyURL = '/login/verification?user='.addDBaseURl();
  static String forgotPasswordSendOtpURL =
      '/password/forgot/find/user'.addBaseURl();
  static String forgotPasswordOtpVerifyURL =
      '/password/forgot/verify/code'.addBaseURl();
  static String resetPasswordURL = '/password/forgot/reset'.addBaseURl();
  static String resendCodeURL =
      '/password/forgot/resend/code?token='.addDBaseURl();
  static String resendLoginOtp =
      '/login/verification/code/resend?user='.addDBaseURl();
  static String passwordUpdateURL =
      '/user/profile/password/update'.addBaseURl();
  static String logOutURL = '/user/logout'.addBaseURl();
  static String registerSendOtp = '/authorize/mobile/send/code'.addBaseURl();
  static String mobileOtpVerify = '/authorize/mobile/verify/code'.addBaseURl();
  static String resendCodeMobileOtpURL =
      '/authorize/mobile/resend/code?token='.addDBaseURl();

  //=> Basic Setting
  static String countryListURL = '/settings/country'.addBaseURl();
  static String basicSettingURL =
      '/settings/basic-settings?lang=es'.addDBaseURl();
  static String languageURl = '/settings/languages?lang='.addDBaseURl();

  //=> Profile
  static String profileInfoGetURL = '/user/profile/info'.addBaseURl();
  static String profileUpdateURL = '/user/profile/info/update'.addDBaseURl();
  static String deleteProfile = '/user/profile/delete-account'.addBaseURl();

  //=> Add Money
  static String paymentGateWayURL =
      '/user/add-money/payment-gateways'.addBaseURl();
  static String automaticPaymentURL =
      '/user/add-money/automatic/submit'.addBaseURl();
  static String manualPaymentURL =
      '/user/add-money/manual/input-fields?alias='.addDBaseURl();
  static String manualPaymentConfirmURL =
      '/user/add-money/manual/submit'.addBaseURl();

  //=> Gift Card
  static String myGiftCardURL = '/user/gift-card/'.addDBaseURl();
  static String allGiftCardURL = '/user/gift-card/all'.addDBaseURl();
  static String giftCardSearchURL =
      '/user/gift-card/search/?country='.addDBaseURl();
  static String giftCardOrderURL = '/user/gift-card/order'.addDBaseURl();
  static String giftCardDetailsURL =
      '/user/gift-card/details/?product_id='.addDBaseURl();

  //=> Two fa
  static String twoFAUpdateURL =
      '/authorize/google/2fa/status-update'.addDBaseURl();
  static String twoFAInfoURL = '/authorize/google/2fa/status'.addDBaseURl();
  static String twoFAVerifyURL = '/authorize/google/2fa/verify'.addDBaseURl();

  //=>  Mobile Recharge
  static String topUpDetectOperator =
      '/user/mobile-topup/automatic/check-operator?'.addDBaseURl();
  static String topUpPayConfirmedURL =
      '/user/mobile-topup/automatic/pay'.addDBaseURl();

  // Dashboard
  static String userDashboardURL = '/user/dashboard'.addBaseURl();
  static String transactionHistoryURL = '/user/transaction/log'.addBaseURl();

  //=>  KYC
  static String kycInfoURL = '/authorize/kyc/input-fields'.addDBaseURl();
  static String kycSubmitURL = '/authorize/kyc/submit'.addDBaseURl();

  static String getOperatorInfo =
      '/user/data-bundle/get/operators?country_code='.addDBaseURl();

  static String getChargesInfo =
      '/user/data-bundle/get/bundle/charges'.addDBaseURl();

  static String buyBundle = '/user/data-bundle/get/bundle/buy'.addDBaseURl();
}
