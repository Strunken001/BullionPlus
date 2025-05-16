import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:payloadui/routes/routes.dart';
import '../../../backend/model/common/common_success_model.dart';
import '../../../backend/services/auth/auth_api_service.dart';
import '../../../backend/utils/api_method.dart';

class TwoFaOtpController extends GetxController {
  final otpController = TextEditingController();

  ///------------------------------- TWO FA OTP VERIFY PROCESS ---------------------------------------
  final _isLoading = false.obs;

  bool get isLoading => _isLoading.value;

  late CommonSuccessModel _twoFaVerifyModel;

  CommonSuccessModel get twoFaVerifyModel => _twoFaVerifyModel;

  Future<CommonSuccessModel> twoFaVerifyProcess() async {
    _isLoading.value = true;
    update();

    Map<String, dynamic> inputBody = {
      'code': otpController.text,
    };

    await AuthApiServices.twoFaVerify(
      body: inputBody,
    ).then((value) {
      _twoFaVerifyModel = value!;
      _isLoading.value = false;
      Get.offAllNamed(Routes.navigationScreen);

      update();
    }).catchError((onError) {
      log.e(onError);
    });

    _isLoading.value = false;
    update();
    return _twoFaVerifyModel;
  }
}
