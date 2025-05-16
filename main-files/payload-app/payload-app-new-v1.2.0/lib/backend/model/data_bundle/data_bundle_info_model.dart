import 'dart:convert';

import '../../../widgets/custom_dropdown.dart';

GetOperatorModelInfo getOperatorModelInfoFromJson(String str) =>
    GetOperatorModelInfo.fromJson(json.decode(str));

String getOperatorModelInfoToJson(GetOperatorModelInfo data) =>
    json.encode(data.toJson());

class GetOperatorModelInfo {
  Message message;
  Data data;
  String type;

  GetOperatorModelInfo({
    required this.message,
    required this.data,
    required this.type,
  });

  factory GetOperatorModelInfo.fromJson(Map<String, dynamic> json) =>
      GetOperatorModelInfo(
        message: Message.fromJson(json["message"]),
        data: Data.fromJson(json["data"]),
        type: json["type"],
      );

  Map<String, dynamic> toJson() => {
    "message": message.toJson(),
    "data": data.toJson(),
    "type": type,
  };
}

class Data {
  List<Operator> operators;
  String cacheKey;

  Data({
    required this.operators,
    required this.cacheKey,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
    operators: List<Operator>.from(
        json["operators"].map((x) => Operator.fromJson(x))),
    cacheKey: json["cache_key"],
  );

  Map<String, dynamic> toJson() => {
    "operators": List<dynamic>.from(operators.map((x) => x.toJson())),
    "cache_key": cacheKey,
  };
}

class Operator implements DropdownMenuModel {
  int id;
  int operatorId;
  String name;
  Fx fx;
  List<double> fixedAmounts;
  List<dynamic> localFixedAmounts;
  dynamic fixedAmountsDescriptions;
  bool supportsGeographicalRechargePlans;
  List<GeographicalRechargePlan>? geographicalRechargePlans;
  Operator({
    required this.id,
    required this.operatorId,
    required this.name,
    required this.fx,
    required this.fixedAmounts,
    this.fixedAmountsDescriptions,
    required this.localFixedAmounts,
    required this.supportsGeographicalRechargePlans,
    this.geographicalRechargePlans,
  });

