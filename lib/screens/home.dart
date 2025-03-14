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
          //  api getters
          final products = apiResponse.data.mostDemanded;
          final categories = apiResponse.data.categories;
          final promotedProducts = apiResponse.data.promotedProducts;
          final trendingCategories = apiResponse.data.trendingCategories;

          // api mappers
          final mappedCategories =
              HomeDataMapper.mapCategories(categories as List<Category>);
          final mappedProducts =
              HomeDataMapper.mapProducts(products as List<ProductList>);
          final mappedPromotedProducts = HomeDataMapper.mapPromotedProducts(
              promotedProducts as List<ProductList>);
          final mappedTrendingCategories = HomeDataMapper.mapTrendingCategories(
              trendingCategories as List<Category>);

          const CustomTextStyle = TextStyle(
              color: Colors.black,
              fontFamily: 'Satoshi',
              fontSize: 19.2,
              fontWeight: FontWeight.bold);
          return Container(
            color: Colors.white,
            child: SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CustomListView(list: mappedCategories),
                  const SizedBox(height: 20),
                  const AdBanner(),
                  const SizedBox(height: 20),
                  Text("Featured", style: CustomTextStyle),
                  const SizedBox(height: 15),
                  const Featured(),
                  const SizedBox(height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text("Promoted products", style: CustomTextStyle),
                      IconButton(
                          onPressed: () {
                            Navigator.pushNamed(context, '/notfound');
                          },
                          icon: Icon(
                            size: 24,
                            Icons.arrow_forward_rounded,
                            color: Colors.black,
                          ))
                    ],
                  ),
                  const SizedBox(height: 15),
                  CardSlider(Cardlist: mappedPromotedProducts),
                  const SizedBox(height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text("Trending Product", style: CustomTextStyle),
                      IconButton(
                          onPressed: () {
                            Navigator.pushNamed(context, '/notfound');
                          },
                          icon: Icon(
                            size: 24,
                            Icons.arrow_forward_rounded,
                            color: Colors.black,
                          ))
                    ],
                  ),
                  const SizedBox(height: 15),
                  CardSlider(Cardlist: mappedProducts),
                  const SizedBox(height: 20),
            
                  // categories loop
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: mappedTrendingCategories.map((category) {
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                category["name"],
                                style: CustomTextStyle,
                              ),
                              IconButton(
                                  onPressed: () {
                                    Navigator.pushNamed(context, '/notfound');
                                  },
                                  icon: Icon(
                                    size: 24,
                                    Icons.arrow_forward_rounded,
                                    color: Colors.black,
                                  ))
                            ],
                          ),
                          const SizedBox(height: 15),
                          CardSlider(Cardlist: category["products"]),
                          const SizedBox(height: 20),
                        ],
                      );
                    }).toList(),
                  ),
                ],
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

class HomeDataMapper {
  static List<Map<String, String>> mapCategories(List<Category> categories) {
    return categories
        .map((Category cat) => {
              "name": cat.categoryName,
              "image": cat.categoryImage,
            })
        .toList();
  }

  static List<Map<String, String>> mapProducts(List<ProductList> products) {
    return products
        .map((ProductList prod) => {
              "product_name": prod.productName,
              "product_image": prod.productImage,
              "vendor_image": prod.vendorImage,
              "price": prod.price,
              "discount": prod.discount,
            })
        .toList();
  }

  static List<Map<String, String>> mapPromotedProducts(
      List<ProductList> promotedProducts) {
    return promotedProducts
        .map((ProductList prod) => {
              "product_name": prod.productName,
              "product_image": prod.productImage,
              "vendor_image": prod.vendorImage,
              "price": prod.price,
              "discount": prod.discount,
            })
        .toList();
  }

  static List<Map<String, dynamic>> mapTrendingCategories(
      List<Category> trendingCategories) {
    return trendingCategories
        .map((Category cat) => {
              "name": cat.categoryName,
              "products": cat.products
                  .map((ProductList prod) => {
                        "product_name": prod.productName,
                        "product_image": prod.productImage,
                        "price": prod.price,
                        "discount": prod.discount,
                      })
                  .toList(),
            })
        .toList();
  }
}
