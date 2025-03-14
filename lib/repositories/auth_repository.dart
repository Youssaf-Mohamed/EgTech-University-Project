import 'dart:io';
import 'package:my_app/responses/LoginResponse.dart' as login_response;
import 'package:my_app/responses/RegisterResponse.dart' as register_response;
import 'package:my_app/services/authService.dart';
import 'package:my_app/config/database_service.dart';
import 'package:shared_preferences/shared_preferences.dart';

class AuthRepository {
  final AuthService _authService = AuthService();
  final DatabaseService _databaseService = DatabaseService();

  /// **Login the user and store the token if "Remember Me" is checked**
  Future<login_response.LoginResponse> login(String email, String password, bool rememberMe) async {
    final login_response.LoginResponse response = await _authService.login(email, password);

    if (response.status) {
      // Store the token
      await _databaseService.saveToken(response.token);

      // Save the "Remember Me" preference
      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool('rememberMe', rememberMe);
    }
    return response;
  }

  /// **Register a new user**
  Future<register_response.RegisterResponse> register({
    required String name,
    required String email,
    required String password,
    required String gender,
    required String phone,
    required String address,
    File? image,
  }) async {
    return await _authService.register(
      name: name,
      email: email,
      password: password,
      gender: gender,
      image: image,
      address: address,
      phone: phone
    );
  }

  /// **Check if a user is logged in**
  Future<bool> isUserLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    bool rememberMe = prefs.getBool('rememberMe') ?? false;

    if (!rememberMe) {
      await deleteToken(); // Remove token if "Remember Me" is false
      return false;
    }

    String? token = await _databaseService.getToken();
    return token != null;
  }

  /// **Logout the user and remove the token**
  Future<void> logout() async {
    await deleteToken();
  }

  /// **Delete the token from storage**
  Future<void> deleteToken() async {
    await _databaseService.deleteToken();
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('rememberMe'); 
  }
}
