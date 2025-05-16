import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/controller/add_money/top_up_controller.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading4_widget.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading5_widget.dart';
import '../../../backend/utils/custom_loading_api.dart';
import '../../../languages/strings.dart';
import 'primary_input_filed.dart';

class AmountInputWidget extends StatelessWidget {
  final TopUpController controller;

  const AmountInputWidget({
    super.key,
    required this.controller,
  });

  @override
  Widget build(BuildContext context) {
    return Obx(
      () => controller.isLoading
          ? const CustomLoadingAPI()
          : controller.minAmount.value >= 1
              ? Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    PrimaryInputWidget(
                      suffixIcon: Container(
                        alignment: Alignment.center,
                        width: Dimensions.widthSize * 1.5,
                        decoration: BoxDecoration(
                          color: CustomColor.primaryLightColor,
                          borderRadius: DynamicLanguage.languageDirection ==
                                  TextDirection.rtl
                              ? BorderRadius.only(
                                  bottomLeft:
                                      Radius.circular(Dimensions.radius * 0.5),
                                  topLeft:
                                      Radius.circular(Dimensions.radius * 0.5),
                                )
                              : BorderRadius.only(
                                  bottomRight:
                                      Radius.circular(Dimensions.radius * 0.5),
                                  topRight:
                                      Radius.circular(Dimensions.radius * 0.5),
                                ),
                        ),
                        child: TitleHeading4Widget(
                          text: controller.receiverCurrency.value,
                          color: CustomColor.whiteColor,
                        ),
                      ),
                      controller: controller.amountController,
                      hint: Strings.amount,
                      label: Strings.amount,
                      keyboardType: TextInputType.number,
                      onChanged: (value) {
                        if (controller.amountController.text.length >= 2 &&
                            controller.countryCode.value.isNotEmpty &&
                            controller.mobileNumberController.text.isNotEmpty) {
                          Future.delayed(const Duration(milliseconds: 500), () {
                            controller.detectOperatorProcess();
                          });
                        }
                      },
                    ),
                    TitleHeading5Widget(
                      padding:
                          EdgeInsets.only(top: Dimensions.heightSize * 0.5),
                      text:
                          "${DynamicLanguage.key(Strings.limit)} : ${controller.minAmount.value} ${DynamicLanguage.key(Strings.bDT)} - ${controller.maxAmount.value} ${DynamicLanguage.key(Strings.bDT)}",
                      color: CustomColor.primaryLightColor,
                      fontWeight: FontWeight.w400,
                      fontSize: Dimensions.headingTextSize6 * 1.1,
                    ),
                  ],
                )
              : const SizedBox(),
    );
  }
}
