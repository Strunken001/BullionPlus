import 'package:flutter/material.dart';
import 'package:payloadui/views/history_screen/history_mobile_layout_screen.dart';
import 'package:payloadui/views/utils/responsive_layout.dart';

class HistoryScreen extends StatelessWidget {
  const HistoryScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return ResponsiveLayout(mobileScaffold: HistoryMobileLayoutScreen());
  }
}
