import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/appbar/primary_appbar.dart';
import '../../../languages/strings.dart';
import '../../../routes/routes.dart';
import '../../../widgets/common/dropdown_field/country_gift_card_dropdown.dart';
import '../../../widgets/common/others/custom_gift_card_widget.dart';
import '../../controller/gift_card/add_gift_card_screen_controller.dart';

class AddGiftCardMobileLayoutScreen extends StatelessWidget {
  AddGiftCardMobileLayoutScreen({super.key});

  final controller = Get.put(AddGiftCardController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _appbarWidget(),
      body: Obx(
        () => controller.isLoading
            ? const CustomLoadingAPI()
            : _bodyWidget(context),
      ),
    );
  }

  _appbarWidget() {
    return PrimaryAppBar(
      appbarSize: Dimensions.heightSize * 3.5,
      Strings.addGiftCards,
      showBackButton: false,
      autoLeading: true,
    );
  }

  _bodyWidget(context) {
    return Padding(
      padding:
          EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
      child: Column(
        children: [
          CountryGiftCardDropdown(
            label: DynamicLanguage.key(
              Strings.selectCountry,
            ),
            selectMethod: controller.selectedCountry.value,
            itemsList: controller.allGiftCardModel.data.countries,
            onChanged: (value) {
              controller.countryName.value = value!.name;
              controller.phoneCode.value = value.mobileCode;
              controller.countryCode.value = value.iso2;
              controller.selectedCountry.value = value.name;
              controller.getAllGiftCardProcess();
            },
          ),
          Expanded(
              child: GridView.builder(
            itemCount: controller.allGiftCard.length,
            gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
              mainAxisSpacing: Dimensions.heightSize * 0.8,
              crossAxisSpacing: Dimensions.widthSize,
              crossAxisCount: 2,
              childAspectRatio: 3 / 2.6,
            ),
            itemBuilder: (context, index) {
              final giftCard = controller.allGiftCard[index];
              return InkWell(
                splashColor: Colors.transparent,
                highlightColor: Colors.transparent,
                onTap: () {
                  controller.productId2.value = giftCard.productId.toString();
                  Get.toNamed(Routes.giftCardDetailsScreen);
                },
                child: CustomGiftCardWidget(
                  imagePaths: giftCard.logoUrls,
                  titleText: giftCard.productName,
                ),
              );
            },
          )),
        ],
      ),
    );
  }
}
