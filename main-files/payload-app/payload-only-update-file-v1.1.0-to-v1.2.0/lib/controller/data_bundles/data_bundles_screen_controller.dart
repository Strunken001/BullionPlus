import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/model/data_bundle/data_bundle_info_model.dart';
import 'package:payloadui/backend/services/data_bundle/data_bundle_api.dart';
import 'package:payloadui/controller/profile/update_profile_controller.dart';
import '../../backend/model/common/common_success_model.dart';
import '../../backend/model/data_bundle/get_charges_model.dart';
import '../../backend/utils/api_method.dart';
import '../../congratulation/congratulation_screen.dart';
import '../../languages/strings.dart';
import '../../routes/routes.dart';

class DataBundlesScreenController extends GetxController {
  final controller = Get.put(UpdateProfileController());
  final numberController = TextEditingController();
  RxString currency = "".obs;
  RxDouble amount = 0.0.obs;
  RxString paymentMethod = "".obs;
  RxString webUrls = ''.obs;
  RxString userSelectedCurrency = ''.obs;
  RxBool supportsLocalAmounts = false.obs;
  RxBool supportsGeographicalRechargePlans = false.obs;
  RxString currencyCode = "".obs;
  Rxn<Operator> selectedOperatorData = Rxn<Operator>();
  RxString selectedAmount = "".obs;
  RxInt selectIndex = 0.obs;

  RxString userIosCode = ''.obs;
  RxDouble conversionAmount = 0.0.obs;
  RxDouble percentCharge = 0.0.obs;
  RxString totalPayable = ''.obs;
  RxString exchangeRate = ''.obs;
  RxString SelectedBundle = ''.obs;
  RxString iso2Code = "".obs;
  RxString selectedCountry = "Select Country".obs;




  RxList<GeographicalRechargePlan> geographicalRechargePlansList =
      <GeographicalRechargePlan>[].obs;
  Rxn<GeographicalRechargePlan> selectedGeographicalRecharge =
      Rxn<GeographicalRechargePlan>();



  final List<Operator> operatorsList = [];

  final _isLoading = false.obs;

  bool get isLoading => _isLoading.value;

  late GetOperatorModelInfo _getOperatorModelInfo;

  GetOperatorModelInfo get getOperatorModelInfo => _getOperatorModelInfo;



  Future<GetOperatorModelInfo> getOperatorProcess() async {
    _isLoading.value = true;
    update();
    await DataBundleApiService.getOperatorApi(iso2Code.value).then((value) {
      _getOperatorModelInfo = value!;
      saveInfo();

      update();
      _isLoading.value = false;
      update();
    }).catchError((onError) {
      log.e(onError);
      _isLoading.value = false;
    });
    _isLoading.value = false;
    update();
    return _getOperatorModelInfo;
  }

  RxList<FixedAmountModel> fixedAmountList = <FixedAmountModel>[].obs;
  RxList localFixedAmounts = [].obs;

