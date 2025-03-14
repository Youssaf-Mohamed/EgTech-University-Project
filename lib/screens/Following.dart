import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:my_app/models/Vendors.dart';
import 'package:my_app/models/products.dart';

import 'package:my_app/widgets/CustomCardList.dart';
import 'package:my_app/widgets/CustomListView.dart';

import 'package:my_app/providers/following_data_provider.dart';

class Following extends ConsumerStatefulWidget {
  Following({super.key});

  @override
  ConsumerState<Following> createState() => _FollowingState();
}

class _FollowingState extends ConsumerState<Following> {
  @override
  Widget build(BuildContext context) {
    final followingDataAsync = ref.watch(followingDataProvider);

    return Scaffold(
        body: followingDataAsync.when(
      data: (apiResponse) {
        if (!apiResponse.status) {
          return Text("please sign in");
        }
        final products = apiResponse.data.products;
        final vendor = apiResponse.data.vendor;

        final mappedVendor =
            FollowingDataMapper.mapVendors(vendor as List<VendorList>);
        final mappedProducts =
            FollowingDataMapper.mapProducts(products as List<ProductList>);

        return Container(
          color: Colors.white,
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                CustomBanner(),
                SizedBox(height: 20),
                CustomListView(list: mappedVendor),
                SizedBox(height: 20),
                ListView.builder(
                  physics:
                      NeverScrollableScrollPhysics(), // Prevent nested scrolling
                  shrinkWrap: true, // Ensure it takes only the needed space
                  padding: const EdgeInsets.all(10),
                  itemCount: mappedProducts.length,
                  itemBuilder: (context, index) {
                    final item = mappedProducts[index];
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 10),
                      child: CustomCardList(
                        imageUrl: item["product_image"] as String? ?? '',
                        title: item["product_name"] as String? ?? '',
                        price: item["price"]?.toString() ?? 'no price listed',
                        vendorImageUrl: item["vendor_image"] as String? ?? '',
                        vendorName: item["vendorName"] as String? ?? '',
                        location: item["location"] as String? ?? '',
                        rating:
                            int.tryParse(item["rating"]?.toString() ?? '0') ??
                                0,
                        discountText: item["discount"] as String? ?? '',
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        );
      },
      error: (error, stack) => Center(child: Text("Error: $error")),
      loading: () => const Center(child: CircularProgressIndicator()),
    ));
  }
}

class CustomBanner extends StatelessWidget {
  const CustomBanner({super.key});

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(12),
      child: Stack(
        children: [
          Positioned.fill(
            child: Image.network(
              'https://th.bing.com/th/id/R.776cc2b3078a87c7faa7524cbd1ce7bc?rik=eLZl%2fcUgb6nHAw&pid=ImgRaw&r=0', // Replace with your image path
              fit: BoxFit.cover,
            ),
          ),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 30),
            decoration: BoxDecoration(
              color: Colors.black.withOpacity(0.5),
            ),
            child: const Center(
              child: Text(
                'Discover the Artistic Wonders of India - Bring a Unique Twist with your Vision & Ideas',
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 13.3,
                  fontWeight: FontWeight.w500,
                  fontFamily: 'Satoshi',
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class FollowingDataMapper {
  static List<Map<String, String>> mapVendors(List<VendorList> vendors) {
    return vendors
        .map((VendorList vendor) => {
              "name": vendor.brandName,
              "image": vendor.vendorImage,
            })
        .toList();
  }

  static List<Map<String, Object>> mapProducts(List<ProductList> products) {
    return products
        .map((ProductList prod) => {
              "product_name": prod.productName,
              "product_image": prod.productImage,
              "vendor_image": prod.vendorImage,
              "price": prod.price,
              "discount": prod.discount,
              "rating": prod.rating
            })
        .toList();
  }
}


// price in string

// rating