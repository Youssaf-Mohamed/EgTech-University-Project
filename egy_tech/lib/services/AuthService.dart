import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'package:my_app/config/api.dart';
import 'package:my_app/models/LoginResponse.dart';
import 'package:my_app/models/RegisterResponse.dart';
import 'package:my_app/config/database_service.dart';
import 'package:my_app/objects/User.dart';

class AuthService {
  final String _baseUrl = ApiConfig.baseUrl;
  final DatabaseService _databaseService = DatabaseService();

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
        final loginResponse = LoginResponse.fromJson(jsonDecode(response.body));
        await _databaseService.saveToken(loginResponse.token); // Save token
        return loginResponse;
      } else {
        throw Exception('Login failed: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to connect to the server: $e');
    }
  }

  Future<RegisterResponse> register({
    required String name,
    required String email,
    required String password,
    required String gender,
    File? image,
  }) async {
    try {
      var url = Uri.parse('$_baseUrl/register');
      var request = http.MultipartRequest('POST', url);
      request.fields['name'] = name;
      request.fields['email'] = email;
      request.fields['password'] = password;
      request.fields['gender'] = gender;
      if (image != null) {
        request.files.add(
          await http.MultipartFile.fromPath(
            'profile_picture',
            image.path,
            contentType: MediaType('image', 'jpeg'),
          ),
        );
      }
      var response = await request.send();
      var responseData = await response.stream.bytesToString();
      if (response.statusCode == 200 || response.statusCode == 201) {
        final registerResponse =
            RegisterResponse.fromJson(jsonDecode(responseData));
        await _databaseService.saveToken(registerResponse.token); // Save token
        return registerResponse;
      } else {
        throw Exception(
            'Registration failed: ${response.statusCode} - $responseData');
      }
    } catch (e) {
      throw Exception('Failed to connect to the server: $e');
    }
  }

  Future<User> fetchUser() async {
    try {
      // Retrieve the token from the database
      final token = await _databaseService.getToken();
      if (token == null) {
        throw Exception('No token found');
      }

      // Make the API request to fetch user data
      var url = Uri.parse('$_baseUrl/user');
      var response = await http.get(
        url,
        headers: {
          'Authorization': 'Bearer $token',
        },
      );
      if (response.statusCode == 200 || response.statusCode == 201) {
        final userData = jsonDecode(response.body);
        return User.fromJson(userData['data']);
      } else {
        throw Exception('Failed to fetch user data: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to connect to the server: $e');
    }
  }
}
