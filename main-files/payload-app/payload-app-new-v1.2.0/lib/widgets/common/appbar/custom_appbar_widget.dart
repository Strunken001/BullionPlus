import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading5_widget.dart';

import '../../../custom_assets/assets.gen.dart';
import '../others/custom_image_widget.dart';

class CustomAppBarWidget extends StatelessWidget
    implements PreferredSizeWidget {
  final String title;
  final String subtitle;
  final String profilePath;
  final String defaultImage;
  final VoidCallback onTap;

  const CustomAppBarWidget({
    super.key,
    required this.title,
    required this.subtitle,
    required this.profilePath,
    required this.onTap,
    required this.defaultImage,
  });

  @override
  Widget build(BuildContext context) {
    return AppBar(
      toolbarHeight: Dimensions.heightSize * 3,
      backgroundColor: CustomColor.whiteColor,
      leading: Padding(
          padding: EdgeInsets.only(
              right: DynamicLanguage.languageDirection == TextDirection.rtl
                  ? Dimensions.marginSizeHorizontal
                  : 0,
              left: DynamicLanguage.languageDirection == TextDirection.ltr
                  ? Dimensions.marginSizeHorizontal
                  : 0,
              top: Dimensions.heightSize * 0.5),
          child: InkWell(
            onTap: onTap,
            splashColor: Colors.transparent,
            highlightColor: Colors.transparent,
            child: Stack(
              children: [
                CustomImageWidget(
                  path: Assets.logo.ellipse48,
                  height: Dimensions.heightSize * 2.8,
                  width: Dimensions.widthSize * 3,
                ),
                Align(
                  alignment: Alignment.center, // Center the CircleAvatar
                  child: CircleAvatar(
                    radius: Dimensions.radius,
                    backgroundColor: Colors.transparent,
                    child: ClipOval(
                      child: Image.network(
                        profilePath,
                        fit: BoxFit.cover,
                        width: Dimensions.radius * 1.6,
                        height: Dimensions.radius * 1.6,
                        errorBuilder: (context, error, stackTrace) {
                          return Image.network(
                            defaultImage,
                            fit: BoxFit.cover,
                            width: Dimensions.radius * 1.6,
                            height: Dimensions.radius * 1.6,
                          );
                        },
                      ),
                    ),
                  ),
                ),
              ],
            ),
          )),
      title: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          TitleHeading5Widget(
            text: title,
            padding: EdgeInsets.only(bottom: Dimensions.heightSize * 0.1),
          ),
          TitleHeading5Widget(text: subtitle)
        ],
      ),
      scrolledUnderElevation: 0,
    );
  }

  @override
  Size get preferredSize => Size.fromHeight(Dimensions.heightSize * 3);
}
