import 'package:flutter/cupertino.dart';
import 'package:payloadui/views/auth/registration_screen/register_otp_verify_mobile_layout_screen.dart';
import 'package:payloadui/views/utils/responsive_layout.dart';

class RegisterOtpVerifyScreen extends StatelessWidget {
  const RegisterOtpVerifyScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return ResponsiveLayout(
        mobileScaffold: RegisterOtpVerifyMobileLayoutScreen());
  }
}
