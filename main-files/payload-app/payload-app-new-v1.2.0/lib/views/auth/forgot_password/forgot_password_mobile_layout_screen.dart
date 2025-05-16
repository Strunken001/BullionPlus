import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/auth/forgot_password/forgot_password_controller.dart';
import 'package:payloadui/custom_assets/assets.gen.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/appbar/custom_top_appbar_widget.dart';
import 'package:payloadui/widgets/common/buttons/primary_button_widget.dart';
import 'package:payloadui/widgets/common/inputs/primary_input_filed.dart';
import 'package:payloadui/widgets/common/text_labels/title_sub_title_widget.dart';
import '../../../languages/strings.dart';

class ForgotPasswordMobileLayoutScreen extends StatelessWidget {
  ForgotPasswordMobileLayoutScreen({super.key});

  final _controller = Get.put(ForgotPasswordController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: bodyWidget(),
    );
  }

  bodyWidget() {
    return Padding(
      padding:
          EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
      child: Form(
        key: _controller.formKey,
        child: Column(
          children: [
            topAppbarAndTexWidget(),
            PrimaryInputWidget(
                keyboardType: TextInputType.number,
                controller: _controller.numberController,
                hint: "Full number"),
            _buttonAndTexWidget()
          ],
        ),
      ),
    );
  }

  _buttonAndTexWidget() {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: Dimensions.heightSize),
      child: Obx(
        () => _controller.isLoading
            ? const CustomLoadingAPI()
            : PrimaryButtonWidget(
                buttonText: Strings.sendOTP,
                onPressed: () {
                  if (_controller.formKey.currentState!.validate()) {
                    _controller.forgotPasswordProcess();
                  }
                },
              ),
      ),
    );
  }

  topAppbarAndTexWidget() {
    return Column(
      children: [
        TopAppBarWidget(
          icon: Icons.arrow_back,
          imagePath: Assets.logo.brandLogo.path,
        ),
        TitleSubTitleWidget(
          isCenterText: true,
          title: Strings.resetYourForgottenPassword,
          subTitle: Strings.takeControlOfYourAccountByResettingYourPassword,
          titleFontSize: Dimensions.headingTextSize3,
          fontWeight: FontWeight.w500,
          subTitleFontSize: Dimensions.headingTextSize6,
        )
      ],
    );
  }
}
