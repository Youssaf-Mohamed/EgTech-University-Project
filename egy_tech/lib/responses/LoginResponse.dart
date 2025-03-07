import 'package:my_app/models/User.dart';

class LoginResponse {
  final bool status;
  final User user;
  final String token;

  LoginResponse({
    required this.status,
    required this.user,
    required this.token,
  });

  factory LoginResponse.fromJson(Map<String, dynamic> json) {
    return LoginResponse(
      status: json['status'],
      user: User.fromJson(json['data']['user']),
      token: json['data']['token'],
    );
  }
}