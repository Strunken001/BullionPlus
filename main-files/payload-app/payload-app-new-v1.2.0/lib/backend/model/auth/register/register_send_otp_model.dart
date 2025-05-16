class RegisterSendOtpModel {
  int userId;
  String code;
  String token;
  DateTime createdAt;

  RegisterSendOtpModel({
    required this.userId,
    required this.code,
    required this.token,
    required this.createdAt,
  });

  factory RegisterSendOtpModel.fromJson(Map<String, dynamic> json) =>
      RegisterSendOtpModel(
        userId: json["user_id"],
        code: json["code"],
        token: json["token"],
        createdAt: DateTime.parse(json["created_at"]),
      );

  Map<String, dynamic> toJson() => {
        "user_id": userId,
        "code": code,
        "token": token,
        "created_at": createdAt.toIso8601String(),
      };
}
