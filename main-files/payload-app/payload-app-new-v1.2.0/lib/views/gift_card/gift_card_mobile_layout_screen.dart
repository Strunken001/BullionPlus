import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/gift_card/my_gift_card_controller.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/appbar/primary_appbar.dart';
import 'package:payloadui/widgets/common/others/custom_cards_widget.dart';

import '../../languages/strings.dart';
import '../../widgets/common/text_labels/title_heading3_widget.dart';

class GiftCardMobileLayoutScreen extends StatelessWidget {
  GiftCardMobileLayoutScreen({super.key});

  final controller = Get.put(MyGiftCardController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _appbarWidget(),
      body: Obx(
        () => controller.isLoading
            ? const CustomLoadingAPI()
            : _bodyWidget(context),
      ),
      floatingActionButton: _buttonWidget(),
    );
  }

  _appbarWidget() {
    return PrimaryAppBar(
      Strings.giftCard,
      showBackButton: true,
      leading: InkWell(
          onTap: () {
            Get.toNamed(Routes.navigationScreen);
          },
          child: const Icon(Icons.arrow_back)),
    );
  }

  _bodyWidget(context) {
    return controller.myGiftCard.isNotEmpty
        ? ListView.builder(
            itemCount: controller.myGiftCard.length,
            itemBuilder: (context, index) {
              final giftCard = controller.myGiftCard[index];
              return CustomCardWidget(
                imagePath: giftCard.cardImage,
                title: giftCard.cardName,
                amount: giftCard.cardInitPrice,
                currency: giftCard.cardCurrency,
              );
            },
          )
        : const Center(
            child: TitleHeading3Widget(text: Strings.nothingToShowYet));
  }

  _buttonWidget() {
    return FloatingActionButton(
      backgroundColor: CustomColor.primaryLightColor,
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(Dimensions.radius)),
      onPressed: () {
        Get.toNamed(Routes.addGiftCardScreen);
      },
      child: const Icon(
        Icons.add,
        color: CustomColor.whiteColor,
      ),
    );
  }
}
