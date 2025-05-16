import 'package:flutter/material.dart';
import 'package:payloadui/views/bundle_preview_screen/bundle_preview_mobile_screen_layout.dart';
import 'package:payloadui/views/utils/responsive_layout.dart';

class BundlePreviewScreen extends StatelessWidget {
  const BundlePreviewScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return ResponsiveLayout(mobileScaffold: BundlePreviewMobileScreenLayout());
  }
}
