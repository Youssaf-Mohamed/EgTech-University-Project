class MostDemandedProduct {
  final int productId;
  final String productName;
  final String productImage;
  final String vendorImage;
  final String price;
  final String discount;
  final String brandName;

  MostDemandedProduct({
    required this.productId,
    required this.productName,
    required this.productImage,
    required this.vendorImage,
    required this.price,
    required this.discount,
    required this.brandName,
  });

  factory MostDemandedProduct.fromJson(Map<String, dynamic> json) {
    return MostDemandedProduct(
      productId: json['product_id'] as int,
      productName: json['product_name'] as String,
      productImage: json['product_image'] as String,
      vendorImage: json['vendor_image'] as String,
      price: json['price'] as String,
      discount: json['discount'] as String,
      brandName: json['brand_name'] as String,
    );
  }
}
