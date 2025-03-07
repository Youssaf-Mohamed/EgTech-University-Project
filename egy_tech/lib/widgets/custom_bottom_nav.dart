import 'package:flutter/material.dart';
import 'package:curved_navigation_bar/curved_navigation_bar.dart';

class CustomBottomNavBar extends StatelessWidget {
  final int selectedIndex;
  final Function(int) onItemTapped;

  const CustomBottomNavBar({
    super.key,
    required this.selectedIndex,
    required this.onItemTapped,
  });

  @override
  Widget build(BuildContext context) {
    final List<Widget> items = List.generate(4, (index) {
      return MouseRegion(
        cursor: SystemMouseCursors.click,
        child: GestureDetector(
          onTap: () => onItemTapped(index),
          child: Icon(
            [Icons.home, Icons.grid_view, Icons.groups, Icons.person][index],
            color: Colors.white,
          ),
        ),
      );
    });

    final int safeIndex = (selectedIndex >= 0 && selectedIndex < items.length)
        ? selectedIndex
        : 0;

    return CurvedNavigationBar(
      animationCurve: Curves.easeInOut,
      animationDuration: const Duration(milliseconds: 400),
      height: 55,
      index: safeIndex,
      onTap: onItemTapped,
      backgroundColor: Colors.white,
      color: Colors.red[700]!,
      items: items,
    );
  }
}
