import 'dart:io'; // For File class
import 'package:my_app/models/LoginResponse.dart' as login_response;
import 'package:my_app/models/RegisterResponse.dart' as register_response;
import 'package:my_app/services/authService.dart';

class AuthRepository {
  final AuthService _authService = AuthService();

  Future<login_response.LoginResponse> login(String email, String password) async {
    return await _authService.login(email, password);
  }

  Future<register_response.RegisterResponse> register({
    required String name,
    required String email,
    required String password,
    required String gender,
    File? image, // Add image parameter
  }) async {
    return await _authService.register(
      name: name,
      email: email,
      password: password,
      gender: gender,
      image: image, // Pass the image to AuthService
    );
  }
}