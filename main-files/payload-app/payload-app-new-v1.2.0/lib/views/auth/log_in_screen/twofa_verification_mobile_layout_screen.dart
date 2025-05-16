import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/auth/two_factor/two_fa_controller.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/appbar/primary_appbar.dart';
import 'package:payloadui/widgets/common/buttons/primary_button_widget.dart';
import 'package:payloadui/widgets/common/inputs/primary_input_filed.dart';
import 'package:payloadui/widgets/common/others/custom_copy_box_widget.dart';
import '../../../languages/strings.dart';
import '../../utils/custom_color.dart';

class TwofaVerificationMobileLayoutScreen extends StatelessWidget {
  TwofaVerificationMobileLayoutScreen({super.key});

  final _controller = Get.put(TwoFaController());

  @override
  Widget build(BuildContext context) {
    print(_controller.status.value);
    return Scaffold(
      appBar: const PrimaryAppBar(
        Strings.twoFactorAuthenticator,
        autoLeading: true,
        showBackButton: false,
      ),
      body: Obx(
        () => _controller.isLoading
            ? const CustomLoadingAPI()
            : _bodyWidget(context),
      ),
    );
  }

  _bodyWidget(BuildContext context) {
    return Padding(
      padding:
          EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
      child: Column(
        children: [
          _fieldWidget(context),
          _qrCodeImage(),
          _buttonWidget(),
        ],
      ),
    );
  }

  _buttonWidget() {
    return Obx(() => _controller.isSubmitLoading
        ? const CustomLoadingAPI()
        : PrimaryButtonWidget(
            buttonText: _controller.status.value == 1
                ? Strings.disable
                : Strings.enable,
            onPressed: () {
              _controller.twoFaStatusUpdateProcess();
              print(_controller.status.value);
            },
          ));
  }

  _qrCodeImage() {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: Dimensions.paddingSize),
      child: Image.network(_controller.qrCodeImage.value),
    );
  }

  _fieldWidget(BuildContext context) {
    return PrimaryInputWidget(
      controller: _controller.authController,
      hint: "",
      suffixIcon: InkWell(
          onTap: () async {
            Clipboard.setData(
                    ClipboardData(text: _controller.authController.text))
                .then((_) {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text(
                    Strings.textCopy,
                    style: TextStyle(color: CustomColor.primaryLightColor),
                  ),
                ),
              );
            });
          },
          child: const CustomCopyBoxWidget(icon: Icons.copy)),
    );
  }
}
