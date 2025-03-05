import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:my_app/widgets/AdBanner.dart';
import 'package:my_app/widgets/MainAppBar.dart';
import 'package:my_app/widgets/SearchBar.dart';
import 'package:my_app/widgets/custom_appbar.dart';
import 'package:my_app/widgets/custom_bottom_nav.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  List<Map<String, String>> getCategory() {
    return [
      {"name": "Artisan", "image": "assets/images/main_testing/artist.png"},
      {"name": "Designer", "image": "assets/images/main_testing/designer.png"},
      {"name": "Crafts", "image": "assets/images/main_testing/crafts.png"},
      {"name": "Products", "image": "assets/images/main_testing/products.png"},
      {"name": "Workshop", "image": "assets/images/main_testing/workshop.png"},
      {"name": "Events", "image": "assets/images/main_testing/events.png"},
    ];
  }

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  @override
  Widget build(BuildContext context) {
    final Category = widget.getCategory();
    return Scaffold(
      extendBodyBehindAppBar: false,
      backgroundColor: Colors.white,
      appBar: CustomAppBar(),
      body: Container(
        color: Colors.white,
        child: Padding(
          padding: const EdgeInsets.all(8.0),
          child: SingleChildScrollView(
              child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              SizedBox(
                height: 150,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  physics: const BouncingScrollPhysics(),
                  padding: EdgeInsets.symmetric(vertical: 5),
                  itemCount: Category.length * 2,
                  itemBuilder: (context, index) {
                    final cat = Category[index % Category.length];
                    return Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                      child: Container(
                        width: 130,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(10),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.1),
                              spreadRadius: 1,
                              blurRadius: 6,
                              offset: const Offset(0, 3),
                            ),
                          ],
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.start,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(6),
                              child: Image.asset(
                                cat["image"]!,
                                width: 120,
                                height: 100,
                                fit: BoxFit.cover,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              cat["name"]!,
                              style: const TextStyle(
                                  fontSize: 14, fontWeight: FontWeight.w500),
                              textAlign: TextAlign.center,
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
              ),
              SizedBox(
                height: 20,
              ),
              AdBanner(),
              SizedBox(
                height: 20,
              ),
              Container(
                decoration: BoxDecoration(
                    borderRadius: BorderRadius.all(Radius.circular(15))),
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
                                'assets/images/main_testing/featured.png', // Add your background image
                                fit: BoxFit.cover,
                              ),
                            ),

                            Positioned(
                              top: 20,
                              right: 0,
                              child: Container(
                                padding: EdgeInsets.symmetric(
                                    horizontal: 7, vertical: 5),
                                decoration: BoxDecoration(
                                    color: const Color.fromARGB(
                                        160, 158, 158, 158),
                                    borderRadius: BorderRadius.only(
                                        topLeft: Radius.circular(20),
                                        bottomLeft: Radius.circular(20))),
                                child: Row(
                                  children: [
                                    SizedBox(
                                        width: 20,
                                        child: Image.asset(
                                            "assets/icons/IconLogo.png")),
                                    SizedBox(width: 5),
                                    Text(
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
                                    child: Center(
                                        child: Text(
                                      "Tribal Lambani Jewellery",
                                      style: TextStyle(
                                          color: Colors.white,
                                          fontFamily: 'Satoshi',
                                          fontSize: 16,
                                          fontWeight: FontWeight.w500),
                                    ))))
                          ],
                        ),
                      ),
                    ),

                    // Bottom Text Label
                  ],
                ),
              )
            ],
          )),
        ),
      ),
      bottomNavigationBar: CustomBottomNavBar(
        selectedIndex: 0,
        onItemTapped: (index) {},
      ),
    );
  }
}
