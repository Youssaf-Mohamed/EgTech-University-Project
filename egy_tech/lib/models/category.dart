import 'category_product.dart';

class Category {
  final int id;
  final String categoryName;
  final String description;
  final String categoryImage;
  final List<CategoryProduct> products;

  Category({
    required this.id,
    required this.categoryName,
    required this.description,
    required this.categoryImage,
    required this.products,
  });

  String get name => categoryName;

  String get image => categoryImage;

  factory Category.fromJson(Map<String, dynamic> json) {
    var productsJson = json['products'] as List? ?? [];
    return Category(
      id: json['id'] as int,
      categoryName: json['category_name'] as String,
      description: json['description'] as String,
      categoryImage: json['category_image'] as String,
      products: productsJson
          .map((item) => CategoryProduct.fromJson(item as Map<String, dynamic>))
          .toList(),
    );
  }
}
