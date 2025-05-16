import 'dart:convert';

GetChargesModel getChargesModelFromJson(String str) => GetChargesModel.fromJson(json.decode(str));

String getChargesModelToJson(GetChargesModel data) => json.encode(data.toJson());

class GetChargesModel {
  Message message;
  Data data;
  String type;

  GetChargesModel({
    required this.message,
    required this.data,
    required this.type,
  });

  factory GetChargesModel.fromJson(Map<String, dynamic> json) => GetChargesModel(
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
  Charges charges;

  Data({
    required this.charges,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
    charges: Charges.fromJson(json["charges"]),
  );

  Map<String, dynamic> toJson() => {
    "charges": charges.toJson(),
  };
}

class Charges {
  String amount;
  String requestAmount;
  dynamic merchantCurrencyCode;
  double merchantCurrencyRate;
  String merchantExchangeRate;
  String walletCurrencyCode;
  int walletCurrencyRate;
  dynamic receiverCurrencyCode;
  String defaultCurrencyCode;
  String exchangeRate;
  String fixedCharge;
  String percentCharge;
  String fixedChargeCalc;
  String percentChargeCalc;
  String totalChargeCalc;
  String walletBalance;
  String chargeExchangeRate;
  String chargeCurrency;
  String exchangeAmount;
  String totalPayable;
  String minLimit;
  String maxLimit;
  String minLimitCalc;
  String maxLimitCalc;
  bool operatorHasLimit;
  String operatorMinLimit;
  String operatorMaxLimit;
  String operatorName;
  String bundleCurrency;

  Charges({
    required this.amount,
    required this.requestAmount,
    required this.merchantCurrencyCode,
    required this.merchantCurrencyRate,
    required this.merchantExchangeRate,
    required this.walletCurrencyCode,
    required this.walletCurrencyRate,
    required this.receiverCurrencyCode,
    required this.defaultCurrencyCode,
    required this.exchangeRate,
    required this.fixedCharge,
    required this.percentCharge,
    required this.fixedChargeCalc,
    required this.percentChargeCalc,
    required this.totalChargeCalc,
    required this.walletBalance,
    required this.chargeExchangeRate,
    required this.chargeCurrency,
    required this.exchangeAmount,
    required this.totalPayable,
    required this.minLimit,
    required this.maxLimit,
    required this.minLimitCalc,
    required this.maxLimitCalc,
    required this.operatorHasLimit,
    required this.operatorMinLimit,
    required this.operatorMaxLimit,
    required this.operatorName,
    required this.bundleCurrency,
  });

  factory Charges.fromJson(Map<String, dynamic> json) => Charges(
    amount: json["amount"],
    requestAmount: json["request_amount"],
    merchantCurrencyCode: json["merchant_currency_code"],
    merchantCurrencyRate: json["merchant_currency_rate"]?.toDouble(),
    merchantExchangeRate: json["merchant_exchange_rate"],
    walletCurrencyCode: json["wallet_currency_code"],
    walletCurrencyRate: json["wallet_currency_rate"],
    receiverCurrencyCode: json["receiver_currency_code"],
    defaultCurrencyCode: json["default_currency_code"],
    exchangeRate: json["exchange_rate"],
    fixedCharge: json["fixed_charge"],
    percentCharge: json["percent_charge"],
    fixedChargeCalc: json["fixed_charge_calc"],
    percentChargeCalc: json["percent_charge_calc"],
    totalChargeCalc: json["total_charge_calc"],
    walletBalance: json["wallet_balance"],
    chargeExchangeRate: json["charge_exchange_rate"],
    chargeCurrency: json["charge_currency"],
    exchangeAmount: json["exchange_amount"],
    totalPayable: json["total_payable"],
    minLimit: json["min_limit"],
    maxLimit: json["max_limit"],
    minLimitCalc: json["min_limit_calc"],
    maxLimitCalc: json["max_limit_calc"],
    operatorHasLimit: json["operator_has_limit"],
    operatorMinLimit: json["operator_min_limit"],
    operatorMaxLimit: json["operator_max_limit"],
    operatorName: json["operator_name"],
    bundleCurrency: json["bundle_currency"],
  );

  Map<String, dynamic> toJson() => {
    "amount": amount,
    "request_amount": requestAmount,
    "merchant_currency_code": merchantCurrencyCode,
    "merchant_currency_rate": merchantCurrencyRate,
    "merchant_exchange_rate": merchantExchangeRate,
    "wallet_currency_code": walletCurrencyCode,
    "wallet_currency_rate": walletCurrencyRate,
    "receiver_currency_code": receiverCurrencyCode,
    "default_currency_code": defaultCurrencyCode,
    "exchange_rate": exchangeRate,
    "fixed_charge": fixedCharge,
    "percent_charge": percentCharge,
    "fixed_charge_calc": fixedChargeCalc,
    "percent_charge_calc": percentChargeCalc,
    "total_charge_calc": totalChargeCalc,
    "wallet_balance": walletBalance,
    "charge_exchange_rate": chargeExchangeRate,
    "charge_currency": chargeCurrency,
    "exchange_amount": exchangeAmount,
    "total_payable": totalPayable,
    "min_limit": minLimit,
    "max_limit": maxLimit,
    "min_limit_calc": minLimitCalc,
    "max_limit_calc": maxLimitCalc,
    "operator_has_limit": operatorHasLimit,
    "operator_min_limit": operatorMinLimit,
    "operator_max_limit": operatorMaxLimit,
    "operator_name": operatorName,
    "bundle_currency": bundleCurrency,
  };
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

class Fx {
  int rate;
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
