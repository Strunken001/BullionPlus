import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/model/auth/register/country_model.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/views/utils/size.dart';
import 'package:payloadui/widgets/common/buttons/primary_button_widget.dart';
import 'package:payloadui/widgets/common/inputs/primary_input_filed.dart';
import 'package:payloadui/widgets/common/text_labels/login_link_text_widget.dart';
import '../../../controller/auth/login/log_in_screen_controller.dart';
import '../../../languages/strings.dart';
import '../../../widgets/common/buttons/custom_button_builder_widget.dart';
import '../../../widgets/common/inputs/custom_phone_number_field.dart';
import '../../../widgets/common/text_labels/title_heading2_widget.dart';
import '../../../widgets/common/text_labels/title_heading4_widget.dart';
import '../../../widgets/common/text_labels/title_heading5_widget.dart';
import '../../../widgets/common/buttons/custom_button_widget.dart';
import '../../../widgets/custom_dropdown.dart';

class LogInMobileLayoutScreen extends StatelessWidget {
  LogInMobileLayoutScreen({super.key});

  final controller = Get.put(LogInController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Obx(
      () => controller.selectedCountry.isEmpty
          ? const CustomLoadingAPI()
          : _bodyWidget(context),
    ));
  }

  _bodyWidget(context) {
    return SingleChildScrollView(
      child: Padding(
        padding:
            EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
        child: Column(
          children: [
            _loginTextAndButton(),
            controller.myIndex.value == 0
                ? _logInWithOtpView()
                : _logInWithPasswordView(),
            _buttonAndTextWidget(),
          ],
        ),
      ),
    );
  }

  _loginTextAndButton() {
    return Column(
      children: [
        TitleHeading2Widget(
          padding: EdgeInsets.only(top: Dimensions.marginSizeVertical * 4),
          text: Strings.login,
          fontWeight: FontWeight.w500,
        ),
        CustomButtonBuilderWidget(
          itemBuilder: (context, index) {
            return Obx(
              () => CustomButtonWidget(
                text: controller.buttonTextList[index],
                selectedColor: CustomColor.whiteColor,
                backgroundColor: CustomColor.primaryLightColor.withOpacity(0.8),
                isSelected: controller.myIndex.value == index,
                onTap: () {
                  controller.customButtonOnchange(index);
                },
              ),
            );
          },
        )
      ],
    );
  }

  _logInWithOtpView() {
    return Form(
      key: controller.formKey1,
      child: Column(
        crossAxisAlignment: crossStart,
        children: [
          const TitleHeading4Widget(
            padding: EdgeInsets.only(bottom: 7),
            text: Strings.selectCountry,
            fontWeight: FontWeight.w600,
          ),
          CustomDropdownMenu<Country>(
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(Dimensions.radius * 0.8),
              color: CustomColor.greyColor.withOpacity(0.2),
            ),
            itemsList: controller.countryNameList,
            selectMethod: controller.selectedCountry,
            onChanged: (value) {
              controller.selectedCountry.value = value!.name;
              controller.mobileCode.value = value.mobileCode;
            },
          ),
          CustomInputField(
              controller: controller.otpNumberController,
              label: DynamicLanguage.key(Strings.mobileNumber),
              hint: Strings.mobile,
              phoneCodeText: controller.mobileCode.value),
        ],
      ),
    );
  }

  _logInWithPasswordView() {
    return Form(
      key: controller.formKey1,
      child: Column(
        crossAxisAlignment: crossEnd,
        children: [
          CustomDropdownMenu<Country>(
            isCountryLabelText: true,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(Dimensions.radius * 0.8),
              color: CustomColor.greyColor.withOpacity(0.2),
            ),
            itemsList: controller.countryNameList,
            selectMethod: controller.selectedCountry,
            onChanged: (value) {
              controller.selectedCountry.value = value!.name;
              controller.mobileCode.value = value.mobileCode;
            },
            dropdownIconColor: CustomColor.greyColor,
          ),
          CustomInputField(
              controller: controller.numberController,
              label: Strings.mobileNumber,
              hint: Strings.mobile,
              phoneCodeText: controller.mobileCode.value),
          PrimaryInputWidget(
            isObscure: controller.isPasswordHidden.value,
            isValidator: true,
            controller: controller.passwordController,
            hint: Strings.password,
            label: Strings.password,
            suffixIcon: InkWell(
              splashColor: Colors.transparent,
              highlightColor: Colors.transparent,
              onTap: () {
                controller.isPasswordHidden.value =
                    !controller.isPasswordHidden.value;
              },
              child: Icon(
                color: Colors.grey,
                controller.isPasswordHidden.value
                    ? Icons.visibility_off_sharp
                    : Icons.visibility_sharp,
                size: Dimensions.iconSizeSmall * 2,
              ),
            ),
          ),
          InkWell(
            highlightColor: Colors.transparent,
            splashColor: Colors.transparent,
            onTap: () {
              Get.toNamed(Routes.forgotPasswordScreen);
            },
            child: TitleHeading5Widget(
              padding:
                  EdgeInsets.symmetric(vertical: Dimensions.paddingSize * 0.3),
              text: Strings.forgotPassword,
              color: CustomColor.primaryLightColor,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  _buttonAndTextWidget() {
    return Column(
      children: [
        Obx(() {
          return controller.isLoading
              ? const CustomLoadingAPI()
              : PrimaryButtonWidget(
                  buttonText: Strings.continues,
                  onPressed: () {
                    controller.signInProcessApiAndCheckValidation();
                  },
                );
        }),
        LinkTwoTextWidget(
            onTap: () {
              Get.toNamed(Routes.registrationScreen);
            },
            text1: Strings.dontHaveAccount,
            text2: Strings.registerNow),
      ],
    );
  }
}
