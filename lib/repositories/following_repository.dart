import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:my_app/config/api.dart';
import 'package:my_app/config/database_service.dart';
import 'package:my_app/models/FollowingData.dart';

class FollowingRepository {
  final String _baseUrl = ApiConfig.baseUrl;
  final String endpoint = '/following';

  Future<ApiResponse<FollowingData>> fetchFollowingData() async {
    final token = await DatabaseService().getToken();
    print(token);
    final response = await http.get(
      Uri.parse(_baseUrl + endpoint),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );
    if (response.statusCode == 200) {
      final jsonMap = jsonDecode(response.body) as Map<String, dynamic>;
      return ApiResponse<FollowingData>.fromJson(
          jsonMap, FollowingData.fromJson);
    } else if (response.statusCode == 403 || response.statusCode == 405) {
      return ApiResponse<FollowingData>.error(
          "Access denied (Error ${response.statusCode})");
    } else {
      throw Exception("Failed to load data (Status: ${response.statusCode})");
    }
  }
}
