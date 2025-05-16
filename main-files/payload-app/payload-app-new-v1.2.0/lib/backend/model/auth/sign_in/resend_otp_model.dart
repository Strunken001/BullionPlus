class ResendLoginOtpModel {
  Message message;
  Data data;
  String type;

  ResendLoginOtpModel({
    required this.message,
    required this.data,
    required this.type,
  });

  factory ResendLoginOtpModel.fromJson(Map<String, dynamic> json) =>
      ResendLoginOtpModel(
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
  String userId;
  String waitTime;

  Data({
    required this.userId,
    required this.waitTime,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        userId: json["user_id"],
        waitTime: json["wait_time"],
      );

  Map<String, dynamic> toJson() => {
        "user_id": userId,
        "wait_time": waitTime,
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
