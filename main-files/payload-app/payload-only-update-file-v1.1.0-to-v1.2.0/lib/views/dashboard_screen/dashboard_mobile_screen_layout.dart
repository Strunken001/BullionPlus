import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/views/utils/size.dart';
import 'package:payloadui/widgets/common/inputs/custom_amount_input_widget.dart';
import 'package:payloadui/widgets/common/appbar/custom_appbar_widget.dart';
import 'package:payloadui/widgets/common/others/custom_info_card_widget.dart';
import '../../backend/utils/custom_loading_api.dart';
import '../../controller/dashboard/dashboard_controller.dart';
import '../../controller/add_money/add_money_screen_controller.dart';
import '../../languages/strings.dart';
import '../../widgets/common/text_labels/title_heading2_widget.dart';
import '../../widgets/home_widgets/balance_box_widget.dart';
import '../../widgets/home_widgets/custom_recharge_amount_widget.dart';
import '../../widgets/home_widgets/slider_image_box_widget.dart';
import '../drawer_menu_screen/drawer_menu_mobile_layout_screen.dart';

class DashboardMobileScreenLayout extends StatelessWidget {
  DashboardMobileScreenLayout({
    super.key,
  });
  final GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey<ScaffoldState>();
  final controller = Get.put(DashboardController());
  final wallerController = Get.put(AddMoneyScreenController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      key: _scaffoldKey,
      drawer: MyDrawerMenu(),
      body: Obx(
        () => controller.isLoading || wallerController.isLoading
            ? const CustomLoadingAPI()
            : RefreshIndicator(
                color: CustomColor.primaryLightColor,
                backgroundColor: CustomColor.whiteColor,
                onRefresh: () async {
                  await controller.getDashboardProcessApi();
                },
                child: _bodyWidget(context),
              ),
      ),
    );
  }

  _bodyWidget(BuildContext context) {
    return SingleChildScrollView(
      child: Column(
        children: [
          appBarWidget(context),
          _sliderImageBox(),
          _balanceRelatedAllWidget(context),
          _myOfferBoxWidget(context)
        ],
      ),
    );
  }

  appBarWidget(BuildContext context) {
    return CustomAppBarWidget(
      onTap: () {
        _scaffoldKey.currentState!.openDrawer();
      },
      defaultImage: controller.defaultImage.value,
      profilePath: controller.image.value,
      title: controller.userName.value,
      subtitle: controller.fullMobile.value,
    );
  }

  _sliderImageBox() {
    return SliderImageBoxWidget(
      pageController: controller.pageController,
      imageList: controller.imageList,
      currentIndex: controller.currentIndex,
    );
  }

  _balanceRelatedAllWidget(context) {
    var walletData = controller.getDashboardInfoModel.data.wallets.first;
    var amountsList = controller.getDashboardInfoModel.data.rechargeBttn;
    return Column(
      children: [
        Container(
          decoration: BoxDecoration(
              color: CustomColor.whiteColor,
              borderRadius: BorderRadius.circular(
            Dimensions.radius,
          )),
          margin:
              EdgeInsets.symmetric(vertical: Dimensions.marginSizeVertical * 0.5,
              horizontal: Dimensions.marginSizeHorizontal
              ),
          padding: EdgeInsets.symmetric(
            horizontal: Dimensions.marginSizeHorizontal,
            vertical: Dimensions.marginSizeVertical * 0.5,
          ),
          child: BalanceBoxWidget(
            currency: controller.currency.value,
            currentBalance:
                double.parse(walletData.balance.toString()).toStringAsFixed(2),
            topUpCount: controller.mobileTopUpCount.value.toString(),
            gifCardCount: controller.giftCardCount.value.toString(),
            addMoneyCount: controller.addMoneyCount.value.toString(),
          ),
        ),
        Container(
          decoration: BoxDecoration(
              color: CustomColor.whiteColor,
              borderRadius: BorderRadius.circular(
                Dimensions.radius,
              ),
          ),
          margin: EdgeInsets.only(bottom: Dimensions.marginSizeVertical * 0.5,
              left: Dimensions.marginSizeHorizontal,
              right: Dimensions.marginSizeHorizontal,
          ),
          padding: EdgeInsets.symmetric(
            horizontal: Dimensions.marginSizeHorizontal,
            vertical: Dimensions.marginSizeVertical * 0.5,
          ),
          child: Column(
            children: [
              RechargeAmountInputWidget(
                fieldController: TextEditingController(),
                rechargeAmounts: amountsList.take(5).toList(),
                onAmountSelected: (value) {
                  controller.inputAmountController.text = value;
                  controller.selectedAmount.value = value;
                },
              ),
              verticalSpace(Dimensions.heightSize),
              Row(
                mainAxisAlignment: mainSpaceBet,
                children: amountsList.asMap().entries.map((entry) {
                  final index = entry.key;
                  final amount = entry.value;
                  return CustomRechargeAmountWidget(
                    text: amount,
                    onPressed: () {
                      controller.isSelectedAmount.value = !controller.isSelectedAmount.value;
                      controller.inputAmountController.text = amount;
                      controller.selectedAmount.value = amount;
                      Get.toNamed(Routes.walletRechargeScreen);
                    },
                  );
                }).toList(),
              ),

            ],
          ),
        ),
      ],
    );
  }

  _myOfferBoxWidget(BuildContext context) {
    return Container(
      height: MediaQuery.of(context).size.height * 0.3,
      width: double.infinity,
      decoration: const BoxDecoration(
        color: CustomColor.whiteColor,
      ),
      child: Padding(
        padding: EdgeInsets.symmetric(
            horizontal: Dimensions.marginSizeHorizontal,
            vertical: Dimensions.marginSizeVertical * 0.5),
        child: Column(
          crossAxisAlignment: crossStart,
          children: [
            TitleHeading2Widget(
              padding: EdgeInsets.only(bottom: Dimensions.heightSize),
              text: Strings.myOffers,
              fontWeight: FontWeight.w500,
            ),
            const CustomInfoCardWidget(
              title: Strings.unlimitedLife,
              subtitle: Strings.uninterruptedInternet,
              buttonText: Strings.buyNow,
            )
          ],
        ),
      ),
    );
  }
}
