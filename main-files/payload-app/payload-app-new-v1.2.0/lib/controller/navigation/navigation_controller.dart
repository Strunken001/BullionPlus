import 'package:flutter/cupertino.dart';
import 'package:get/get.dart';
import 'package:payloadui/views/dashboard_screen/dashboard_screen.dart';
import '../../views/profile/profile_screen/profile_screen.dart';
import '../../views/recharge/recharge_screen.dart';
import '../../views/service/services_screen/services_screen.dart';

class BottomNavBarController extends GetxController {
  RxInt currentIndex = 0.obs;
  final List<Widget> screens = [
    const DashboardScreen(),
    const RechargeScreen(),
    const ServicesScreen(),
    const ProfileScreen()
  ];

  void changeIndex(int index) {
    currentIndex.value = index;
  }

  void backToHome() {
    changeIndex(0);
  }

  void backToRecharge() {
    changeIndex(1);
  }

  void backToServices() {
    changeIndex(3);
  }

  void backToProfile() {
    changeIndex(4);
  }
}