  void saveInfo() {
    supportsGeographicalRechargePlans.value = getOperatorModelInfo
        .data.operators.first.supportsGeographicalRechargePlans;
    selectedOperatorData.value = _getOperatorModelInfo.data.operators.first;

    if (supportsGeographicalRechargePlans.value) {
      selectedGeographicalRecharge.value = _getOperatorModelInfo
          .data.operators.first.geographicalRechargePlans!.first;

      _getOperatorModelInfo.data.operators.first.geographicalRechargePlans!
          .forEach(
        (element) {
          geographicalRechargePlansList.add(
            GeographicalRechargePlan(
              locationCode: element.locationCode,
              locationName: element.locationName,
              fixedAmounts: element.fixedAmounts,
              localAmounts: element.localAmounts,
              fixedAmountsPlanNames: element.fixedAmountsPlanNames,
              fixedAmountsDescriptions: element.fixedAmountsDescriptions,
              localFixedAmountsPlanNames: element.localFixedAmountsPlanNames,
              localFixedAmountsDescriptions:
                  element.localFixedAmountsDescriptions,
            ),
          );
        },
      );

      _getOperatorModelInfo
          .data.operators.first.geographicalRechargePlans!.first.localAmounts!
          .forEach((element) {
        final descriptionKey = _getOperatorModelInfo
            .data
            .operators
            .first
            .geographicalRechargePlans!
            .first
            .localFixedAmountsPlanNames!
            .descriptions
            .keys
            .firstWhere(
          (key) => key == element.toStringAsFixed(2),
          orElse: () => '',
        );

        final description = _getOperatorModelInfo
                .data
                .operators
                .first
                .geographicalRechargePlans!
                .first
                .localFixedAmountsPlanNames!
                .descriptions[descriptionKey] ??
            '';
        currencyCode.value =
            _getOperatorModelInfo.data.operators.first.fx.currencyCode;

        fixedAmountList.add(
          FixedAmountModel(
              amount: element.toStringAsFixed(2),
              details:
                  '${element.toStringAsFixed(2)} ${currencyCode.value} - $description'),
        );
      });
    } else {
      _getOperatorModelInfo.data.operators.first.fixedAmounts.forEach(
        (element) {
          final descriptionKey = _getOperatorModelInfo
              .data.operators.first.fixedAmountsDescriptions!.descriptions.keys
              .firstWhere(
            (key) => key == element.toStringAsFixed(2),
            orElse: () => '',
          );

          final description = _getOperatorModelInfo.data.operators.first
                  .fixedAmountsDescriptions!.descriptions[descriptionKey] ??
              '';
          currencyCode.value =
              _getOperatorModelInfo.data.operators.first.fx.currencyCode;

          fixedAmountList.add(
            FixedAmountModel(
                amount: element.toStringAsFixed(2),
                details:
                    '${element.toStringAsFixed(2)} ${currencyCode.value} - $description'),
          );

          SelectedBundle.value = description;
        },
      );
    }

    operatorsList.clear();
    for (var all in _getOperatorModelInfo.data.operators) {
      operatorsList.add(Operator(
        id: all.id,
        fixedAmounts: all.fixedAmounts,
        fixedAmountsDescriptions: all.fixedAmountsDescriptions,
        fx: all.fx,
        geographicalRechargePlans: all.geographicalRechargePlans,
        name: all.name,
        operatorId: all.operatorId,
        localFixedAmounts: all.localFixedAmounts,
        supportsGeographicalRechargePlans:
            all.supportsGeographicalRechargePlans,
      ));
    }
    update();
  }

  final _isLoading2 = false.obs;

  bool get isLoading2 => _isLoading2.value;

  late GetChargesModel _getChargesModel;

  GetChargesModel get getChargesModel => _getChargesModel;

  Future<GetChargesModel> getChargesInfoProcess() async {
    _isLoading2.value = true;
    update();
    Map<String, dynamic> inputBody = {
      'operator': selectedOperatorData.value!.operatorId.toString(),
      'amount': selectedAmount.value,
      'cache_key': selectedOperatorData.value!.name,
      'geo_location': supportsGeographicalRechargePlans.value
          ? selectedGeographicalRecharge.value!.locationCode!
          : 'false'
    };

    await DataBundleApiService.getChargeApi(body: inputBody).then((value) {
      _getChargesModel = value!;

      totalPayable.value = _getChargesModel.data.charges.totalPayable;
      exchangeRate.value = _getChargesModel.data.charges.exchangeRate;

      getChargesModel.data.charges.amount;
      _isLoading2.value = false;
      Get.toNamed(Routes.bundlePreview);
      update();
    }).catchError((onError) {
      log.e(onError);
    });

    _isLoading2.value = false;
    update();
    return _getChargesModel;
  }

  final _isSubmitLoading = false.obs;
  late CommonSuccessModel _buyBundleModel;

  bool get isSubmitLoading => _isSubmitLoading.value;

  CommonSuccessModel get buyBundleModel => _buyBundleModel;

  Future<CommonSuccessModel> buyDataBundle() async {
    _isSubmitLoading.value = true;
    update();
    Map<String, dynamic> inputBody = {
      'operator_id': selectedOperatorData.value!.operatorId.toString(),
      'request_amount': _getChargesModel.data.charges.requestAmount,
      'cache_key':
          'DATA_BUNDLE_MOBILE_TOPUP_OPERATORS_${iso2Code.value}_BUNDLE',
      'phone': numberController.text,
      'geo_location': supportsGeographicalRechargePlans.value.toString(),
      'country_code' : iso2Code.value,
    };

    await DataBundleApiService.buyBundle(body: inputBody).then((value) {
      _buyBundleModel = value!;
      Get.offAll(CongratulationScreen(
          title: DynamicLanguage.key(Strings.congratulations),
          subTitle: DynamicLanguage.isLoading
              ? ""
              : DynamicLanguage.key(Strings.bundlePurchaseSuccessful),
          route: Routes.navigationScreen));

      _isSubmitLoading.value = false;
      update();
    }).catchError((onError) {
      log.e(onError);
    });

    _isSubmitLoading.value = false;
    update();
    return _buyBundleModel;
  }
}

class FixedAmountModel {
  final String amount, details;

  FixedAmountModel({required this.amount, required this.details});
}
