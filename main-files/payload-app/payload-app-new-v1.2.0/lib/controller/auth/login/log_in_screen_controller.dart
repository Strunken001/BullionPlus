import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/local_storage/local_storage.dart';
import 'package:payloadui/backend/model/auth/register/country_model.dart';
import 'package:payloadui/controller/auth/register/register_screen_controller.dart';
import '../../../backend/model/auth/sign_in/login_with_otp.dart';
import '../../../backend/model/auth/sign_in/login_with_password_model.dart';
import '../../../backend/services/auth/auth_api_service.dart';
import '../../../backend/services/auth/country_list_api_service.dart';
import '../../../backend/utils/api_method.dart';
import '../../../languages/strings.dart';
import '../../../routes/routes.dart';

class LogInController extends GetxController {
  final passwordController = TextEditingController();
  final otpNumberController = TextEditingController();
  final numberController = TextEditingController();
  final GlobalKey<FormState> formKey1 = GlobalKey<FormState>();
  final registerController = Get.put(RegisterController());

  RxInt twoFAStatus = 0.obs;

  RxInt myIndex = 0.obs;
  RxBool isPasswordHidden = true.obs;
  RxInt smsVerified = 0.obs;

  @override
  void onInit() {
    mobileCode.value = "880";
    numberController.text = '123456789';
    passwordController.text = 'appdevs';
    super.onInit();
    getCountryListProcess();
  }

  final List buttonTextList = [
    DynamicLanguage.key(
      Strings.otp,
    ),
    DynamicLanguage.key(
      Strings.password,
    )
  ];

  customButtonOnchange(int index) {
    myIndex.value = index;
  }

  void signInProcessApiAndCheckValidation() async {
    setSwitchingValue();

    if (myIndex.value == 0) {
      if (formKey1.currentState!.validate()) {
        await LogInOtpProcess();
      }
    } else {
      logInPasswordProcess();
    }
  }

  void setSwitchingValue() {
    if (myIndex.value == 0) {
      switching.value = 'otp';
    } else {
      switching.value = 'password';
    }
  }

  ///____________________ API CALL FOR PASSWORD LOGIN AND OTP_____________________________

  final _isLoading = false.obs;

  bool get isLoading => _isLoading.value;

  late LoginWithPasswordModel _loginWithPasswordModel;
  late LoginWithOtpModel _loginWithOTPModel;

  LoginWithPasswordModel get loginWithPasswordModel => _loginWithPasswordModel;

  LoginWithOtpModel get loginWithOTPModel => _loginWithOTPModel;

  RxString switching = ''.obs;

//Login With Password

  Future<LoginWithPasswordModel> logInPasswordProcess() async {
    _isLoading.value = true;
    update();

    Map<String, dynamic> inputBody = {
      'otp_number': otpNumberController.text,
      'password': passwordController.text,
      'password_number': '${mobileCode.value}${numberController.text}',
      'switch': switching.value,
    };

    await AuthApiServices.logInProcessApi(body: inputBody).then((value) {
      _loginWithPasswordModel = value!;
      _isLoading.value = false;

      smsVerified.value = _loginWithPasswordModel.data.userInfo.smsVerified;
      twoFAStatus.value = _loginWithPasswordModel.data.userInfo.twoFactorStatus;
      LocalStorage.saveToken(token: _loginWithPasswordModel.data.token);

      if (twoFAStatus.value == 1) {
        Get.toNamed(Routes.twoFaOtpVerifyScreen);
      } else {
        if (LocalStorage.getSmsVerify() == 1 && smsVerified.value == 0) {
          registerController.getRegisterOtp().then(
            (value) {
              Get.toNamed(Routes.registerOtpVerifyScreen);
            },
          );
        } else {
          Get.offAllNamed(Routes.navigationScreen);
        }
      }

      update();
    }).catchError((onError) {
      log.e(onError);
    });
    _isLoading.value = false;
    update();
    return _loginWithPasswordModel;
  }

  //Login With OTP

  RxInt userId = 0.obs;
  RxString type = "".obs;

  Future<LoginWithOtpModel> LogInOtpProcess() async {
    _isLoading.value = true;
    update();

    Map<String, dynamic> inputBody = {
      'otp_number': '${mobileCode.value}${otpNumberController.text}',
      'switch': switching.value,
    };

    await AuthApiServices.signInOtpProcessApi(body: inputBody).then((value) {
      _loginWithOTPModel = value!;
      _isLoading.value = false;
      Get.toNamed(Routes.otpVerificationScreen);
      userId.value = _loginWithOTPModel.data.userInfo.id;
      type.value = _loginWithOTPModel.data.type;

      update();
    }).catchError((onError) {
      log.e(onError);
    });
    _isLoading.value = false;
    update();
    return _loginWithOTPModel;
  }

  /// => ----------- GET COUNTRY API ---------------------------------

  final List<Country> countryNameList = [];
  RxString mobileCode = "".obs;
  RxString selectedCountry = "".obs;

  late CountryModel _countryListModel;

  CountryModel get countryListModel => _countryListModel;

  Future<CountryModel> getCountryListProcess() async {
    await CountryListApiService.getCountryListInfoProcessApi().then((value) {
      _countryListModel = value!;
      for (var element in _countryListModel.data.countries) {
        countryNameList.add(
          Country(
              mobileCode: element.mobileCode,
              currencyCode: element.currencyCode,
              currencyName: element.currencyName,
              currencySymbol: element.currencySymbol,
              id: element.id,
              iso2: element.iso2,
              name: element.name),
        );
      }

      // mobileCode.value = _countryListModel.data.countries.first.mobileCode;
      // mobileCode.value = "+44";
      mobileCode.value = _countryListModel.data.countries
          .firstWhere((value) => value.mobileCode == "93")
          .mobileCode;
      // selectedCountry.value = _countryListModel.data.countries.first.name;
      // selectedCountry.value = "United Kingdom";
      selectedCountry.value = _countryListModel.data.countries
          .firstWhere((value) => value.mobileCode == "93")
          .name;
    }).catchError((onError) {
      log.e(onError);
    }).whenComplete(() {
      _isLoading.value = false;
      update();
    });

    return _countryListModel;
  }

  @override
  void dispose() {
    otpNumberController.dispose();
    numberController.dispose();
    passwordController.dispose();
    super.dispose();
  }
}
