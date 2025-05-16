import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/controller/dashboard/dashboard_controller.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';

class CustomRechargeAmountWidget extends StatelessWidget {
  final controller = Get.put(DashboardController());

  final String text;
  final VoidCallback onPressed;

  CustomRechargeAmountWidget({
    super.key,
    required this.text,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onPressed,
      splashColor: Colors.transparent,
      highlightColor: Colors.transparent,
      child: Obx(() => Container(
            margin: EdgeInsets.only(right: Dimensions.widthSize * 0.5),
            padding: EdgeInsets.symmetric(
                horizontal: Dimensions.widthSize * 0.8,
                vertical: Dimensions.heightSize * 0.2),
            alignment: Alignment.center,
            decoration: BoxDecoration(
                border: Border.all(color: CustomColor.greyColor),
                borderRadius: BorderRadius.circular(Dimensions.radius * 0.5)),
            child: Text(
              '$text ${controller.currency.value}',
              style: TextStyle(
                fontWeight: FontWeight.normal,
                color: CustomColor.secondaryTextColor,
                fontSize: Dimensions.headingTextSize5, // Customize font size
              ),
            ),
          )),
    );
  }
}
