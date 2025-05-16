import 'package:flutter/cupertino.dart';
import 'package:payloadui/views/setting_screen/setting_mobile_layout_screen.dart';
import 'package:payloadui/views/utils/responsive_layout.dart';

class SettingScreen extends StatelessWidget {
  const SettingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return const ResponsiveLayout(mobileScaffold: SettingMobileLayoutScreen());
  }
}
