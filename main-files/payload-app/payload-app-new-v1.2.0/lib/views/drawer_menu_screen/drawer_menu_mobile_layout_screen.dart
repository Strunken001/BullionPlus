import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/local_storage/local_storage.dart';
import 'package:payloadui/controller/profile/profile_controller.dart';
import 'package:payloadui/widgets/common/custom_container.dart';
import '../../backend/utils/custom_loading_api.dart';
import '../../languages/strings.dart';
import '../../web_view/web_view_screen.dart';
import '../../widgets/common/text_labels/title_heading2_widget.dart';
import '../../widgets/common/text_labels/title_heading4_widget.dart';
import '../utils/custom_color.dart';
import '../utils/dimensions.dart';
import '../utils/size.dart';

class MyDrawerMenu extends StatelessWidget {
  MyDrawerMenu({super.key});

  final _controller = Get.put(ProfileController());

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Drawer(
        backgroundColor: CustomColor.whiteColor,
        child: SingleChildScrollView(
          child: Padding(
            padding: EdgeInsets.symmetric(
                horizontal: Dimensions.marginSizeHorizontal,
                vertical: Dimensions.heightSize),
            child: Column(
              children: [
                ListTile(
                  title: const TitleHeading4Widget(
                    text: Strings.privacyPolicy,
                    fontWeight: FontWeight.bold,
                  ),
                  onTap: () {
                    Get.to(WebViewScreen(
                        url: LocalStorage.getPrivacyPolicyLink(),
                        title: Strings.privacyPolicy));
                  },
                  leading: const Icon(
                    Icons.privacy_tip,
                  ),
                ),
                ListTile(
                  title: const TitleHeading4Widget(
                    text: Strings.aboutUs,
                    fontWeight: FontWeight.bold,
                  ),
                  onTap: () {
                    Get.to(WebViewScreen(
                        url: LocalStorage.getAboutUsLink(),
                        title: Strings.aboutUs));
                  },
                  leading: const Icon(
                    Icons.info,
                  ),
                ),
                ListTile(
                  title: const TitleHeading4Widget(
                    text: Strings.contactUs,
                    fontWeight: FontWeight.bold,
                  ),
                  onTap: () {
                    Get.to(WebViewScreen(
                        url: LocalStorage.getContactUsLink(),
                        title: Strings.contactUs));
                  },
                  leading: const Icon(
                    Icons.contact_mail,
                  ),
                ),
                Obx(
                  () => _controller.isLoading
                      ? const CustomLoadingAPI()
                      : ListTile(
                          onTap: () {
                            _controller.signOutProcess();
                          },
                          title: const TitleHeading4Widget(
                            text: Strings.logOut,
                            fontWeight: FontWeight.bold,
                          ),
                          leading: const Icon(
                            Icons.exit_to_app_outlined,
                          ),
                        ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  signOutDialog(BuildContext context) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return BackdropFilter(
          filter: ImageFilter.blur(
            sigmaX: 5,
            sigmaY: 5,
          ),
          child: Dialog(
            backgroundColor: Theme.of(context).colorScheme.surface,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(Dimensions.radius),
            ),
            child: Padding(
              padding: EdgeInsets.all(Dimensions.paddingSize),
              child: Column(
                crossAxisAlignment: crossStart,
                mainAxisSize: mainMin,
                children: [
                  const TitleHeading2Widget(
                    text: "Log Out Alert",
                    textAlign: TextAlign.start,
                  ),
                  verticalSpace(Dimensions.heightSize),
                  const TitleHeading4Widget(
                    text: Strings.areYouSure,
                    textAlign: TextAlign.start,
                    opacity: 0.80,
                  ),
                  verticalSpace(Dimensions.heightSize),
                  Row(
                    children: [
                      Expanded(
                        child: InkWell(
                          onTap: () {
                            Get.back();
                          },
                          child: CustomContainer(
                            height: Dimensions.buttonHeight * 0.7,
                            borderRadius: Dimensions.radius * 0.8,
                            color: Get.isDarkMode
                                ? CustomColor.primaryBGLightColor
                                    .withOpacity(0.15)
                                : CustomColor.primaryBGDarkColor
                                    .withOpacity(0.15),
                            child: const TitleHeading4Widget(
                              text: Strings.cancel,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ),
                      horizontalSpace(Dimensions.widthSize),
                      Expanded(
                        child: InkWell(
                          onTap: () {
                            // controller.signOutProcess();
                          },
                          child: CustomContainer(
                            height: Dimensions.buttonHeight * 0.7,
                            borderRadius: Dimensions.radius * 0.8,
                            color: Theme.of(context).primaryColor,
                            child: const TitleHeading4Widget(
                              text: Strings.okay,
                              color: CustomColor.whiteColor,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ).paddingAll(Dimensions.paddingSize * 0.5),
                ],
              ),
            ),
          ),
        );
      },
    );
  }
}
