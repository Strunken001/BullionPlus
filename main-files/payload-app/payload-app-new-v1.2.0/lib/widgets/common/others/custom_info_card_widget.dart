import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get_core/src/get_main.dart';
import 'package:get/get_navigation/get_navigation.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/views/utils/size.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading4_widget.dart';

class CustomInfoCardWidget extends StatelessWidget {
  final String title;
  final String subtitle;
  final String buttonText;

  const CustomInfoCardWidget({
    super.key,
    required this.title,
    required this.subtitle,
    required this.buttonText,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(Dimensions.paddingSize * 0.8),
      decoration: BoxDecoration(
        color: CustomColor.secondaryWhiteBoxColor,
        borderRadius: BorderRadius.circular(Dimensions.radius * 0.8),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          TitleHeading4Widget(
            text: title,
            fontWeight: FontWeight.w500,
          ),
          TitleHeading4Widget(
            text: subtitle,
            color: CustomColor.secondaryTextColor,
          ),
          Row(
            mainAxisAlignment: mainEnd,
            children: [
              ElevatedButton(
                  onPressed: () {
                    Get.toNamed(Routes.dataBundlesScreen);
                  },
                  style: ElevatedButton.styleFrom(
                      backgroundColor: CustomColor.primaryLightColor,
                      shape: RoundedRectangleBorder(
                          borderRadius:
                              BorderRadius.circular(Dimensions.radius * 0.8))),
                  child: Text(
                    style: TextStyle(color: CustomColor.whiteColor),
                    (DynamicLanguage.key(buttonText)),
                  )),
            ],
          )
        ],
      ),
    );
  }
}
