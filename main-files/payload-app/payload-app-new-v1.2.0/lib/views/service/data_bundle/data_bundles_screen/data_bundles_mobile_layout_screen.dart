import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/controller/data_bundles/data_bundles_screen_controller.dart';
import 'package:payloadui/views/utils/size.dart';
import 'package:payloadui/widgets/common/appbar/primary_appbar.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading4_widget.dart';
import '../../../../backend/model/data_bundle/data_bundle_info_model.dart';
import '../../../../backend/utils/custom_loading_api.dart';
import '../../../../controller/profile/update_profile_controller.dart';
import '../../../../languages/strings.dart';
import '../../../../widgets/bundle_card_widget.dart';
import '../../../../widgets/common/dropdown_field/country_dropdown.dart';
import '../../../../widgets/custom_dropdown.dart';
import '../../../utils/custom_color.dart';
import '../../../utils/custom_style.dart';
import '../../../utils/dimensions.dart';

class DataBundlesMobileLayoutScreen extends StatelessWidget {
  DataBundlesMobileLayoutScreen({super.key});

  final controller = Get.put(DataBundlesScreenController());
  final profileController = Get.put(UpdateProfileController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: PrimaryAppBar(
          appbarSize: Dimensions.appBarHeight * 0.6,
          DynamicLanguage.isLoading
              ? ""
              : DynamicLanguage.key(
                  Strings.myOffers,
                ),
          showBackButton: false,
          autoLeading: true),
      body: Obx(
        () => controller.isLoading ? CustomLoadingAPI() : _bodyWidget(),
      ),
    );
  }

  _bodyWidget() {
    return Obx(
      () => Padding(
        padding:
            EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
        child: Column(
          crossAxisAlignment: crossStart,
          children: [
            CountryDropDown(
              label: DynamicLanguage.key(
                Strings.selectCountry,
              ),
              selectMethod: controller.selectedCountry.value,
              itemsList: profileController.profileInfoModel.data.countries,
              onChanged: (value) {
                controller.operatorsList.clear();
                controller.selectedCountry.value = value!.name;
                controller.iso2Code.value = value.iso2;
                controller.getOperatorProcess();
              },
            ),
            if (controller.operatorsList.isNotEmpty) ...[
              Text(
                DynamicLanguage.isLoading
                    ? ""
                    : DynamicLanguage.key(Strings.selectOperator),
                style: CustomStyle.darkHeading4TextStyle.copyWith(
                  fontWeight: FontWeight.w600,
                  color: CustomColor.primaryDarkTextColor,
                ),
              ),
              verticalSpace(7),
              CustomDropdownMenu<Operator>(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(Dimensions.radius * 0.8),
                  color: CustomColor.greyColor.withOpacity(0.2),
                ),
                itemsList: controller.operatorsList,
                selectMethod:
                    controller.selectedOperatorData.value?.name.obs ?? ''.obs,
                onChanged: (value) {
                  if (value == null) return;
                  controller.selectedOperatorData.value = value;
                  controller.fixedAmountList.clear();
                  controller.geographicalRechargePlansList.clear();

                  final operatorData =
                      controller.getOperatorModelInfo.data.operators.firstWhere(
                    (operator) => operator.operatorId == value.operatorId,
                  );

                  controller.supportsGeographicalRechargePlans.value =
                      operatorData.supportsGeographicalRechargePlans;

                  if (controller.supportsGeographicalRechargePlans.value) {
                    operatorData.geographicalRechargePlans?.forEach((plan) {
                      controller.geographicalRechargePlansList.add(
                        GeographicalRechargePlan(
                          locationCode: plan.locationCode,
                          locationName: plan.locationName,
                          fixedAmounts: plan.fixedAmounts,
                          localAmounts: plan.localAmounts,
                          fixedAmountsPlanNames: plan.fixedAmountsPlanNames,
                          fixedAmountsDescriptions:
                              plan.fixedAmountsDescriptions,
                          localFixedAmountsPlanNames:
                              plan.localFixedAmountsPlanNames,
                          localFixedAmountsDescriptions:
                              plan.localFixedAmountsDescriptions,
                        ),
                      );
                    });
                  } else {
                    operatorData.fixedAmounts.forEach((amount) {
                      final descriptionKey = operatorData
                          .fixedAmountsDescriptions.descriptions.keys
                          .firstWhere(
                        (key) => key == amount.toStringAsFixed(2),
                        orElse: () => '',
                      );

                      final description = operatorData.fixedAmountsDescriptions
                              .descriptions[descriptionKey] ??
                          '';
                      controller.currencyCode.value =
                          operatorData.fx.currencyCode;

                      controller.fixedAmountList.add(
                        FixedAmountModel(
                          amount: amount.toStringAsFixed(2),
                          details:
                              '${amount.toStringAsFixed(2)} ${controller.currencyCode.value} - $description',
                        ),
                      );
                    });
                  }
                },
              ),
              verticalSpace(Dimensions.heightSize),
              if (controller.supportsGeographicalRechargePlans.value) ...[
                Text(
                  DynamicLanguage.isLoading
                      ? ""
                      : DynamicLanguage.key(Strings.SelectGeoLocation),
                  style: CustomStyle.darkHeading4TextStyle.copyWith(
                    fontWeight: FontWeight.w600,
                    color: CustomColor.primaryDarkTextColor,
                  ),
                ),
                verticalSpace(7),
                CustomDropdownMenu<GeographicalRechargePlan>(
                  decoration: BoxDecoration(
                    borderRadius:
                        BorderRadius.circular(Dimensions.radius * 0.8),
                    color: CustomColor.greyColor.withOpacity(0.2),
                  ),
                  itemsList: controller.geographicalRechargePlansList,
                  selectMethod: controller.selectedGeographicalRecharge.value
                          ?.locationName?.obs ??
                      ''.obs,
                  onChanged: (value) {
                    if (value == null) return;
                    controller.selectedGeographicalRecharge.value = value;

                    final operatorData = controller
                        .getOperatorModelInfo.data.operators
                        .firstWhere(
                      (operator) =>
                          operator.operatorId ==
                          controller.selectedOperatorData.value?.operatorId,
                    );

                    final geoPlan =
                        operatorData.geographicalRechargePlans?.firstWhere(
                      (plan) =>
                          plan.locationCode ==
                          controller
                              .selectedGeographicalRecharge.value?.locationCode,
                    );

                    if (geoPlan == null) return;

                    geoPlan.localAmounts?.forEach((amount) {
                      final descriptionKey = geoPlan
                          .localFixedAmountsPlanNames?.descriptions.keys
                          .firstWhere(
                        (key) => key == amount.toStringAsFixed(2),
                        orElse: () => '',
                      );

                      final description = geoPlan.localFixedAmountsPlanNames
                              ?.descriptions[descriptionKey] ??
                          '';
                      controller.currencyCode.value =
                          operatorData.fx.currencyCode;

                      controller.fixedAmountList.add(
                        FixedAmountModel(
                          amount: amount.toStringAsFixed(2),
                          details:
                              '${amount.toStringAsFixed(2)} ${controller.currencyCode.value} - $description',
                        ),
                      );
                    });
                  },
                ),
              ],

              TitleHeading4Widget(
                padding: EdgeInsets.only(
                  top: Dimensions.heightSize,
                  bottom: Dimensions.heightSize * 0.5,
                ),
                text: Strings.chooseOne,
              ),

              BundleCardWidget(),
            ] else ...[
              verticalSpace(Dimensions.heightSize),
              Center(
                  child: TitleHeading4Widget(
                text: Strings.OperatorNotFound,
              ))
            ]
          ],
        ),
      ),
    );
  }
}
