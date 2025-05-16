import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/auth/register/register_otp_verify_controller.dart';
import 'package:pin_code_fields/pin_code_fields.dart';
import '../../../controller/auth/login/log_in_screen_controller.dart';
import '../../../custom_assets/assets.gen.dart';
import '../../../languages/strings.dart';
import '../../../widgets/common/appbar/custom_top_appbar_widget.dart';
import '../../../widgets/common/buttons/primary_button_widget.dart';
import '../../../widgets/common/text_labels/title_heading3_widget.dart';
import '../../utils/custom_color.dart';
import '../../utils/dimensions.dart';
import '../../utils/size.dart';

class RegisterOtpVerifyMobileLayoutScreen extends StatelessWidget {
  RegisterOtpVerifyMobileLayoutScreen({super.key});

  final controller = Get.put(RegisterOtpVerifyController());
  final signInController = Get.put(LogInController());

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
                controller: controller.otpController,
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
    String userNumber = signInController.numberController.text.isEmpty
        ? controller.controller.mobileController.text
        : signInController.numberController.text;
    return Column(
      children: [
        TopAppBarWidget(
          icon: Icons.arrow_back,
          imagePath: Assets.logo.brandLogo.path,
        ),
        TitleHeading3Widget(
          text:
              "${DynamicLanguage.key(Strings.enterTheVerificationCodeWeSend)} $userNumber",
          fontWeight: FontWeight.w400,
        ),
      ],
    );
  }

  _buttonWidget() {
    return Obx(
      () => controller.isLoading
          ? const CustomLoadingAPI()
          : PrimaryButtonWidget(
              buttonText: Strings.continues,
              onPressed: () {
                controller.verifyMobileCodeProcess();
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
            onTap: controller.enableResend.value
                ? () {
                    controller.timerStart();
                    controller.timerInit();
                    controller.getResendMobileCode();
                    controller.otpController.clear();
                  }
                : null,
            child: Text(
              DynamicLanguage.isLoading
                  ? ""
                  : DynamicLanguage.key(Strings.resendCode),
              style: TextStyle(
                color:
                    controller.enableResend.value ? Colors.blue : Colors.grey,
              ),
            ),
          );
        }),
      ],
    );
  }
}
