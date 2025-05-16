import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/controller/data_bundles/data_bundles_screen_controller.dart';
import 'package:payloadui/languages/strings.dart';
import '../views/utils/custom_color.dart';
import '../views/utils/dimensions.dart';
import 'common/text_labels/title_heading4_widget.dart';

class BundleCardWidget extends StatelessWidget {
  BundleCardWidget({super.key});

  final controller = Get.put(DataBundlesScreenController());

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: ListView.builder(
        itemCount: controller.fixedAmountList.length,
        itemBuilder: (context, index) {
          return Container(
              margin: EdgeInsets.only(bottom: Dimensions.heightSize),
              width: double.maxFinite,
              padding: EdgeInsets.symmetric(
                  vertical: Dimensions.heightSize,
                  horizontal: Dimensions.widthSize * 1.5),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(Dimensions.radius),
                color: CustomColor.whiteColor,
                border: Border.all(color: CustomColor.greyColor),
              ),
              child: ListTile(
                trailing: InkWell(
                  splashColor: Colors.transparent,
                  highlightColor: Colors.transparent,
                  onTap: () {
                    controller.selectIndex.value = index;
                    controller.selectedAmount.value =
                        controller.fixedAmountList[index].amount;
                    controller.getChargesInfoProcess();
                  },
                  child: Obx(
                        () =>
                    controller.selectIndex.value == index &&
                        controller.isLoading2
                        ? CircularProgressIndicator(
                      color: CustomColor.primaryLightColor,
                    )
                        : Container(
                      decoration: BoxDecoration(
                          color: CustomColor.primaryLightColor,
                          borderRadius:
                          BorderRadius.circular(Dimensions.radius)),
                      child: TitleHeading4Widget(
                        text: Strings.buyNow,
                        fontWeight: FontWeight.w500,
                        color: CustomColor.whiteColor,
                      ),
                      padding: EdgeInsets.symmetric(
                          horizontal:
                          Dimensions.marginSizeHorizontal * 0.6,
                          vertical: Dimensions.heightSize * 0.4),
                    ),
                  ),
                ),
                contentPadding: EdgeInsets.zero,
                title: TitleHeading4Widget(
                  maxLines: 2,
                  text: '${controller.fixedAmountList[index].details}',
                  fontWeight: FontWeight.w600,
                ),
              ));
        },
      ),
    );
  }
}
