import 'package:flutter/material.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/views/utils/size.dart';
import 'package:payloadui/widgets/common/text_labels/title_sub_title_widget.dart';

import '../../../languages/strings.dart';

class ProfileContentBoxWidget extends StatelessWidget {
  final String title;
  final String? sub;
  final IconData icon;
  final bool isArrow;
  final VoidCallback? onTap;

  const ProfileContentBoxWidget({
    super.key,
    required this.title,
    required this.icon,
    this.isArrow = false,
    this.onTap,
    this.sub,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      splashColor: Colors.transparent,
      highlightColor: Colors.transparent,
      onTap: onTap,
      child: Container(
          margin: EdgeInsets.symmetric(
              vertical: Dimensions.marginSizeVertical * 0.2),
          height: MediaQuery.of(context).size.height * 0.10,
          decoration: BoxDecoration(
            color: CustomColor.whiteColor,
            borderRadius: BorderRadius.circular(Dimensions.radius),
          ),
          child: Padding(
            padding: EdgeInsets.symmetric(
                horizontal: Dimensions.marginSizeVertical * 0.5),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Wrap(
                  children: [
                    CircleAvatar(
                      radius: Dimensions.radius * 2,
                      backgroundColor:
                          CustomColor.primaryLightColor.withOpacity(0.2),
                      child: Icon(
                        icon,
                        size: Dimensions.iconSizeLarge,
                        color: CustomColor.primaryLightColor,
                      ),
                    ),
                    horizontalSpace(Dimensions.marginSizeHorizontal * 0.5),
                    TitleSubTitleWidget(
                      titleFontSize: Dimensions.headingTextSize4,
                      subTitleFontSize: Dimensions.headingTextSize5,
                      title: title,
                      subTitle: sub ?? Strings.totalPurchase,
                    ),
                  ],
                ),
                if (isArrow == true) ...[
                  Icon(
                    Icons.arrow_forward_ios,
                    color: Colors.grey.withOpacity(0.5),
                  )
                ]
              ],
            ),
          )),
    );
  }
}
