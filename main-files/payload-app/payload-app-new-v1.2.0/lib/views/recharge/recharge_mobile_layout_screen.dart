import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/backend/utils/custom_snackbar.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/appbar/primary_appbar.dart';
import 'package:payloadui/widgets/common/buttons/primary_button_widget.dart';
import '../../controller/add_money/top_up_controller.dart';
import '../../languages/strings.dart';
import '../../routes/routes.dart';
import '../../widgets/common/inputs/custom_phone_number_field.dart';
import '../../widgets/common/inputs/input_amount_widget.dart';
import '../../widgets/custom_country_dropdown_widget.dart';

class RechargeMobileLayoutScreen extends StatelessWidget {
  RechargeMobileLayoutScreen({super.key});

  final controller = Get.put(TopUpController());
  final GlobalKey<FormState> formKey = GlobalKey<FormState>();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appbarWidget(),
      bottomNavigationBar: _bottomWidget(context),
      body: _bodyWidget(context),
    );
  }

  appbarWidget() {
    return PrimaryAppBar(
      titleFontWeight: FontWeight.w400,
      Strings.recharge,
      showBackButton: false,
      appbarSize: Dimensions.heightSize * 4,
    );
  }

  _bodyWidget(context) {
    return Form(
      key: formKey,
      child: Padding(
        padding:
            EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
        child: Column(
            mainAxisAlignment: MainAxisAlignment.start,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              CountryDropdown(
                label: Strings.selectCountry,
                selectMethod: DynamicLanguage.isLoading
                    ? ""
                    : DynamicLanguage.key(Strings.selectCountry),
                itemsList: controller.controller.countryList,
                onChanged: (value) {
                  if (value != null) {
                    controller.selectedCountry.value = value.name;
                    controller.mobileCode.value = value.mobileCode;
                    controller.countryCode.value = value.iso2;
                  }
                },
              ),
              Obx(
                () => CustomInputField(
                  controller: controller.mobileNumberController,
                  label: Strings.mobileNumber,
                  hint: Strings.mobileNumber,
                  phoneCodeText: controller.mobileCode.value,
                  onChanged: (value) {
                    if (controller.countryCode.value.isNotEmpty &&
                        controller.mobileNumberController.text.length == 10) {
                      controller.detectOperatorProcess();
                    }
                  },
                ),
              ),
              Obx(
                () => controller.isLoading
                    ? const CustomLoadingAPI()
                    : AmountInputWidget(controller: controller),
              )
            ]),
      ),
    );
  }

  _bottomWidget(context) {
    return Padding(
        padding: EdgeInsets.symmetric(
            horizontal: Dimensions.marginSizeHorizontal,
            vertical: Dimensions.marginSizeVertical * 0.5),
        child: Obx(
          () => PrimaryButtonWidget(
              buttonColor: controller.isLoading == true
                  ? CustomColor.primaryLightColor.withOpacity(0.3)
                  : CustomColor.primaryLightColor,
              buttonText: Strings.continues,
              onPressed: () {
                int amount =
                    int.tryParse(controller.amountController.text) ?? 0;
                if (controller.isLoading == false) {
                  if (formKey.currentState!.validate()) {
                    if (amount >= 20) {
                      controller.calculateAllCharges();
                      Get.toNamed(Routes.rechargePreviewScreen);
                    } else {
                      CustomSnackBar.error(
                          DynamicLanguage.key(Strings.invalidAmounts));
                    }
                  }
                }
              }),
        ));
  }
}
