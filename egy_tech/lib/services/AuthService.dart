import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:my_app/config/api.dart'; // For base URL
import 'package:my_app/models/LoginResponse.dart'; // For LoginResponse model

class AuthService {
  final String _baseUrl = ApiConfig.baseUrl;

  Future<LoginResponse> login(String email, String password) async {
    try {
      var url = Uri.parse('$_baseUrl/login');
      var response = await http.post(
        url,
        headers: {"Content-Type": "application/json"},
        body: jsonEncode({
          'email': email,
          'password': password,
        }),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        return LoginResponse.fromJson(jsonDecode(response.body));
      } else {
        throw Exception('Login failed: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to connect to the server: $e');
    }
  }
}