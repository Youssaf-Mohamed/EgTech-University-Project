import 'package:my_app/models/User.dart';

class RegisterResponse {
  final bool status;
  final User user;
  final String token;

  RegisterResponse({
    required this.status,
    required this.user,
    required this.token,
  });

  factory RegisterResponse.fromJson(Map<String, dynamic> json) {
    return RegisterResponse(
      status: json['status'],
      user: User.fromJson(json['data']['user']),
      token: json['data']['token'],
    );
  }
}