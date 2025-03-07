import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:my_app/models/category.dart';
import 'package:my_app/models/products.dart';

import 'package:my_app/widgets/AdBanner.dart';
import 'package:my_app/widgets/CardSlider.dart';
import 'package:my_app/widgets/CustomListView.dart';
import 'package:my_app/config/Constants.dart';
import 'package:my_app/providers/home_data_provider.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  // Sample static category list.
  // List<Map<String, String>> getCategory() {
  //   return [
  //     {"name": "Artisan", "image": "assets/images/main_testing/artist.png"},
  //     {"name": "Designer", "image": "assets/images/main_testing/designer.png"},
  //     {"name": "Crafts", "image": "assets/images/main_testing/crafts.png"},
  //     {"name": "Products", "image": "assets/images/main_testing/products.png"},
  //     {"name": "Workshop", "image": "assets/images/main_testing/workshop.png"},
  //     {"name": "Events", "image": "assets/images/main_testing/events.png"},
  //   ];
  // }

  // Sample static card list.
  // List<Map<String, String>> getCardItem() {
  //   return [
  //     {
  //       "product_name": "Khavda Pottery",
  //       "product_image":
  //           "https://th.bing.com/th/id/R.27cf23f37d3fe1fa0dd5db0da76126f9?rik=gzJrE3yP9ejKbw&pid=ImgRaw&r=0",
  //       "vendor_image":
  //           "https://i.ibb.co/B5qQFMzq/WIN-20250128-14-29-18-Pro.jpg",
  //       "price": "346.00",
  //       "discount": "20%"
  //     },
  //     {
  //       "product_name": "Handmade Vase",
  //       "product_image":
  //           "https://th.bing.com/th/id/OIP.y92FBzeshMp7q1gs7wR4LwHaE7?rs=1&pid=ImgDetMain",
  //       "vendor_image":
  //           "https://i.ibb.co/B5qQFMzq/WIN-20250128-14-29-18-Pro.jpg",
  //       "price": "400.00",
  //       "discount": "10%"
  //     },
  //     {
  //       "product_name": "Decor Pot",
  //       "product_image":
  //           "https://th.bing.com/th/id/OIP._LUrmB7Hnxk_hD6XYrL7ggHaIW?w=794&h=895&rs=1&pid=ImgDetMain",
  //       "vendor_image":
  //           "https://i.ibb.co/B5qQFMzq/WIN-20250128-14-29-18-Pro.jpg",
  //       "price": "250.00",
  //       "discount": "15%"
  //     }
  //   ];
  // }

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  @override
  Widget build(BuildContext context) {
    final homeDataAsync = ref.watch(homeDataProvider);

    return Scaffold(
      body: homeDataAsync.when(
        data: (apiResponse) {
          final products = apiResponse.data.mostDemanded;
          final categories = apiResponse.data.categories;

          final List<Map<String, String>> mappedCategories =
              (categories as List<Category>)
                  .map((Category cat) => <String, String>{
                        "name": cat.name,
                        "image": cat.image,
                      })
                  .toList();

          final List<Map<String, String>> mappedProducts =
              (products as List<MostDemandedProduct>)
                  .map((MostDemandedProduct prod) => <String, String>{
                        "product_name": prod.productName,
                        "product_image": prod.productImage,
                        "vendor_image": prod.vendorImage,
                        "price": prod.price,
                        "discount": prod.discount,
                      })
                  .toList();

          return Container(
            color: Colors.white,
            child: Padding(
              padding: const EdgeInsets.all(8.0),
              child: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    CustomListView(list: mappedCategories),
                    const SizedBox(height: 20),
                    const AdBanner(),
                    const SizedBox(height: 20),
                    const Text(
                      "Featured",
                      style: TextStyle(
                          color: Colors.black,
                          fontFamily: 'Satoshi',
                          fontSize: 19.2,
                          fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 15),
                    const Featured(),
                    const SizedBox(height: 20),
                    const Text(
                      "Trending Product",
                      style: TextStyle(
                          color: Colors.black,
                          fontFamily: 'Satoshi',
                          fontSize: 19.2,
                          fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 15),
                    CardSlider(Cardlist: mappedProducts),
                  ],
                ),
              ),
            ),
          );
        },
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (error, stack) => Center(child: Text("Error: $error")),
      ),
    );
  }
}

class Featured extends StatelessWidget {
  const Featured({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(borderRadius: BorderRadius.circular(15)),
      height: 280,
      child: Column(
        children: [
          // Image Stack
          Expanded(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(15),
              child: Stack(
                clipBehavior: Clip.hardEdge,
                children: [
                  // Background Image
                  Positioned.fill(
                    child: Image.asset(
                      'assets/images/main_testing/featured.png', // Background image
                      fit: BoxFit.cover,
                    ),
                  ),
                  Positioned(
                    top: 20,
                    right: 0,
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 7, vertical: 5),
                      decoration: const BoxDecoration(
                          color: Color.fromARGB(160, 158, 158, 158),
                          borderRadius: BorderRadius.only(
                              topLeft: Radius.circular(20),
                              bottomLeft: Radius.circular(20))),
                      child: Row(
                        children: [
                          SizedBox(
                              width: 20,
                              child: Image.asset(Constants.IconLogo)),
                          const SizedBox(width: 5),
                          const Text(
                            "New In",
                            style: TextStyle(color: Colors.white),
                          ),
                        ],
                      ),
                    ),
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    left: 0,
                    child: Container(
                      height: 60,
                      color: const Color.fromARGB(163, 0, 0, 0),
                      child: const Center(
                          child: Text(
                        "Tribal Lambani Jewellery",
                        style: TextStyle(
                            color: Colors.white,
                            fontFamily: 'Satoshi',
                            fontSize: 16,
                            fontWeight: FontWeight.w500),
                      )),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
