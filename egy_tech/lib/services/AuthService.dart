import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'package:my_app/config/api.dart';
import 'package:my_app/responses/LoginResponse.dart';
import 'package:my_app/responses/RegisterResponse.dart';
import 'package:my_app/config/database_service.dart';
import 'package:my_app/models/User.dart';

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
        print(loginResponse.token);
        await _databaseService.deleteToken(); // Delete existing token
        await _databaseService.saveToken(loginResponse.token); // Save token
        return loginResponse;
      } else if (response.statusCode == 405) {
        // Handle 405: Invalid token or permissions issue
        await _databaseService.deleteToken(); // Delete invalid token
        throw Exception('Token is invalid or expired, please log in again.');
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
    required String phone,
    required String address,

    File? image,
  }) async {
    try {
      var url = Uri.parse('$_baseUrl/register');
      var request = http.MultipartRequest('POST', url);
      request.fields['name'] = name;
      request.fields['email'] = email;
      request.fields['password'] = password;
      request.fields['gender'] = gender;
      request.fields['phone'] = phone;
      request.fields['address'] = address;

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
        await _databaseService.saveToken(registerResponse.token);
        return registerResponse;
      } else if (response.statusCode == 405) {
        await _databaseService.deleteToken(); // Delete invalid token
        throw Exception('Token is invalid or expired, please log in again.');
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
      } else if (response.statusCode == 405) {
        await _databaseService.deleteToken();
        throw Exception('Token is invalid or expired, please log in again.');
      } else {
        throw Exception('Failed to fetch user data: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to connect to the server: $e');
    }
  }
}
