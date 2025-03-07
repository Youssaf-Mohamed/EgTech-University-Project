import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:my_app/config/api.dart';
import 'package:my_app/config/database_service.dart';
// import 'package:my_app/models/LoginResponse.dart';
import 'package:my_app/models/User.dart';

class ApiService {
  final String _baseUrl = ApiConfig.baseUrl;
final DatabaseService _databaseService = DatabaseService();

  
  // Helper function to create a Uri
  Uri _buildUri(String endpoint) {
    return Uri.parse('$_baseUrl$endpoint');
  }

  Future<dynamic> getRequest(String endpoint) async {
    try {
      var url = _buildUri(endpoint);
      var response = await http.get(url);

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        throw Exception('Failed to load data: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Failed to connect to the server: $e');
    }
  }


// Future<LoginResponse> postRequest(String endpoint, Map<String, dynamic> data) async {
//   try {
//     var url = _buildUri(endpoint);
//     var response = await http.post(
//       url,
//       headers: {"Content-Type": "application/json"},
//       body: jsonEncode(data),
//     );

//     if (response.statusCode == 200 || response.statusCode == 201) {
//       var responseData = jsonDecode(response.body);
//       return LoginResponse.fromJson(responseData);
//     } else {
//       throw Exception('Request failed with status: ${response.statusCode}');
//     }
//   } catch (e) {
//     throw Exception('Failed to connect to the server: $e');
//   }
// }


Future<User> fetchUserProfile() async {
    final token = await _databaseService.getToken();
    if (token == null) {
      throw Exception('No token found');
    }
    var url = Uri.parse('$_baseUrl/user/profile');
    var response = await http.get(
      url,
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $token", // Include token in headers
      },
    );
    if (response.statusCode == 200) {
      return User.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to load user profile: ${response.statusCode}');
    }
  }
}