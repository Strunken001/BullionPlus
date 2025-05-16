import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/dashboard/dashboard_controller.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/views/utils/size.dart';
import '../../../languages/strings.dart';
import '../../../widgets/common/appbar/primary_appbar.dart';
import '../../../widgets/common/others/profile_content_box_widget.dart';
import '../../../widgets/common/text_labels/title_heading2_widget.dart';
import '../../../widgets/common/text_labels/title_heading4_widget.dart';
import '../../../widgets/common/text_labels/title_heading5_widget.dart';

class ProfileMobileLayoutScreen extends StatelessWidget {
  ProfileMobileLayoutScreen({super.key});

  final controller = Get.put(DashboardController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _appbarWidget(),
      body: Obx(
        () => controller.isLoading
            ? const CustomLoadingAPI()
            : RefreshIndicator(
                color: CustomColor.primaryLightColor,
                backgroundColor: CustomColor.whiteColor,
                onRefresh: () => controller.getDashboardProcessApi(),
                child: _bodyWidget(context),
              ),
      ),
    );
  }

  _bodyWidget(BuildContext context) {
    return SingleChildScrollView(
      child: Padding(
        padding: EdgeInsets.symmetric(
            horizontal: Dimensions.marginSizeHorizontal * 0.8),
        child: Column(
          children: [
            _updateButtonWidget(),
            _profileLogoWidget(context),
            _balanceCardBoxWidget(context),
            _profileBoxWidget(context),
          ],
        ),
      ),
    );
  }

  _updateButtonWidget() {
    return Row(
      mainAxisAlignment: mainEnd,
      children: [
        Obx(() => controller.isLoading
            ? const CustomLoadingAPI()
            : OutlinedButton(
                style: ElevatedButton.styleFrom(
                  side: const BorderSide(color: CustomColor.primaryLightColor),
                  shape: RoundedRectangleBorder(
                    borderRadius:
                        BorderRadius.circular(Dimensions.radius * 0.8),
                  ),
                ),
                onPressed: () {
                  Get.toNamed(Routes.updateProfileScreen);
                },
                child: const TitleHeading4Widget(
                  text: Strings.update,
                  color: CustomColor.primaryLightColor,
                ))),
      ],
    );
  }

  _profileBoxWidget(context) {
    return Column(
      children: [
        ProfileContentBoxWidget(
            onTap: () {
              Get.toNamed(Routes.settingScreen);
            },
            sub: Strings.information,
            isArrow: true,
            title: DynamicLanguage.key(
              Strings.settings,
            ),
            icon: Icons.settings),
        ProfileContentBoxWidget(
            title: "${DynamicLanguage.key(
              Strings.recharge,
            )} - ${controller.mobileTopUpCount.value.toString()}",
            icon: Icons.phone_android),
        ProfileContentBoxWidget(
            title: "${DynamicLanguage.key(
              Strings.giftCard,
            )} - ${controller.giftCardCount.value.toString()}",
            icon: Icons.card_giftcard),
        ProfileContentBoxWidget(
            title: "${DynamicLanguage.key(
              Strings.addMoney,
            )} - ${controller.addMoneyCount.value.toString()}",
            icon: Icons.attach_money_sharp),
      ],
    );
  }

  _balanceCardBoxWidget(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(top: Dimensions.marginSizeVertical),
      child: Container(
        height: MediaQuery.of(context).size.height * 0.16,
        decoration: BoxDecoration(
            color: CustomColor.whiteColor,
            borderRadius: BorderRadius.circular(Dimensions.radius)),
        child: _balanceBoxWidget(),
      ),
    );
  }

  _profileLogoWidget(context) {
    return Container(
      padding: EdgeInsets.all(Dimensions.paddingSize * 0.3),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(Dimensions.radius * 10),
        border: Border.all(color: CustomColor.primaryLightColor, width: 4),
      ),
      child: CircleAvatar(
        radius: Dimensions.radius * 4,
        backgroundColor: Colors.transparent,
        child: ClipOval(
          child: Image.network(
              controller.image.value,
            fit: BoxFit.cover,
            width: Dimensions.radius * 8,
            height: Dimensions.radius * 8,
            errorBuilder: (context, error, stackTrace) {
              return Image.network(
                controller.defaultImage.value,
                fit: BoxFit.cover,
                width: Dimensions.radius * 8,
                height: Dimensions.radius * 8,
              );
            },
          ),
        ),
      ),
    );
  }

  _balanceBoxWidget() {
    return Padding(
      padding:
          EdgeInsets.symmetric(vertical: Dimensions.marginSizeVertical * 0.3),
      child: Row(
        mainAxisAlignment: mainCenter,
        children: [
          _balanceTextCircleWidget(),
          horizontalSpace(Dimensions.marginSizeHorizontal),
          Column(
            crossAxisAlignment: crossCenter,
            mainAxisAlignment: mainCenter,
            children: [
              Wrap(
                children: [
                  TitleHeading5Widget(
                    text: '${controller.userName.value} ',
                    color: CustomColor.primaryLightColor,
                  ),
                  const TitleHeading5Widget(
                    text: Strings.welcomeBack,
                    fontWeight: FontWeight.w500,
                  ),
                ],
              ),
              Padding(
                padding: EdgeInsets.symmetric(
                    vertical: Dimensions.marginSizeVertical * 0.2),
                child: TitleHeading2Widget(text: controller.fullMobile.value),
              ),
              OutlinedButton(
                  style: ElevatedButton.styleFrom(
                    side:
                        const BorderSide(color: CustomColor.primaryLightColor),
                    shape: RoundedRectangleBorder(
                        borderRadius:
                            BorderRadius.circular(Dimensions.radius * 0.8)),
                  ),
                  onPressed: () {
                    Get.toNamed(Routes.historyScreen);
                  },
                  child: const TitleHeading4Widget(
                    text: Strings.history,
                    color: CustomColor.primaryLightColor,
                  ))
            ],
          ),
        ],
      ),
    );
  }

  _balanceTextCircleWidget() {
    var walletData = controller.getDashboardInfoModel.data.wallets.first;
    return Container(
      decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(Dimensions.radius * 10),
          border: Border.all(width: 2, color: CustomColor.primaryLightColor)),
      child: CircleAvatar(
          radius: Dimensions.radius * 4,
          backgroundColor: CustomColor.whiteColor,
          child: Column(
            mainAxisAlignment: mainCenter,
            children: [
              TitleHeading4Widget(
                text: double.parse(walletData.balance.toString())
                    .toStringAsFixed(2),
                color: CustomColor.primaryDarkColor,
                fontWeight: FontWeight.bold,
              ),
              TitleHeading4Widget(
                text: walletData.currency.code,
                color: CustomColor.primaryLightColor,
                fontWeight: FontWeight.bold,
              ),
            ],
          )),
    );
  }

  _appbarWidget() {
    return PrimaryAppBar(
      Strings.myProfile,
      showBackButton: false,
      appbarSize: Dimensions.heightSize * 3.5,
    );
  }
}
