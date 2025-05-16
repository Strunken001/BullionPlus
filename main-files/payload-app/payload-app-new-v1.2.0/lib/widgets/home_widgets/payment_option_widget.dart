import 'package:flutter/material.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';

class PaymentMethodWidget extends StatelessWidget {
  const PaymentMethodWidget(
      {super.key, required this.imagePath, required this.onTap});

  final String imagePath;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      splashColor: Colors.transparent,
      highlightColor: Colors.transparent,
      onTap: onTap,
      child: Padding(
        padding: EdgeInsets.only(right: Dimensions.widthSize * 0.8),
        child: CircleAvatar(
          radius: Dimensions.radius *1.5,
          backgroundColor: CustomColor.primaryLightColor.withOpacity(0.12),
          child: Image.network(
            imagePath,
            height: Dimensions.heightSize * 1.5,
          ),
        ),
      ),
    );
  }
}
