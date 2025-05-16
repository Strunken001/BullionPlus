import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/auth/otp_verification/otp_verification_controller.dart';
import 'package:payloadui/controller/auth/register/register_screen_controller.dart';
import 'package:payloadui/widgets/common/appbar/custom_top_appbar_widget.dart';
import 'package:payloadui/widgets/common/buttons/primary_button_widget.dart';
import 'package:pin_code_fields/pin_code_fields.dart';
import '../../../custom_assets/assets.gen.dart';
import '../../../languages/strings.dart';
import '../../../widgets/common/text_labels/title_heading3_widget.dart';
import '../../utils/custom_color.dart';
import '../../utils/dimensions.dart';
import '../../utils/size.dart';

class OtpVerificationMobileLayoutScreen extends StatelessWidget {
  OtpVerificationMobileLayoutScreen({super.key});

  final _controller = Get.put(OtpVerificationController());
  final controller = Get.put(RegisterController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _bodyWidget(context),
    );
  }

  _bodyWidget(context) {
    return Padding(
      padding:
          EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
      child: Column(
        mainAxisAlignment: mainSpaceBet,
        children: [
          Column(
            children: [
              topAppbarAndTexWidget(),
              PinCodeTextField(
                cursorColor: CustomColor.primaryLightColor,
                keyboardType: TextInputType.number,
                appContext: context,
                length: 6,
                controller: _controller.otpController,
                pinTheme: PinTheme(
                    inactiveColor: CustomColor.greyColor.withOpacity(0.5),
                    activeColor: CustomColor.primaryLightColor),
              ),
              _buttonWidget(),
              _resendCodeTextWidget()
            ],
          ),
        ],
      ),
    );
  }

  topAppbarAndTexWidget() {
    String userNumber = _controller.controller.otpNumberController.text.isEmpty
        ? controller.mobileController.text
        : _controller.controller.otpNumberController.text;
    String code = _controller.controller.mobileCode.value.isEmpty
        ? controller.phoneCode.value
        : _controller.controller.mobileCode.value;
    return Column(
      children: [
        TopAppBarWidget(
          icon: Icons.arrow_back,
          imagePath: Assets.logo.brandLogo.path,
        ),
        Obx(
          () => TitleHeading3Widget(
            text:
                "${DynamicLanguage.key(Strings.enterTheVerificationCodeWeSend)} $code$userNumber",
            fontWeight: FontWeight.w400,
          ),
        )
      ],
    );
  }

  _buttonWidget() {
    return Obx(
      () => _controller.isLoading
          ? const CustomLoadingAPI()
          : PrimaryButtonWidget(
              buttonText: Strings.continues,
              onPressed: () {
                _controller.loginOtpVerifyProcess();
              },
            ),
    );
  }

  _resendCodeTextWidget() {
    return Column(
      children: [
        TitleHeading3Widget(
          text: DynamicLanguage.key(
            Strings.didntGetCode,
          ),
          fontWeight: FontWeight.w500,
          fontSize: Dimensions.headingTextSize4,
          color: CustomColor.greyColor,
        ),
        Obx(() {
          return InkWell(
            onTap: _controller.enableResend.value
                ? () {
                    _controller.resendCode();
                    _controller.timerInit();
                    _controller.getResendLoginCode();
                    _controller.otpController.clear();
                  }
                : null,
            child: Text(
              _controller.enableResend.value
                  ? "Resend Code"
                  : "Resend in ${_controller.secondsRemaining.value}s",
              style: TextStyle(
                color:
                    _controller.enableResend.value ? Colors.blue : Colors.grey,
              ),
            ),
          );
        }),
      ],
    );
  }
}
