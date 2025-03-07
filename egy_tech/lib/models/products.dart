// ignore_for_file: unnecessary_cast

class ProductList {
  final int productId;
  final String productName;
  final String productImage;
  final String vendorImage;
  final String price;
  final String discount;
  final String brandName;

  ProductList({
    this.productId= 0,
    this.productName = '',
    this.productImage = '',
    this.vendorImage = '',
    this.price = '',
    this.discount = '',
    this.brandName = '',
  });

  factory ProductList.fromJson(Map<String, dynamic> json) {
    return ProductList(
      productId: json['product_id'] as int,
      productName: json['product_name'].toString() as String,
      productImage: json['product_image'].toString() as String,
      vendorImage: json['vendor_image'].toString() as String,
      price: json['price'].toString() as String,
      discount: json['discount'].toString() as String,
      brandName: json['brand_name'].toString() as String,
    );
  }
}
