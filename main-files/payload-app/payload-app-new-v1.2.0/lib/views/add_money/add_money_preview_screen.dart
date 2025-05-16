import 'package:flutter/cupertino.dart';
import 'package:payloadui/views/utils/responsive_layout.dart';
import 'package:payloadui/views/add_money/add_money_preview_mobile_layout_screen.dart';

class AddMoneyPreviewScreen extends StatelessWidget {
  const AddMoneyPreviewScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return ResponsiveLayout(
        mobileScaffold: AddMoneyPreviewMobileLayoutScreen());
  }
}
