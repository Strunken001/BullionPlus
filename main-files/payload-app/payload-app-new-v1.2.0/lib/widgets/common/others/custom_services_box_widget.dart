import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/views/utils/size.dart';
import '../text_labels/title_heading5_widget.dart';

class CustomServicesBoxWidget extends StatelessWidget {
  final IconData iconPath;
  final String title;
  final VoidCallback onPressed;

  const CustomServicesBoxWidget({
    super.key,
    required this.iconPath,
    required this.title,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onPressed,
      splashColor: Colors.transparent,
      highlightColor: Colors.transparent,
      child: Column(
        children: [
          CircleAvatar(
              backgroundColor: CustomColor.primaryLightScaffoldBackgroundColor,
              child: Icon(iconPath,color: CustomColor.primaryDarkColor,)

          ),
          verticalSpace(Dimensions.marginSizeVertical * 0.2),
          TitleHeading5Widget(
            text: DynamicLanguage.isLoading ? "" : DynamicLanguage.key(title),
          ),
        ],
      ),
    );
  }
}
