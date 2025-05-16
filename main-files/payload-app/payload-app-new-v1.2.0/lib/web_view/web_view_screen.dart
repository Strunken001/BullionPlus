import 'package:flutter/material.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import '../../backend/utils/custom_loading_api.dart';
import '../../languages/strings.dart';
import '../../routes/routes.dart';
import '../../widgets/common/appbar/primary_appbar.dart';
import 'package:get/get.dart';
import '../../congratulation/congratulation_screen.dart';

class WebViewScreen extends StatefulWidget {
  final String url, title;

  const WebViewScreen({super.key, required this.url, required this.title});

  @override
  State<WebViewScreen> createState() => _WebViewScreenState();
}

class _WebViewScreenState extends State<WebViewScreen> {
  late InAppWebViewController webViewController;

  final ValueNotifier<bool> isLoading = ValueNotifier<bool>(true);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: PrimaryAppBar(
        autoLeading: true,
        backgroundColor: CustomColor.whiteColor,
        showBackButton: false,
        widget.title,
      ),
      body: _bodyWidget(context),
    );
  }

  _bodyWidget(BuildContext context) {
    return Stack(
      children: [
        InAppWebView(
          initialUrlRequest: URLRequest(url: WebUri(widget.url)),
          onWebViewCreated: (controller) {
            webViewController = controller;
            controller.addJavaScriptHandler(
              handlerName: 'jsHandler',
              callback: (args) {},
            );
          },
          onLoadStart: (controller, url) {
            isLoading.value = true;
          },
          onLoadStop: (controller, url) {
            isLoading.value = false;
            if (url.toString().contains("/success") ||
                url.toString().contains("/callback")) {
              Get.off(() => const CongratulationScreen(
                    title: Strings.congratulations,
                    subTitle: Strings.congratulationsSubTile,
                    route: Routes.navigationScreen,
                  ));
            } else if (url.toString().contains('/cancel/response')) {
              Get.close(1);
            }
          },
        ),
        ValueListenableBuilder<bool>(
          valueListenable: isLoading,
          builder: (context, isLoading, _) {
            return isLoading
                ? const Center(child: CustomLoadingAPI())
                : const SizedBox.shrink();
          },
        ),
      ],
    );
  }
}
