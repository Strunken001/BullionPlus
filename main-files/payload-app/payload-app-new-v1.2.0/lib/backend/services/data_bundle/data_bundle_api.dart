import 'package:payloadui/backend/model/common/common_success_model.dart';
import 'package:payloadui/backend/model/data_bundle/data_bundle_info_model.dart';
import 'package:payloadui/backend/model/data_bundle/get_charges_model.dart';

import '../../utils/api_method.dart';
import '../../utils/custom_snackbar.dart';
import '../api_endpoint.dart';

class DataBundleApiService {
  static Future<GetOperatorModelInfo?> getOperatorApi(String ios2Code) async {
    Map<String, dynamic>? mapResponse;
    try {
      mapResponse = await ApiMethod(isBasic: false).get(
        '${ApiEndpoint.getOperatorInfo}$ios2Code',
        code: 200,
      );
      if (mapResponse != null) {
        GetOperatorModelInfo result =
            GetOperatorModelInfo.fromJson(mapResponse);

        return result;
      }
    } catch (e) {
      log.e('🐞🐞🐞 err from get operator process api service ==> $e 🐞🐞🐞');
      CustomSnackBar.error('Something went Wrong! in Get operator model');
      return null;
    }
    return null;
  }

  //

  static Future<GetChargesModel?> getChargeApi(
      {required Map<String, dynamic> body}) async {
    Map<String, dynamic>? mapResponse;
    try {
      mapResponse = await ApiMethod(isBasic: false).post(
        ApiEndpoint.getChargesInfo,
        body,
        code: 200,
        showResult: true,
      );
      if (mapResponse != null) {
        GetChargesModel otpResult = GetChargesModel.fromJson(mapResponse);
        // CustomSnackBar.success(otpResult.message.success.first.toString());
        return otpResult;
      }
    } catch (e) {
      log.e('🐞🐞🐞 err from Get Charges in api service ==> $e 🐞🐞🐞');
      CustomSnackBar.error('Something went Wrong! in Get Charges model');
      return null;
    }
    return null;
  }

  //=> BUY DATA BUNDLE

  static Future<CommonSuccessModel?> buyBundle(
      {required Map<String, dynamic> body}) async {
    Map<String, dynamic>? mapResponse;
    try {
      mapResponse = await ApiMethod(isBasic: false).post(
        ApiEndpoint.buyBundle,
        body,
        code: 200,
      );
      if (mapResponse != null) {
        CommonSuccessModel result = CommonSuccessModel.fromJson(mapResponse);

        return result;
      }
    } catch (e) {
      log.e('🐞🐞🐞 err from Buy bundle process api service ==> $e 🐞🐞🐞');
      CustomSnackBar.error(
          'Something went Wrong! in Reset password process api');
      return null;
    }
    return null;
  }
}
