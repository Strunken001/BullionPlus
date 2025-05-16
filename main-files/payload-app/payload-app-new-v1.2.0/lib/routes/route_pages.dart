part of '../routes/routes.dart';

class RoutePageList {
  static var list = [
    GetPage(
      name: Routes.splashScreen,
      page: () => SplashScreen(),
      binding: SplashBinding(),
    ),
    GetPage(
      name: Routes.onboardScreen,
      page: () => const OnboardScreen(),
    ),
    GetPage(
      name: Routes.signInScreen,
      page: () => const LogInScreen(),
    ),
    GetPage(
      name: Routes.otpVerificationScreen,
      page: () => const OtpVerificationScreen(),
    ),
    GetPage(
      name: Routes.navigationScreen,
      page: () => NavigationScreen(),
    ),
    GetPage(
      name: Routes.registrationScreen,
      page: () => const RegistrationScreen(),
    ),
    GetPage(
      name: Routes.dataBundlesScreen,
      page: () => const DataBundlesScreen(),
    ),
    GetPage(
      name: Routes.rechargePreviewScreen,
      page: () => const RechargePreviewScreen(),
    ),
    GetPage(
      name: Routes.giftCardScreen,
      page: () => const GiftCardScreen(),
    ),
    GetPage(
      name: Routes.addGiftCardScreen,
      page: () => const GiftCardSelectScreen(),
    ),
    GetPage(
      name: Routes.giftCardDetailsScreen,
      page: () => const GiftCardDetailsScreen(),
    ),
    GetPage(
      name: Routes.walletRechargeScreen,
      page: () => const AddMoneyScreen(),
    ),
    GetPage(
      name: Routes.changePasswordScreen,
      page: () => const ChangePasswordScreen(),
    ),
    GetPage(
      name: Routes.twofaVerificationScreen,
      page: () => const TwofaVerificationScreen(),
    ),
    GetPage(
      name: Routes.kycVerificationScreen,
      page: () => const KycVerificationScreen(),
    ),
    GetPage(
      name: Routes.historyScreen,
      page: () => const HistoryScreen(),
    ),
    GetPage(
      name: Routes.forgotPasswordScreen,
      page: () => const ForgotPasswordScreen(),
    ),
    GetPage(
      name: Routes.updateProfileScreen,
      page: () => const UpdateProfileScreen(),
    ),
    GetPage(
      name: Routes.forgotPasswordOtpVerifyScreen,
      page: () => const ForgotPasswordOtpVerifyScreen(),
    ),
    GetPage(
      name: Routes.resetPasswordScreen,
      page: () => const ResetPasswordScreen(),
    ),
    GetPage(
      name: Routes.walletRechargePreviewScreen,
      page: () => const AddMoneyPreviewScreen(),
    ),
    GetPage(
      name: Routes.manualGatewayRechargeScreen,
      page: () => const ManualGatewayRechargeScreen(),
    ),
    GetPage(
      name: Routes.dowerScreen,
      page: () => MyDrawerMenu(),
    ),
    GetPage(
      name: Routes.twoFaOtpVerifyScreen,
      page: () => const TwoFaOtpVerifyScreen(),
    ),
    GetPage(
      name: Routes.settingScreen,
      page: () => const SettingScreen(),
    ),
    GetPage(
      name: Routes.registerOtpVerifyScreen,
      page: () => const RegisterOtpVerifyScreen(),
    ),
    GetPage(
      name: Routes.bundlePreview,
      page: () => const BundlePreviewScreen(),
    ),
  ];
}
