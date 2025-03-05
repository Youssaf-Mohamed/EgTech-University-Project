import 'package:flutter/material.dart';
import 'MainAppBar.dart';
import 'SearchBar.dart';

class CustomAppBar extends StatelessWidget implements PreferredSizeWidget {
  @override
  final Size preferredSize; // Preferred size for AppBar

  const CustomAppBar({Key? key})
      : preferredSize = const Size.fromHeight(90),
        super(key: key);

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: AppBar(
        scrolledUnderElevation: 0,
        automaticallyImplyLeading: false,
        backgroundColor: Colors.white,
        toolbarHeight: 90,
        flexibleSpace: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Column(
            mainAxisSize: MainAxisSize.max,
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: const [
              Mainappbar(),
              SearchBarWidget(),
            ],
          ),
        ),
      ),
    );
  }
}
