import 'products.dart';
import 'Vendors.dart';

class ApiResponse<T> {
  final bool status;
  final T? data;
  final String? errorMessage;

  ApiResponse({
    required this.status,
    this.data,
    this.errorMessage,
  });

  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Map<String, dynamic> json) fromJsonT,
  ) {
    return ApiResponse<T>(
      status: json['status'] as bool,
      data: fromJsonT(json['data'] as Map<String, dynamic>),
    );
  }

  factory ApiResponse.error(String message) {
    return ApiResponse<T>(
      status: false,
      data: null,
      errorMessage: message,
    );
  }
}

class ApiErrorResponse {
  final int statusCode;
  final String message;

  ApiErrorResponse({
    required this.statusCode,
    required this.message,
  });

  factory ApiErrorResponse.fromJson(Map<String, dynamic> json) {
    return ApiErrorResponse(
      statusCode: json['status_code'] as int,
      message: json['message'] as String,
    );
  }
}

class FollowingData {
  final List<ProductList> products;
  final List<VendorList> vendor;

  FollowingData({
    required this.products,
    required this.vendor,
  });

  factory FollowingData.fromJson(Map<String, dynamic> json) {
    var productsJson = json['products'] as List? ?? [];
    var vendorJson = json['vendors'] as List? ?? [];

    return FollowingData(
      products: productsJson
          .map((item) => ProductList.fromJson(item as Map<String, dynamic>))
          .toList(),
      vendor: vendorJson
          .map((item) => VendorList.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }
}