import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:payloadui/backend/utils/custom_loading_api.dart';
import 'package:payloadui/controller/profile/update_profile_controller.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/appbar/primary_appbar.dart';
import '../../../languages/strings.dart';
import '../../../widgets/common/buttons/primary_button.dart';
import '../../../widgets/common/image_picker/image_picker_widget.dart';
import '../../../widgets/common/inputs/custom_form_widget.dart';
import '../../../widgets/common/dropdown_field/country_dropdown.dart';
import '../../../widgets/common/text_labels/title_heading2_widget.dart';
import '../../../widgets/common/text_labels/title_heading4_widget.dart';
import '../../utils/custom_color.dart';

class UpdateProfileMobileScreenLayout extends StatelessWidget {
  UpdateProfileMobileScreenLayout({super.key});

  final controller = Get.put(UpdateProfileController());

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _appBarWidget(context),
      body: Obx(
        () => controller.isLoading
            ? const CustomLoadingAPI()
            : _bodyWidget(context),
      ),
    );
  }

  _appBarWidget(BuildContext context) {
    return PrimaryAppBar(
      action: [
        Container(
          margin: EdgeInsets.only(
              right: Dimensions.marginSizeHorizontal,
              top: Dimensions.heightSize * 0.5,
              left: Dimensions.marginSizeHorizontal,
              bottom: Dimensions.heightSize * 0.5),
          decoration: BoxDecoration(
              border: Border.all(color: CustomColor.redColor),
              borderRadius: BorderRadius.circular(Dimensions.radius * 0.8)),
          child: TextButton(
              onPressed: () {
                showDialog(
                    context: context,
                    builder: (BuildContext context) {
                      return Obx(
                        () => controller.isDeleteLoading
                            ? const CustomLoadingAPI()
                            : _alertDialogBox(),
                      );
                    });
              },
              child: const TitleHeading4Widget(
                text: Strings.delete,
                color: CustomColor.redColor,
              )),
        )
      ],
      Strings.updateProfile,
      showBackButton: false,
      autoLeading: true,
      appbarSize: Dimensions.heightSize * 4,
    );
  }

  _alertDialogBox() {
    return AlertDialog(
      backgroundColor: CustomColor.whiteColor,
      title: const TitleHeading2Widget(text: Strings.confirmDeletion),
      content: const TitleHeading4Widget(
          text: Strings.areYouSureYouWantToDeleteYourProfile),
      actions: [
        TextButton(
            onPressed: () {
              Get.back();
            },
            child: const TitleHeading4Widget(
              text: Strings.cancel,
              color: CustomColor.primaryDarkTextColor,
            )),
        TextButton(
            onPressed: () {
              controller.deleteProfileProcess();
            },
            child: const TitleHeading4Widget(
              text: Strings.delete,
            )),
      ],
    );
  }

  _bodyWidget(BuildContext context) {
    return Padding(
        padding:
            EdgeInsets.symmetric(horizontal: Dimensions.marginSizeHorizontal),
        child: SingleChildScrollView(
          child: Column(
            children: [
              ImagePickerWidget(),
              _inputFormWidget(context),
              _buttonWidget(context),
            ],
          ),
        ));
  }

  _inputFormWidget(BuildContext context) {
    return Form(
      key: controller.formKey,
      child: Column(
        children: [
          CustomFormWidget(
              hint: Strings.firstName,
              controller: controller.firstNameController,
              label: Strings.firstName),
          CustomFormWidget(
              hint: Strings.lastName,
              controller: controller.lastNameController,
              label: Strings.lastName),
          CustomFormWidget(
              hint: Strings.phoneNumber,
              controller: controller.numberController,
              keyboardType: TextInputType.number,
              label: Strings.phoneNumber),
          CustomFormWidget(
              hint: Strings.emailAddress,
              controller: controller.emailController,
              label: Strings.emailAddress),
          CountryDropDown(
            label: DynamicLanguage.key(
              Strings.selectCountry,
            ),
            selectMethod: controller.selectedCountry.value,
            itemsList: controller.profileInfoModel.data.countries,
            onChanged: (value) {
              controller.countryName.value = value!.name;
              controller.mobileCode.value = value.mobileCode;
              controller.iso2Code.value = value.iso2;
            },
          ),
        ],
      ),
    );
  }

  _buttonWidget(BuildContext context) {
    return Container(
      margin: EdgeInsets.only(
        top: Dimensions.marginSizeVertical,
        bottom: Dimensions.marginSizeVertical,
      ),
      child: Obx(
        () => controller.isUpdateLoading
            ? const CustomLoadingAPI()
            : PrimaryButton(
                title: Strings.updateProfile.tr,
                onPressed: () {
                  if (controller.formKey.currentState!.validate()) {
                    if (controller.imageController.isImagePathSet.value) {
                      controller.profileUpdateWithImageProcess();
                    } else {
                      controller.profileUpdateWithOutImageProcess();
                    }
                  }
                }),
      ),
    );
  }
}
