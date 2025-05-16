import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../languages/strings.dart';
import '../../views/utils/custom_color.dart';
import '../../views/utils/dimensions.dart';

class CustomSnackBar {
  static success(String message) {
    return Get.snackbar(
      DynamicLanguage.key(Strings.success),
      DynamicLanguage.key(message),
      margin: EdgeInsets.symmetric(
          horizontal: Dimensions.paddingSize * 0.5,
          vertical: Dimensions.paddingSize * 0.5),
      backgroundColor: CustomColor.primaryLightColor,
      colorText: CustomColor.whiteColor,
      leftBarIndicatorColor: CustomColor.greenColor,
      progressIndicatorBackgroundColor: CustomColor.redColor,
      isDismissible: true,
      animationDuration: const Duration(seconds: 1),
      snackPosition: SnackPosition.BOTTOM,
      borderRadius: 5.0,
      mainButton: TextButton(
        onPressed: () {
          Get.back();
          // Get.close(0);
        },
        child: Text(
          DynamicLanguage.isLoading
              ? ""
              : DynamicLanguage.key(
                  Strings.dismiss,
                ),
          style: const TextStyle(color: CustomColor.whiteColor),
        ),
      ),
      icon: const Icon(
        Icons.check_circle_rounded,
        color: CustomColor.greenColor,
      ),
    );
  }

  static error(String message) {
    return Get.snackbar(
        DynamicLanguage.key(Strings.alert), DynamicLanguage.key(message),
        margin: EdgeInsets.symmetric(
            horizontal: Dimensions.paddingSize * 0.5,
            vertical: Dimensions.paddingSize * 0.5),
        backgroundColor: CustomColor.redColor.withOpacity(0.8),
        colorText: CustomColor.whiteColor,
        leftBarIndicatorColor: CustomColor.redColor,
        progressIndicatorBackgroundColor: CustomColor.redColor,
        isDismissible: true,
        animationDuration: const Duration(seconds: 1),
        snackPosition: SnackPosition.BOTTOM,
        borderRadius: 5.0,
        mainButton: TextButton(
          onPressed: () {
            Get.back();
            // Get.close(1);
          },
          child: Text(
            DynamicLanguage.key(Strings.dismiss),
            style: const TextStyle(color: CustomColor.whiteColor),
          ),
        ),
        icon: const Icon(
          Icons.warning,
          color: CustomColor.redColor,
        ));
  }
}
