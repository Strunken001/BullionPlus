import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/controller/add_money/add_money_screen_controller.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import '../../../languages/strings.dart';
import '../../../routes/routes.dart';
import '../../../views/utils/size.dart';
import '../../home_widgets/payment_option_widget.dart';

class RechargeAmountInputWidget extends StatelessWidget {
  final List<String> rechargeAmounts;
  final ValueChanged<String> onAmountSelected;
  final TextEditingController fieldController;

  RechargeAmountInputWidget({
    super.key,
    required this.rechargeAmounts,
    required this.onAmountSelected,
    required this.fieldController,
  });

  final wallerController = Get.put(AddMoneyScreenController());

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Container(
          decoration: BoxDecoration(
              color: CustomColor.secondaryWhiteBoxColor,
              borderRadius: BorderRadius.circular(Dimensions.radius)),

          padding: EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal * 0.2),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: TextFormField(
                  controller: fieldController,
                  readOnly: true, // Makes the field non-editable
                  decoration: InputDecoration(
                    contentPadding: EdgeInsets.only(
                      top: Dimensions.heightSize * 0.4,
                      left: Dimensions.widthSize,
                    ),
                    hintText: DynamicLanguage.key(
                      Strings.enterAmount,
                    ),
                    hintStyle: TextStyle(
                      fontSize: Dimensions.headingTextSize5,
                      color: CustomColor.secondaryTextColor,
                      fontWeight: FontWeight.normal,
                    ),
                    border: const OutlineInputBorder(
                      borderSide: BorderSide.none,
                    ),
                  ),
                ),
              ),
              Row(
                mainAxisAlignment: mainSpaceBet,
                children: [
                  ...List.generate(
                    wallerController.paymentGatewayInfoList.take(4).length,
                    (index) {
                      if (index == 3) {
                        return InkWell(
                          splashColor: Colors.transparent,
                          highlightColor: Colors.transparent,
                          onTap: () {
                            Get.toNamed(Routes.walletRechargeScreen);
                          },
                          child: Padding(
                            padding: EdgeInsets.only(
                                right: Dimensions.widthSize * 0.8),
                            child: CircleAvatar(
                                radius: Dimensions.radius *1.5,
                                backgroundColor: CustomColor.primaryLightColor
                                    .withOpacity(0.12),
                                child: Icon(
                                  Icons.more_horiz,size: Dimensions.iconSizeDefault * 1.3,
                                  color: CustomColor.primaryLightColor
                                      .withOpacity(0.6),
                                )),
                          ),
                        );
                      }
                      final data = wallerController.paymentGatewayInfoList[index];
                      return PaymentMethodWidget(
                          onTap: () {
                            wallerController.selectPaymentIndex(index);
                            wallerController.selectedGatewayName.value =
                                data.name;
                            Get.toNamed(Routes.walletRechargeScreen);
                          },
                          imagePath:
                              "${wallerController.imageUrls.value}/${data.image}");
                    },
                  ),
                ],
              ),
            ],
          ),
        ),
      ],
    );
  }
}
