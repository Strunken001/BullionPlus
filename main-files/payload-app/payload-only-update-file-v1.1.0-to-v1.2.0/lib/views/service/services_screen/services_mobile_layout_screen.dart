import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/routes/routes.dart';
import 'package:payloadui/widgets/common/others/custom_services_box_widget.dart';
import '../../../controller/services/service_screen_controller.dart';
import '../../../languages/strings.dart';
import '../../../widgets/common/text_labels/title_heading4_widget.dart';
import '../../utils/custom_color.dart';
import '../../utils/dimensions.dart';
import '../../utils/size.dart';

class ServicesMobileLayoutScreen extends StatelessWidget {
  ServicesMobileLayoutScreen({super.key});

  final _controller = Get.put(ServicesController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _bodyWidget(context),
    );
  }

  _bodyWidget(BuildContext context) {
    return Column(
      children: [
        _itemsCardBoxWidget(context),
      ],
    );
  }

  _itemsCardBoxWidget(BuildContext context) {
    return Container(
      padding: EdgeInsets.symmetric(
          horizontal: Dimensions.marginSizeHorizontal,
          vertical: Dimensions.marginSizeVertical * 2),
      width: double.maxFinite,
      color: CustomColor.whiteColor,
      child: Column(
        crossAxisAlignment: crossStart,
        children: [
          const TitleHeading4Widget(
            text: Strings.ourServices,
          ),
          _iconAndTextWidget1(),
        ],
      ),
    );
  }

  _iconAndTextWidget1() {
    return Padding(
      padding: EdgeInsets.symmetric(
          horizontal: Dimensions.marginSizeHorizontal * 0.25,
          vertical: Dimensions.marginSizeVertical * 0.4),
      child: Row(
        mainAxisAlignment: mainSpaceBet,
        children: [
          CustomServicesBoxWidget(
              onPressed: () {
                Get.toNamed(Routes.giftCardScreen);
              },
              iconPath: Icons.card_giftcard,
              title: Strings.giftCard),
          CustomServicesBoxWidget(
              onPressed: () {
                Get.toNamed(Routes.dataBundlesScreen);
              },
              iconPath: Icons.data_array_sharp,
              title: Strings.dataBundle),
          CustomServicesBoxWidget(
              onPressed: () {
                _controller.backToRecharge();
              },
              iconPath: Icons.phone_android_outlined,
              title: Strings.recharge),
        ],
      ),
    );
  }
}
