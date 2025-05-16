import 'package:get/get_navigation/src/routes/get_route.dart';
import 'package:payloadui/bindings/splash_screen_bindings.dart';
import 'package:payloadui/views/auth/registration_screen/register_otp_verify_screen.dart';
import 'package:payloadui/views/auth/log_in_screen/reset_password_screen.dart';
import 'package:payloadui/views/bundle_preview_screen/bundle_preview_screen.dart';
import 'package:payloadui/views/drawer_menu_screen/drawer_menu_mobile_layout_screen.dart';
import 'package:payloadui/views/recharge/recharge_preview_screen.dart';
import 'package:payloadui/views/setting_screen/setting_screen.dart';
import 'package:payloadui/views/add_money/add_money_preview_screen.dart';
import 'package:payloadui/views/add_money/add_money_screen.dart';
import '../views/auth/forgot_password/forgot_password_otp_verify_Screen.dart';
import '../views/auth/forgot_password/forgot_password_screen.dart';
import '../views/auth/log_in_screen/log_in_screen.dart';
import '../views/auth/log_in_screen/otp_verification_screen.dart';
import '../views/auth/log_in_screen/twofa_verification_screen.dart';
import '../views/auth/registration_screen/registration_screen.dart';
import '../views/change_password_screen/change_password_screen.dart';
import '../views/gift_card/add_gift_card_screen.dart';
import '../views/gift_card/gift_card_oder_screen.dart';
import '../views/gift_card/gift_card_screen.dart';
import '../views/history_screen/history_screen.dart';
import '../views/kyc_verification_screen/kyc_verification_screen.dart';
import '../views/navigation_screen/navigation_screen.dart';
import '../views/onboard_screen/onboard_screen.dart';
import '../views/profile/update_profile_screen/update_profile_screen.dart';
import '../views/recharge/manual_gateway_recharge_screen.dart';
import '../views/service/data_bundle/data_bundles_screen/data_bundles_screen.dart';
import '../views/splash_screen/splash_screen.dart';
import '../views/two_fa_otp_verify/two_fa_otp_verify_screen.dart';
part '../routes/route_pages.dart';

class Routes {
  static var list = RoutePageList.list;

  static const String splashScreen = '/splash_screen';
  static const String onboardScreen = '/onboard_screen';
  static const String signInScreen = '/sign_in_screen';
  static const String otpVerificationScreen = '/otp_verification_screen';
  static const String navigationScreen = '/navigation_screen';
  static const String offersScreen = '/offers_screen';
  static const String offerDetailsScreen = '/offer_details_screen';
  static const String servicesScreen = '/services_screen';
  static const String giftCardScreen = '/gift_card_screen';
  static const String flexiPlanScreen = '/flexiplan_screen';
  static const String historyScreen = '/purchase_history_screen';
  static const String registrationScreen = '/registration_screen';
  static const String dataBundlesScreen = '/data_bundles_screen';
  static const String rechargePreviewScreen = '/recharge_preview_screen';
  static const String addGiftCardScreen = '/add_gift_card_screen';
  static const String giftCardDetailsScreen = '/gift_card_details_screen';
  static const String walletRechargeScreen = '/wallet_recharge_screen';
  static const String changePasswordScreen = '/change_password_screen';
  static const String twofaVerificationScreen = '/twofa_verification_screen';
  static const String kycVerificationScreen = '/kyc_verification_screen';
  static const String forgotPasswordScreen = '/forgot_password_screen';
  static const String rechargeHistoryScreen = '/recharge_history_screen';
  static const String kycPendingOrVerifyScreen =
      '/kyc_pending_or_verify_screen';
  static const String updateProfileScreen = '/update_profile_screen';
  static const String forgotPasswordOtpVerifyScreen =
      '/forgot_password_otp_verify_screen';
  static const String resetPasswordScreen = '/reset_password_screen';
  static const String walletRechargePreviewScreen =
      '/wallet_recharge_preview_screen';
  static const String webViewScreen = '/web_view_screen';
  static const String manualGatewayRechargeScreen =
      '/manual_gateway_recharge_screen';
  static const String congratulationScreen = '/congratulation_screen';
  static const String dowerScreen = '/drawer_menu_screen';
  static const String twoFaOtpVerifyScreen = '/two_fa_otp_verify_screen';
  static const String settingScreen = '/setting_screen';
  static const String registerOtpVerifyScreen = '/register_otp_verify_screen';
  static const String bundlePreview = '/bundle_preview_screen';
}
