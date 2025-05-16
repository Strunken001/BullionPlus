import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/others/custom_image_widget.dart';

class TopAppBarWidget extends StatelessWidget {
  final IconData icon;
  final String imagePath;
  final double? imageHeight;
  final double? imageWidth;
  final EdgeInsets? padding; // Optional padding parameter

  const TopAppBarWidget({
    super.key,
    required this.icon,
    required this.imagePath,
    this.imageHeight,
    this.imageWidth,
    this.padding, // Initialize padding parameter
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: padding ??
          EdgeInsets.only(
              top: Dimensions.heightSize *
                  2.6), // Use provided padding or default
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          InkWell(
            highlightColor: Colors.transparent,
            splashColor: Colors.transparent,
            onTap: () {
              Get.back();
            },
            child: Icon(icon),
          ),
          CustomImageWidget(
            path: imagePath,
            height: imageHeight ?? MediaQuery.of(context).size.height * 0.10,
            width: imageWidth ?? MediaQuery.of(context).size.height * 0.15,
          ),
          Container(),
        ],
      ),
    );
  }
}
