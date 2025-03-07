// ignore_for_file: unnecessary_cast

import 'products.dart';

class Category {
  final int id;
  final String categoryName;
  final String description;
  final String categoryImage;
  final List<ProductList> products;

  Category({
    this.id = 0,
    this.categoryName = '',
    this.description = '',
    this.categoryImage = '',
    this.products = const <ProductList>[],
  });

  factory Category.fromJson(Map<String, dynamic> json) {
    var productsJson = json['products'] as List? ?? [];
    return Category(
      id: (json['category_id'] ?? json['id']) as int,
      categoryName: json['category_name'].toString() as String,
      description: json['description'].toString() as String,
      categoryImage: json['category_image'].toString() as String,
      products: productsJson
          .map((item) => ProductList.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }
}
