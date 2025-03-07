import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:my_app/config/api.dart';
import 'package:my_app/models/Homedata.dart';

class HomeRepository {
  final String _baseUrl = ApiConfig.baseUrl;
  final String endpoint = '/home';

  Future<ApiResponse<HomeData>> fetchHomeData() async {
    final response = await http.get(Uri.parse(_baseUrl + endpoint));
    if (response.statusCode == 200) {
      final jsonMap = jsonDecode(response.body) as Map<String, dynamic>;
      return ApiResponse<HomeData>.fromJson(jsonMap, HomeData.fromJson);
    } else {
      throw Exception("Failed to load data");
    }
  }
}