  factory Operator.fromJson(Map<String, dynamic> json) => Operator(
    id: json["id"],
    operatorId: json["operatorId"],
    name: json["name"],
    fx: Fx.fromJson(json["fx"]),
    supportsGeographicalRechargePlans:
    json["supportsGeographicalRechargePlans"],
    fixedAmounts:
    List<double>.from(json["fixedAmounts"].map((x) => x?.toDouble())),
    fixedAmountsDescriptions:
    json["supportsGeographicalRechargePlans"] == true
        ? []
        : FixedAmountsDescriptions.fromJson(
        json["fixedAmountsDescriptions"]),
    localFixedAmounts:
    List<dynamic>.from(json["localFixedAmounts"].map((x) => x)),
    geographicalRechargePlans: json["geographicalRechargePlans"] == null
        ? []
        : List<GeographicalRechargePlan>.from(
        json["geographicalRechargePlans"]!
            .map((x) => GeographicalRechargePlan.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "id": id,
    "operatorId": operatorId,
    "name": name,
    "fx": fx.toJson(),
    "fixedAmounts": List<dynamic>.from(fixedAmounts.map((x) => x)),
    "fixedAmountsDescriptions": fixedAmountsDescriptions.toJson(),
    "geographicalRechargePlans": geographicalRechargePlans == null
        ? []
        : List<dynamic>.from(
        geographicalRechargePlans!.map((x) => x.toJson())),
    "localFixedAmounts":
    List<dynamic>.from(localFixedAmounts.map((x) => x)),
    "supportsGeographicalRechargePlans": supportsGeographicalRechargePlans,
  };

  @override
  String get title => name;
}

class Country {
  String isoName;
  String name;

  Country({
    required this.isoName,
    required this.name,
  });

  factory Country.fromJson(Map<String, dynamic> json) => Country(
    isoName: json["isoName"],
    name: json["name"],
  );

  Map<String, dynamic> toJson() => {
    "isoName": isoName,
    "name": name,
  };
}

class Fees {
  int international;
  int local;
  int localPercentage;
  int internationalPercentage;

  Fees({
    required this.international,
    required this.local,
    required this.localPercentage,
    required this.internationalPercentage,
  });

  factory Fees.fromJson(Map<String, dynamic> json) => Fees(
    international: json["international"],
    local: json["local"],
    localPercentage: json["localPercentage"],
    internationalPercentage: json["internationalPercentage"],
  );

  Map<String, dynamic> toJson() => {
    "international": international,
    "local": local,
    "localPercentage": localPercentage,
    "internationalPercentage": internationalPercentage,
  };
}

class FixedAmountsDescriptions {
  Map<String, String?> descriptions;

  FixedAmountsDescriptions({
    required this.descriptions,
  });

  factory FixedAmountsDescriptions.fromJson(Map<String, dynamic> json) {
    return FixedAmountsDescriptions(
      descriptions: json.map((key, value) => MapEntry(key, value as String?)),
    );
  }

  Map<String, dynamic> toJson() => descriptions;
}

class Fx {
  dynamic rate;
  String currencyCode;

  Fx({
    required this.rate,
    required this.currencyCode,
  });

  factory Fx.fromJson(Map<String, dynamic> json) => Fx(
    rate: json["rate"],
    currencyCode: json["currencyCode"],
  );

  Map<String, dynamic> toJson() => {
    "rate": rate,
    "currencyCode": currencyCode,
  };
}

class Message {
  List<String> success;

  Message({
    required this.success,
  });

  factory Message.fromJson(Map<String, dynamic> json) => Message(
    success: List<String>.from(json["success"].map((x) => x)),
  );

  Map<String, dynamic> toJson() => {
    "success": List<dynamic>.from(success.map((x) => x)),
  };
}

class GeographicalRechargePlan implements DropdownMenuModel {
  String? locationCode;
  String? locationName;
  List<double>? fixedAmounts;
  List<int>? localAmounts;
  List<dynamic>? fixedAmountsPlanNames;
  FixedAmountsDescriptions? fixedAmountsDescriptions;
  LocalFixedAmounts? localFixedAmountsPlanNames;
  LocalFixedAmounts? localFixedAmountsDescriptions;

  GeographicalRechargePlan({
    this.locationCode,
    this.locationName,
    this.fixedAmounts,
    this.localAmounts,
    this.fixedAmountsPlanNames,
    this.fixedAmountsDescriptions,
    this.localFixedAmountsPlanNames,
    this.localFixedAmountsDescriptions,
  });

  factory GeographicalRechargePlan.fromJson(Map<String, dynamic> json) =>
      GeographicalRechargePlan(
        locationCode: json["locationCode"],
        locationName: json["locationName"],
        fixedAmounts: json["fixedAmounts"] == null
            ? []
            : List<double>.from(
            json["fixedAmounts"]!.map((x) => x?.toDouble())),
        localAmounts: json["localAmounts"] == null
            ? []
            : List<int>.from(json["localAmounts"]!.map((x) => x)),
        fixedAmountsPlanNames: json["fixedAmountsPlanNames"] == null
            ? []
            : List<dynamic>.from(json["fixedAmountsPlanNames"]!.map((x) => x)),
        fixedAmountsDescriptions: json["fixedAmountsDescriptions"] == null
            ? null
            : FixedAmountsDescriptions.fromJson(
            json["fixedAmountsDescriptions"]),
        localFixedAmountsPlanNames: json["localFixedAmountsPlanNames"] == null
            ? null
            : LocalFixedAmounts.fromJson(json["localFixedAmountsPlanNames"]),
        localFixedAmountsDescriptions: json["localFixedAmountsDescriptions"] ==
            null
            ? null
            : LocalFixedAmounts.fromJson(json["localFixedAmountsDescriptions"]),
      );

  Map<String, dynamic> toJson() => {
    "locationCode": locationCode,
    "locationName": locationName,
    "fixedAmounts": fixedAmounts == null
        ? []
        : List<dynamic>.from(fixedAmounts!.map((x) => x)),
    "localAmounts": localAmounts == null
        ? []
        : List<dynamic>.from(localAmounts!.map((x) => x)),
    "fixedAmountsPlanNames": fixedAmountsPlanNames == null
        ? []
        : List<dynamic>.from(fixedAmountsPlanNames!.map((x) => x)),
    "fixedAmountsDescriptions": fixedAmountsDescriptions?.toJson(),
    "localFixedAmountsPlanNames": localFixedAmountsPlanNames?.toJson(),
    "localFixedAmountsDescriptions":
    localFixedAmountsDescriptions?.toJson(),
  };

  @override
  String get title => locationName ?? '';
}

class LocalFixedAmounts {
  Map<String, String?> descriptions;

  LocalFixedAmounts({
    required this.descriptions,
  });

  factory LocalFixedAmounts.fromJson(Map<String, dynamic> json) {
    return LocalFixedAmounts(
      descriptions: json.map((key, value) => MapEntry(key, value as String?)),
    );
  }

  Map<String, dynamic> toJson() => descriptions;
}