import 'package:my_app/models/LoginResponse.dart' as login_response;

import 'package:my_app/services/authService.dart';
// import '../models/loginResponse.dart' as login_response_2;

class AuthRepository {
  final AuthService _authService = AuthService();

  Future<login_response.LoginResponse> login(String email, String password) async {
    return await _authService.login(email, password);
  }
}
