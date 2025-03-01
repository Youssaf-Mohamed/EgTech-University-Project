import 'dart:io';
import 'package:my_app/models/LoginResponse.dart' as login_response;
import 'package:my_app/models/RegisterResponse.dart' as register_response;
import 'package:my_app/services/authService.dart';
import 'package:my_app/config/database_service.dart';

class AuthRepository {
  final AuthService _authService = AuthService();
  final DatabaseService _databaseService = DatabaseService();

  Future<login_response.LoginResponse> login(String email, String password) async {
    return await _authService.login(email, password);
  }

  Future<register_response.RegisterResponse> register({
    required String name,
    required String email,
    required String password,
    required String gender,
    File? image,
  }) async {
    return await _authService.register(
      name: name,
      email: email,
      password: password,
      gender: gender,
      image: image,
    );
  }

  /// **Check if a user is logged in by verifying the stored token**
  Future<bool> isUserLoggedIn() async {
    String? token = await _databaseService.getToken();
    return token != null;
  }

  /// **Logout the user by deleting the token**
  Future<void> logout() async {
    await _databaseService.deleteToken();
  }
}
