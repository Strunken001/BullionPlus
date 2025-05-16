import 'dart:async';
import 'package:get/get.dart';
import 'package:payloadui/backend/local_storage/local_storage.dart';
import 'package:payloadui/extensions/custom_extensions.dart';
import '../../backend/model/bassic_setting/basic_setting_model.dart';
import '../../backend/services/basic_setting/basic_setting_api_service.dart';
import '../../backend/utils/api_method.dart';
import '../../routes/routes.dart';

class BasicSettingController extends GetxController {
  RxBool isVisible = false.obs;
  List<dynamic> itemsList = [];
  RxString selectedLanguage = ''.obs;
  RxInt agreePolicy = 0.obs;
  RxString splashImage = ''.obs;
  RxString onBoardImage = ''.obs;
  RxString defaultImg = ''.obs;
  RxInt userRegister = 0.obs;

  get onGetStarted => _onGetStarted();

  _onGetStarted() {
    Routes.signInScreen.offAllNamed;
    LocalStorage.saveOnboardDoneOrNot(isOnBoardDone: true);
  }

  @override
  void onInit() {
    getBasicSettingProcessApi().then(
      (value) => Timer(const Duration(seconds: 10), () {
        isVisible.value = true;
      }),
    );
    super.onInit();
  }

  final _isLoading = false.obs;

  bool get isLoading => _isLoading.value;

  late BasicSettingModel _basicSettingModel;

  BasicSettingModel get getBasicSettingModel => _basicSettingModel;

  // Fetch Basic Settings from API
  Future<BasicSettingModel> getBasicSettingProcessApi() async {
    _isLoading.value = true;
    try {
      final value = await BasicSettingsApiServices.getBasicSettingProcessApi();
      if (value != null) {
        _basicSettingModel = value;
        _setData(_basicSettingModel);
        agreePolicy.value = _basicSettingModel.data.basicSettings.agreePolicy;
      } else {
        log.e('Failed to fetch Basic Setting data: Data is null.');
      }
    } catch (onError) {
      log.e(onError);
    } finally {
      _isLoading.value = false;
    }
    return _basicSettingModel;
  }

  // Set Image Paths from API Response
  void _setData(BasicSettingModel basicSettingModel) {
    var baseURL = getBasicSettingModel.data.appImagePaths;
    var splashScreenImg = getBasicSettingModel.data.splashScreen;
    var onBoardScreenImg = getBasicSettingModel.data.onboardScreens.first;
    var webLink = basicSettingModel.data.webLinks;
    LocalStorage.saveSmsVerify(
        id: basicSettingModel.data.basicSettings.smsVerification);

    LocalStorage.savePrivacyPolicyLink(link: webLink.privacyPolicy);
    LocalStorage.saveAboutUsLink(link: webLink.aboutUs);
    LocalStorage.saveContactUsLink(link: webLink.contactUs);

    userRegister.value = basicSettingModel.data.basicSettings.userRegistration;

    splashImage.value =
        '${baseURL.baseUrl}/${baseURL.pathLocation}/${splashScreenImg.image}';
    onBoardImage.value =
        '${baseURL.baseUrl}/${baseURL.pathLocation}/${onBoardScreenImg.image}';
    defaultImg.value = '${baseURL.baseUrl}/${baseURL.defaultImage}';
  }
}
