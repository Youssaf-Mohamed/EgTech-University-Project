import 'package:flutter/material.dart';

class CustomBottomNavBar extends StatelessWidget {
  final int selectedIndex;
  final Function(int) onItemTapped;

  const CustomBottomNavBar({
    super.key,
    required this.selectedIndex,
    required this.onItemTapped,
  });

  void _navigateToPage(BuildContext context, int index) {
    if (index == selectedIndex) return;

    switch (index) {
      case 0:
        Navigator.pushReplacementNamed(context, '/');
        break;
      case 1:
        Navigator.pushReplacementNamed(context, '/category');
        break;
      case 2:
        Navigator.pushReplacementNamed(context, '/studio');
      case 3:
        Navigator.pushReplacementNamed(context, '/collaborate');
        break;
      case 4:
        Navigator.pushReplacementNamed(context, '/myprofile');
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    return BottomNavigationBar(
      currentIndex: selectedIndex,
      onTap: (index) => _navigateToPage(context, index),
      backgroundColor: Colors.white,
      selectedItemColor: Colors.red,
      unselectedItemColor: Colors.grey,
      type: BottomNavigationBarType.fixed,
      items: const [
        BottomNavigationBarItem(
          icon: Icon(Icons.home),
          label: 'Home',
        ),
        BottomNavigationBarItem(
          icon: Icon(Icons.grid_view),
          label: 'Category',
        ),
        BottomNavigationBarItem(
          icon: Icon(Icons.storefront),
          label: 'Studio',
        ),
        BottomNavigationBarItem(
          icon: Icon(Icons.groups),
          label: 'Collaborate',
        ),
        BottomNavigationBarItem(
          icon: Icon(Icons.person),
          label: 'Profile',
        ),
      ],
    );
  }
}
