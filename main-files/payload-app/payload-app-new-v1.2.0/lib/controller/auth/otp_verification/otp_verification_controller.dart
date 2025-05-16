import 'dart:async';

import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/model/auth/otp_verify/login_otp_verify_model.dart';
import 'package:payloadui/backend/utils/custom_snackbar.dart';

import '../../../backend/local_storage/local_storage.dart';
import '../../../backend/model/auth/sign_in/resend_otp_model.dart';
import '../../../backend/services/auth/auth_api_service.dart';
import '../../../backend/utils/api_method.dart';
import '../../../languages/strings.dart';
import '../../../routes/routes.dart';
import '../login/log_in_screen_controller.dart';

class OtpVerificationController extends GetxController {
  final otpController = TextEditingController();
  final controller = Get.put(LogInController());

  RxInt twoFaStatus = 0.obs;

  RxInt secondsRemaining = 59.obs;
  RxBool enableResend = true.obs;
  Timer? timer;

  @override
  void onClose() {
    timer?.cancel();
    super.onClose();
  }

  timerInit() {
    timer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (secondsRemaining.value > 0) {
        secondsRemaining.value--;
      } else {
        enableResend.value = true;
        timer?.cancel();
      }
    });
  }

  resendCode() {
    secondsRemaining.value = 59;
    enableResend.value = false;
  }

//Resend Code Timer ConDown End line

  final _isLoading = false.obs;

  bool get isLoading => _isLoading.value;

  late LoginOtpVerifyModel _loginOtpVerifyModel;

  LoginOtpVerifyModel get loginOtpVerifyModel => _loginOtpVerifyModel;

  Future<LoginOtpVerifyModel> loginOtpVerifyProcess() async {
    _isLoading.value = true;
    update();

    Map<String, dynamic> inputBody = {
      'otp': otpController.text,
    };

    await AuthApiServices.loginOtpVerifyApiProcess(
            body: inputBody,
            userId: controller.userId.value,
            type: controller.type.value)
        .then((value) {
      _loginOtpVerifyModel = value!;
      twoFaStatus.value = _loginOtpVerifyModel.data.userInfo.twoFactorStatus;
      _isLoading.value = false;
      LocalStorage.saveToken(token: _loginOtpVerifyModel.data.token);

      if (twoFaStatus.value == 1) {
        Get.toNamed(Routes.twoFaOtpVerifyScreen);
      } else {
        Get.offAllNamed(Routes.navigationScreen);
      }

      update();
    }).catchError((onError) {
      log.e(onError);
    });

    _isLoading.value = false;
    update();
    return _loginOtpVerifyModel;
  }

  late ResendLoginOtpModel _resendLoginOtpModel;

  ResendLoginOtpModel get resendLoginOtpModel => _resendLoginOtpModel;

  Future<ResendLoginOtpModel> getResendLoginCode() async {
    try {
      final value = await AuthApiServices.getResendLoginOtpCode(
          controller.userId.value.toString());
      if (value != null) {
        _resendLoginOtpModel = value;
      } else {
        log.e('Failed to fetch Resend Code data: Data is null.');
      }
    } catch (onError) {
      log.e(onError);
    } finally {
      CustomSnackBar.success(Strings.otpResendSuccessful);
    }
    return _resendLoginOtpModel;
  }
}
