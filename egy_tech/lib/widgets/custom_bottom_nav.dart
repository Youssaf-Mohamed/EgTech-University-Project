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
    final List<IconData> icons = [
      Icons.home,
      Icons.grid_view,
      Icons.groups,
      Icons.person,
      Icons.settings, // Added fifth icon
    ];

    return CurvedNavigationBar(
      animationCurve: Curves.easeInOut,
      animationDuration: const Duration(milliseconds: 400),
      height: 55,
      index: selectedIndex.clamp(0, icons.length - 1),
      onTap: onItemTapped,
      backgroundColor: Colors.white,
      color: Colors.red[700]!,
      items: icons.map((icon) => Icon(icon, color: Colors.white, size: 30)).toList(),
    );
  }
}
