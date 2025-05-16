import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/data_bundles/data_bundles_screen_controller.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/views/utils/size.dart';
import 'package:payloadui/widgets/common/buttons/primary_button.dart';
import 'package:payloadui/widgets/common/inputs/primary_input_filed.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading2_widget.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading4_widget.dart';
import '../../controller/dashboard/dashboard_controller.dart';
import '../../languages/strings.dart';
import '../../widgets/common/others/item_card_widget.dart';
import '../utils/custom_color.dart';

class BundlePreviewMobileScreenLayout extends StatelessWidget {
  BundlePreviewMobileScreenLayout({super.key});

  final controller = Get.put(DataBundlesScreenController());
  final dashBoardController = Get.put(DashboardController());

  @override
  Widget build(BuildContext context) {
    controller.numberController.text = dashBoardController.fullMobile.value;
    return Scaffold(
      bottomNavigationBar: Padding(
          padding: EdgeInsets.symmetric(
              horizontal: Dimensions.marginSizeHorizontal,
              vertical: Dimensions.heightSize * 2),
          child: Obx(
            () => controller.isSubmitLoading
                ? CustomLoadingAPI()
                : PrimaryButton(
                    title: Strings.buyNow2,
                    fontWeight: FontWeight.bold,
                    onPressed: () {
                      controller.buyDataBundle();
                    },
                  ),
          )),
      appBar: AppBar(
        title: TitleHeading2Widget(
          text: Strings.dataBundle,
        ),
        centerTitle: true,
        automaticallyImplyLeading: true,
        backgroundColor: CustomColor.whiteColor,
      ),
      body: Column(
        crossAxisAlignment: crossStart,
        children: [
          ItemCardWidget(
              firstTitle: Strings.operatorName,
              lastTile: controller.selectedOperatorData.value!.name,
              lastTextColor: CustomColor.greenColor,
              icon: Icons.sim_card_download),
          ItemCardWidget(
              firstTitle: Strings.bundle,
              lastTile: controller.SelectedBundle.value,
              lastTextColor: CustomColor.primaryDarkColor,
              icon: Icons.card_giftcard_sharp),
          ItemCardWidget(
            currency: controller.userSelectedCurrency.value,
            firstTitle: Strings.exchangeRate,
            lastTile: controller.exchangeRate.value,
            lastTextColor: CustomColor.primaryLightColor,
            icon: Icons.add_card_sharp,
          ),
          ItemCardWidget(
            currency: controller.userSelectedCurrency.value,
            firstTitle: Strings.totalPayable,
            lastTile: controller.totalPayable.value,
            lastTextColor: CustomColor.primaryLightColor,
            icon: Icons.add_card_sharp,
          ),
          TitleHeading4Widget(
            padding: EdgeInsets.symmetric(
                horizontal: Dimensions.marginSizeHorizontal),
            text: Strings.forNumber,
            fontWeight: FontWeight.bold,
          ),
          Padding(
            padding: EdgeInsets.symmetric(
                horizontal: Dimensions.marginSizeHorizontal),
            child: PrimaryInputWidget(
              keyboardType: TextInputType.number,
              prefixIcon: Icon(Icons.phone_android_outlined),
              controller: controller.numberController,
              hint: Strings.mobileNumber,
            ),
          ),
        ],
      ),
    );
  }
}
