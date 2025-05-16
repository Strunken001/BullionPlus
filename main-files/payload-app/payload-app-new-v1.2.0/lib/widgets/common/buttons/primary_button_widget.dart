import 'package:flutter/material.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/buttons/primary_button.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading4_widget.dart';

class PrimaryButtonWidget extends StatelessWidget {
  final String buttonText;
  final VoidCallback onPressed;

  final Color? buttonColor;
  const PrimaryButtonWidget({
    super.key,
    required this.buttonText,
    required this.onPressed,
    this.buttonColor,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding:
          EdgeInsets.symmetric(vertical: Dimensions.marginSizeVertical * 0.5),
      child: PrimaryButton(
        height: Dimensions.heightSize * 3.3,
        buttonColor: buttonColor ?? CustomColor.primaryLightColor,
        buttonTextColor: CustomColor.whiteColor,
        onPressed: onPressed,
        child: TitleHeading4Widget(
          text: buttonText,
          color: CustomColor.whiteColor,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}
