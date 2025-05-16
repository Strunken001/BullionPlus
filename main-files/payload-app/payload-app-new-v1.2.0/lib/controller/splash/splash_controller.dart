import 'package:get/get.dart';
import '../../../routes/routes.dart';
import '../../backend/local_storage/local_storage.dart';
import 'navigator_plush.dart';

class SplashController extends GetxController {
  final navigatorPlug = NavigatorPlug();

  @override
  void onReady() {
    super.onReady();
    navigatorPlug.startListening(
      seconds: 5,
      onChanged: () {
        LocalStorage.isLoggedIn()
            ? Get.offAndToNamed(Routes.signInScreen)
            : Get.offAndToNamed(
                Routes.onboardScreen,
              );
      },
    );
  }

  @override
  void onClose() {
    navigatorPlug.stopListening();
    super.onClose();
  }
}
