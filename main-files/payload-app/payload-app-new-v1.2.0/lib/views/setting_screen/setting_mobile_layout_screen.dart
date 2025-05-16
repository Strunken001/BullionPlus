import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/appbar/primary_appbar.dart';

import '../../languages/strings.dart';
import '../../languages/language_dropdown_widget.dart';
import '../../widgets/common/text_labels/title_heading4_widget.dart';
import '../utils/custom_color.dart';

class SettingMobileLayoutScreen extends StatelessWidget {
  const SettingMobileLayoutScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: PrimaryAppBar(
        Strings.settings,
        autoLeading: true,
        appbarSize: Dimensions.heightSize * 2,
      ),
      body: _bodyWidget(),
    );
  }

  _bodyWidget() {
    return Padding(
      padding:
          EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
      child: Column(
        children: [
          ListTile(
            title: const TitleHeading4Widget(
              text: Strings.changePassword,
              fontWeight: FontWeight.bold,
              color: CustomColor.primaryLightColor,
            ),
            onTap: () {
              Get.toNamed(Routes.changePasswordScreen);
            },
          ),
          ListTile(
            title: const TitleHeading4Widget(
              text: Strings.KYCVerification,
              fontWeight: FontWeight.bold,
              color: CustomColor.primaryLightColor,
            ),
            onTap: () {
              Get.toNamed(Routes.kycVerificationScreen);
            },
          ),
          ListTile(
            title: const TitleHeading4Widget(
              text: Strings.twofAVerification,
              fontWeight: FontWeight.bold,
              color: CustomColor.primaryLightColor,
            ),
            onTap: () {
              Get.toNamed(Routes.twofaVerificationScreen);
            },
          ),
          const ListTile(
            title: TitleHeading4Widget(
              text: Strings.language,
              fontWeight: FontWeight.bold,
              color: CustomColor.primaryLightColor,
            ),
            trailing: ChangeLanguageWidget(
                dorpButtonColor: CustomColor.secondaryWhiteBoxColor,
                dropTextColor: CustomColor.primaryLightColor,
                dropMenuColor: CustomColor.whiteColor,
                arrowColor: CustomColor.primaryLightColor,
                routeOnChange: Routes.navigationScreen),
          )
        ],
      ),
    );
  }
}
