import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../../../views/utils/dimensions.dart';

class BackButtonWidget extends StatelessWidget {
  const BackButtonWidget({super.key, required this.onTap});

  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      hoverColor: Colors.transparent,
      splashColor: Colors.transparent,
      highlightColor: Colors.transparent,
      onTap: onTap,
      child: const Icon(Icons.arrow_back).paddingSymmetric(
        horizontal: Dimensions.marginSizeHorizontal * 0.5,
      ),
    );
  }
}
