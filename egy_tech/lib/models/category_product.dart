class CategoryProduct {
  final int productId;
  final String productName;
  final String productImage;
  final String price;
  final String discount;

  CategoryProduct({
    required this.productId,
    required this.productName,
    required this.productImage,
    required this.price,
    required this.discount,
  });

  factory CategoryProduct.fromJson(Map<String, dynamic> json) {
    return CategoryProduct(
      productId: json['product_id'] as int,
      productName: json['product_name'] as String,
      productImage: json['product_image'] as String,
      price: json['price'] as String,
      discount: json['discount'] as String,
    );
  }
}
