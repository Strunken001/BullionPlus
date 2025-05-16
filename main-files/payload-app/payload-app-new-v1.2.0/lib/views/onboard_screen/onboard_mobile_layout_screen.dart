import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/local_storage/local_storage.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/basic_setting/basic_setting_controller.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/size.dart';
import 'package:payloadui/widgets/common/buttons/primary_button.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading5_widget.dart';
import '../../languages/language_dropdown_widget.dart';
import '../../languages/strings.dart';
import '../../web_view/web_view_screen.dart';
import '../../widgets/common/appbar/primary_appbar.dart';
import '../utils/dimensions.dart';

class OnboardMobileLayoutScreen extends StatelessWidget {
  OnboardMobileLayoutScreen({super.key});

  final controller = Get.put(BasicSettingController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _appbarWidget(),
      body: Obx(
        () => controller.isLoading
            ? const CustomLoadingAPI()
            : _bodyWidget(context),
      ),
    );
  }

  _appbarWidget() {
    return PrimaryAppBar(
      toolbarHeight: Dimensions.appBarHeight,
      appbarSize: Dimensions.appBarHeight * 0.7,

      Strings.everythingIn1App,
      titleFontWeight: FontWeight.w400,
      showBackButton: false,
      titleColor: CustomColor.primaryLightColor,
    );
  }

  _bodyWidget(BuildContext context) {
    return Stack(
      alignment: Alignment.center,
      children: [
        Image.network(
          controller.onBoardImage.value.isNotEmpty
              ? controller.onBoardImage.value
              : controller.defaultImg.value,
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) {
            return Image.network(
              controller.defaultImg.value,
              fit: BoxFit.cover,
            );
          },
        ),
        Padding(
            padding: EdgeInsets.symmetric(
                horizontal: Dimensions.marginSizeHorizontal,
                vertical: Dimensions.heightSize),
            child: Obx(
                  () => Column(
                mainAxisAlignment: mainEnd,
                children: [
                  const ChangeLanguageWidget(
                      dorpButtonColor: CustomColor.whiteColor,
                      dropTextColor: CustomColor.primaryDarkTextColor,
                      dropMenuColor: CustomColor.whiteColor,
                      arrowColor: CustomColor.primaryDarkTextColor,
                      routeOnChange: Routes.onboardScreen),
                  Padding(
                    padding: EdgeInsets.only(top: Dimensions.heightSize * 0.8),
                    child: PrimaryButton(
                      title: Strings.login,
                      onPressed: () {
                        controller.onGetStarted;
                      },
                      buttonTextColor: CustomColor.whiteColor,
                    ),
                  ),
                  if (controller.userRegister.value == 1) ...[
                    Padding(
                      padding: EdgeInsets.only(top: Dimensions.heightSize * 0.8),
                      child: PrimaryButton(
                        title: Strings.register,
                        onPressed: () {
                          Get.toNamed(Routes.registrationScreen);
                        },
                        borderColor: CustomColor.primaryLightColor,
                        buttonColor: CustomColor.whiteColor,
                        borderWidth: 1,
                        buttonTextColor: CustomColor.primaryDarkTextColor,
                      ),
                    ),
                  ],
                  TitleHeading5Widget(
                    padding: EdgeInsets.only(top: Dimensions.heightSize),
                    fontSize: Dimensions.headingTextSize6,
                    text: Strings.byProceedingYouAgreeToOur,
                    color: CustomColor.greyColor,
                  ),
                  _termsAndPolicyTextWidget(),
                ],
              ),
            )),
      ],
    );
  }

  }

  _termsAndPolicyTextWidget() {
    return Row(
      mainAxisAlignment: mainCenter,
      children: [
        InkWell(
          splashColor: Colors.transparent,
          highlightColor: Colors.transparent,
          onTap: () {
            Get.to(WebViewScreen(
                url: LocalStorage.getPrivacyPolicyLink(),
                title: Strings.privacyPolicy));
          },
          child: TitleHeading5Widget(
            fontSize: Dimensions.headingTextSize6,
            text: Strings.termsAndCondition,
            color: CustomColor.primaryLightColor,
          ),
        ),
        TitleHeading5Widget(
          padding: EdgeInsets.symmetric(
              horizontal: Dimensions.marginSizeHorizontal * 0.1),
          fontSize: Dimensions.headingTextSize6,
          text: Strings.and,
          color: CustomColor.greyColor,
        ),
        InkWell(
          splashColor: Colors.transparent,
          highlightColor: Colors.transparent,
          onTap: () {
            Get.to(WebViewScreen(
                url: LocalStorage.getPrivacyPolicyLink(),
                title: Strings.privacyPolicy));
          },
          child: TitleHeading5Widget(
            fontSize: Dimensions.headingTextSize6,
            text: Strings.privacyPolicy,
            color: CustomColor.primaryLightColor,
          ),
        ),
      ],
    );
  }

