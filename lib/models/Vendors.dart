// ignore_for_file: unnecessary_cast

class VendorList {
  final int vendorId;
  final String brandName;
  final String vendorImage;

  VendorList({
    this.vendorId = 0,
    this.brandName = '',
    this.vendorImage = '',
  });

  factory VendorList.fromJson(Map<String, dynamic> json) {
    return VendorList(
      vendorId: json['vendor_id'] as int,
      brandName: json['brand_name'].toString() as String,
      vendorImage: json['vendor_image'].toString() as String,
    );
  }
}
