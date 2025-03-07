import 'products.dart';
import 'category.dart';

class ApiResponse<T> {
  final bool status;
  final T data;

  ApiResponse({
    required this.status,
    required this.data,
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
}

class HomeData {
  final List<MostDemandedProduct> mostDemanded;
  final List<Category> categories;

  HomeData({
    required this.mostDemanded,
    required this.categories,
  });

  factory HomeData.fromJson(Map<String, dynamic> json) {
    var mostDemandedJson = json['most_demanded'] as List? ?? [];
    var categoriesJson = json['categories'] as List? ?? [];
    return HomeData(
      mostDemanded: mostDemandedJson
          .map((item) => MostDemandedProduct.fromJson(item as Map<String, dynamic>))
          .toList(),
      categories: categoriesJson
          .map((item) => Category.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }
}
