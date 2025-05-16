import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/model/auth/register/register_model.dart';
import 'package:payloadui/backend/utils/custom_snackbar.dart';
import '../../../backend/local_storage/local_storage.dart';
import '../../../backend/model/auth/register/country_model.dart';
import '../../../backend/model/auth/register/register_send_otp_model.dart';
import '../../../backend/services/auth/auth_api_service.dart';
import '../../../backend/services/auth/country_list_api_service.dart';
import '../../../backend/utils/api_method.dart';
import '../../../routes/routes.dart';
import '../../basic_setting/basic_setting_controller.dart';

class RegisterController extends GetxController {
  final firstNameController = TextEditingController();
  final lastNameController = TextEditingController();
  final mobileController = TextEditingController();
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  final controller = Get.put(BasicSettingController());

  @override
  void onInit() {
    super.onInit();
    getCountryListProcess();
  }

  final formKey = GlobalKey<FormState>();
  RxBool isPasswordHidden = true.obs;
  RxBool isChecked = false.obs;

  RxString sendCodeToken = ''.obs;

  ///____________________ API CALL _____________________________

  RxString phoneCode = "".obs;
  RxString selectedCountry = "".obs;

  RxBool agreed = false.obs;

  final _isLoading = false.obs;

  bool get isLoading => _isLoading.value;

  late RegisterModel _registerModel;

  RegisterModel get registerModel => _registerModel;

  get onRegister => registerApiProcess();

  ///* Register Process Api
  Future<RegisterModel> registerApiProcess() async {
    _isLoading.value = true;
    update();

    Map<String, dynamic> inputBody = {
      'firstname': firstNameController.text,
      'lastname': lastNameController.text,
      'email': emailController.text,
      'password': passwordController.text.trim(),
      'phone_code': phoneCode.value,
      'mobile': mobileController.text,
      'country': selectedCountry.value,
    };

    await AuthApiServices.registerProcessApi(body: inputBody).then((value) {
      _registerModel = value!;
      _goToSavedUser(registerModel);
    }).catchError((onError) {
      log.e(onError);
    }).whenComplete(() {
      _isLoading.value = false;
      update();
    });

    return _registerModel;
  }

  void _goToSavedUser(RegisterModel registerModel) {
    if (LocalStorage.getSmsVerify() == 1) {
      LocalStorage.saveToken(token: registerModel.data.token);
      getRegisterOtp();
      Get.toNamed(Routes.registerOtpVerifyScreen);
    } else {
      LocalStorage.saveToken(token: registerModel.data.token);
      Get.offAllNamed(Routes.navigationScreen);
    }
  }

  late RegisterSendOtpModel _registerSendOtpModel;

  RegisterSendOtpModel get registerSendOtpModel => _registerSendOtpModel;

  Future<RegisterSendOtpModel> getRegisterOtp() async {
    try {
      final value = await AuthApiServices.getRegisterOtpApi();
      if (value != null) {
        _registerSendOtpModel = value;
        sendCodeToken.value = _registerSendOtpModel.token;
      } else {
        CustomSnackBar.error(
          'Failed to fetch Register Otp Code data: Data is null.',
        );
      }
    } catch (onError) {
      log.e(onError);
    } finally {}
    return _registerSendOtpModel;
  }

  // Get Country List0

  RxList<Country> countryList = <Country>[].obs;

  late CountryModel _countryListModel;

  CountryModel get countryListModel => _countryListModel;

  /// Fetch the country list and update the state
  Future<CountryModel> getCountryListProcess() async {
    await CountryListApiService.getCountryListInfoProcessApi().then((value) {
      _countryListModel = value!;
      _setData(_countryListModel);
    }).catchError((onError) {
      log.e(onError);
    }).whenComplete(() {
      _isLoading.value = false;
      update();
    });

    return _countryListModel;
  }

  void _setData(CountryModel countryModel) {
    countryList.addAll(countryModel.data.countries);
    if (countryList.isNotEmpty) {
      selectedCountry.value = countryList.first.name;
      phoneCode.value = countryList.first.mobileCode;
    }
  }

  @override
  void dispose() {
    firstNameController.dispose();
    lastNameController.dispose();
    emailController.dispose();
    passwordController.dispose();
    mobileController.dispose();
    super.dispose();
  }
}
