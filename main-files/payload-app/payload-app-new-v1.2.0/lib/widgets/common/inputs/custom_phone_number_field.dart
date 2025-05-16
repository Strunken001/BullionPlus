import 'package:dynamic_languages/dynamic_languages.dart';
import 'package:flutter/material.dart';
import 'package:payloadui/views/utils/custom_color.dart';
import 'package:payloadui/views/utils/dimensions.dart';
import 'package:payloadui/widgets/common/text_labels/title_heading4_widget.dart';
import 'primary_input_filed.dart';

class CustomInputField extends StatelessWidget {
  final TextEditingController controller;
  final String label;
  final String hint;
  final String phoneCodeText;
  final bool isPassword;
  final bool isValidator;
  final bool? isObscure;
  final TextInputType? keyboardType; // Optional keyboardType
  final Function(dynamic)? onChanged; // Optional onChanged

  const CustomInputField({
    super.key,
    required this.controller,
    required this.label,
    required this.hint,
    this.isPassword = false,
    this.isValidator = true,
    this.isObscure,
    required this.phoneCodeText,
    this.keyboardType, // Initialize keyboardType
    this.onChanged, // Initialize onChanged
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding:
          EdgeInsets.symmetric(vertical: Dimensions.marginSizeVertical * 0.4),
      child: PrimaryInputWidget(
        onChanged: onChanged,
        keyboardType: TextInputType.number,
        prefixIcon: Container(
          margin: EdgeInsets.only(right: Dimensions.widthSize * 0.5),
          padding: EdgeInsets.only(
              top: Dimensions.heightSize * 0.8, left: 5, right: 5),
          decoration: BoxDecoration(
            color: Colors.blue,
            borderRadius: DynamicLanguage.languageDirection == TextDirection.rtl
                ? const BorderRadius.only(
                    topRight: Radius.circular(10),
                    bottomRight: Radius.circular(10),
                  )
                : const BorderRadius.only(
                    topLeft: Radius.circular(10),
                    bottomLeft: Radius.circular(10),
                  ),
          ),
          child: TitleHeading4Widget(
            textAlign: TextAlign.center,
            text: '+$phoneCodeText',
            color: CustomColor.whiteColor,
          ),
        ),
        isObscure: isObscure ?? false,
        isValidator: isValidator,
        controller: controller,
        hint: DynamicLanguage.key(hint),
        label: label,
      ),
    );
  }
}
