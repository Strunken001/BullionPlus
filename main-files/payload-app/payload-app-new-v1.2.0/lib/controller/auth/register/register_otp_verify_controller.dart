import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/model/common/common_success_model.dart';
import 'package:payloadui/controller/auth/register/register_screen_controller.dart';
import '../../../backend/services/add_money/add_money_api_service.dart';
import '../../../backend/services/auth/auth_api_service.dart';
import '../../../routes/routes.dart';

class RegisterOtpVerifyController extends GetxController {
  final otpController = TextEditingController();
  final controller = Get.put(RegisterController());

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

  timerStart() {
    secondsRemaining.value = 59;
    enableResend.value = false;
  }

  final _isLoading = false.obs;

  bool get isLoading => _isLoading.value;

  late CommonSuccessModel _registerOtpVerifyModel;

  CommonSuccessModel get registerOtpVerifyModel => _registerOtpVerifyModel;

  Future<CommonSuccessModel> verifyMobileCodeProcess() async {
    _isLoading.value = true;
    update();

    Map<String, dynamic> inputBody = {
      'token': controller.sendCodeToken.value,
      'code': otpController.text,
    };

    await AuthApiServices.verifyMobileCode(body: inputBody).then((value) {
      _registerOtpVerifyModel = value!;
      _isLoading.value = false;
      Get.offNamed(Routes.navigationScreen);
      update();
    }).catchError((onError) {
      log.e(onError);
    });
    _isLoading.value = false;
    update();
    return _registerOtpVerifyModel;
  }

  late CommonSuccessModel _resendMobileCodeModel;

  CommonSuccessModel get resendMobileCodeModel => _resendMobileCodeModel;

  Future<CommonSuccessModel> getResendMobileCode() async {
    try {
      final value = await AuthApiServices.getResendMobileCode(
          controller.sendCodeToken.value);
      if (value != null) {
        _resendMobileCodeModel = value;
      } else {
        log.e('Failed to fetch Resend Code data: Data is null.');
      }
    } catch (onError) {
      log.e(onError);
    } finally {}
    return _resendMobileCodeModel;
  }
}
