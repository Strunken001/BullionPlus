import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../languages/strings.dart';
import '../../routes/routes.dart';
import '../../views/utils/custom_color.dart';
import '../../views/utils/dimensions.dart';
import '../../views/utils/size.dart';
import '../common/text_labels/title_heading3_widget.dart';
import '../common/text_labels/title_heading4_widget.dart';

class BalanceBoxWidget extends StatelessWidget {
  final String currentBalance;
  final String currency;

  final String topUpCount;
  final String gifCardCount;
  final String addMoneyCount;

  const BalanceBoxWidget({
    super.key,
    required this.currentBalance,
    required this.currency,
    required this.topUpCount,
    required this.gifCardCount,
    required this.addMoneyCount,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
        padding: EdgeInsets.only(bottom: Dimensions.heightSize * 0.5),
        child: Column(
          children: [
            _balanceAndHistoryButton(),
            verticalSpace(Dimensions.heightSize * 0.5),
            _allPackCount(),
          ],
        ));
  }

  _allPackCount() {
    return Row(
      mainAxisAlignment: mainSpaceBet,
      children: [
        CircleAvatar(
          backgroundColor: const Color(0xffECE5FF),
          radius: Dimensions.radius,
          child: Icon(
            Icons.phone_android,
            color: const Color(0xff7A52F0),
            size: Dimensions.heightSize,
          ),
        ),
        horizontalSpace(Dimensions.widthSize * 0.8),
        Row(
          children: [
            TitleHeading3Widget(
              fontSize: Dimensions.headingTextSize5,
              padding: EdgeInsets.only(right: Dimensions.widthSize * 0.5),
              text: topUpCount,
            ),
            TitleHeading3Widget(
                fontSize: Dimensions.headingTextSize6,
                text: DynamicLanguage.isLoading
                    ? ''
                    : DynamicLanguage.key(Strings.topUp)),
          ],
        ),
        Container(
          margin: EdgeInsets.only(
              left: Dimensions.widthSize * 0.5, right: Dimensions.widthSize * 0.5),
          height: Dimensions.heightSize * 1.6,
          width: 2,
          color: CustomColor.greyColor.withOpacity(0.6),
        ),
        CircleAvatar(
          backgroundColor: const Color(0xffFFE9D6),
          radius: Dimensions.radius,
          child: Icon(
            Icons.attach_money_sharp,
            color: const Color(0xffFF6411),
            size: Dimensions.heightSize * 1.2,
          ),
        ),
        horizontalSpace(Dimensions.widthSize * 0.8),
        Row(
          children: [
            TitleHeading3Widget(
              fontSize: Dimensions.headingTextSize5,
              padding: EdgeInsets.only(right: Dimensions.widthSize * 0.5),
              text: addMoneyCount,
            ),
            TitleHeading3Widget(
                fontSize: Dimensions.headingTextSize6,
                text: DynamicLanguage.isLoading
                    ? ''
                    : DynamicLanguage.key(Strings.money)),
          ],
        ),
        Container(
          margin: EdgeInsets.only(
              left: Dimensions.widthSize * 0.5, right: Dimensions.widthSize * 0.5),
          height: Dimensions.heightSize * 1.6,
          width: 2,
          color: CustomColor.greyColor.withOpacity(0.6),
        ),
        CircleAvatar(
            backgroundColor: const Color(0xffDAF7FF),
            radius: Dimensions.radius,
            child: Icon(
              Icons.credit_card_sharp,
              color: const Color(0xff11667D),
              size: Dimensions.heightSize,
            )),
        horizontalSpace(Dimensions.widthSize * 0.8),
        Row(
          children: [
            TitleHeading3Widget(
              fontSize: Dimensions.headingTextSize5,
              padding: EdgeInsets.only(right: Dimensions.widthSize * 0.5),
              text: gifCardCount,
            ),
            TitleHeading3Widget(
              maxLines: 1,
                textOverflow: TextOverflow.ellipsis,
                fontSize: Dimensions.headingTextSize6,
                text: DynamicLanguage.isLoading
                    ? ''
                    : DynamicLanguage.key(Strings.gifCard)),
          ],
        )
      ],
    );
  }

  _balanceAndHistoryButton() {
    return Row(
      mainAxisAlignment: mainSpaceBet,
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            TitleHeading4Widget(
              text: Strings.currentBalance,
              fontSize: Dimensions.headingTextSize5,
              color: CustomColor.secondaryTextColor,
              fontWeight: FontWeight.normal,
            ),
            Padding(
              padding: EdgeInsets.symmetric(
                  vertical: Dimensions.marginSizeVertical * 0.2),
              child: Row(
                children: [
                  TitleHeading3Widget(
                    fontSize: Dimensions.headingTextSize2,
                    text: currentBalance,
                  ),
                  horizontalSpace(Dimensions.widthSize * 0.5),
                  TitleHeading3Widget(
                    fontSize: Dimensions.headingTextSize2,
                    text: currency,
                    color: CustomColor.primaryLightColor,
                  ),
                ],
              ),
            ),
          ],
        ),
        SizedBox(
          height: Dimensions.heightSize * 2,
          child: OutlinedButton(
            style: OutlinedButton.styleFrom(
                side: const BorderSide(
                  color: CustomColor.greyColor,
                  width: 1.2,
                ),
                shape: RoundedRectangleBorder(
                    borderRadius:
                        BorderRadius.circular(Dimensions.radius * 0.8))),
            onPressed: () {
              Get.toNamed(Routes.historyScreen);
            },
            child: TitleHeading3Widget(
              text: Strings.history,
              fontWeight: FontWeight.normal,
              color: CustomColor.secondaryTextColor,
              fontSize: Dimensions.headingTextSize5,
            ),
          ),
        ),
      ],
    );
  }
}
