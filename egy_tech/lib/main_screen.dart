import 'package:flutter/material.dart';
import 'package:my_app/screens/404.dart';
import 'package:my_app/screens/Following.dart';
// import 'package:my_app/screens/MyCollection.dart';
import 'package:my_app/screens/MyProfile.dart';
import 'package:my_app/widgets/custom_appbar.dart';
import './screens/home.dart';
import './screens/collaborate_screen.dart';
import './widgets/custom_bottom_nav.dart';

class MainScreen extends StatefulWidget {
  final int index;
  MainScreen({required this.index});

  @override
  _MainScreenState createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  late int _selectedIndex; // The current page index (e.g., 0, 6, etc.)
  late int _lastMainTabIndex; // The last main tab index (0-3)

  // List of pages, including main tabs and inner pages
  final List<Widget> _pages = [
    HomeScreen(), // Index 0: Main tab
    Placeholder(), // Index 1: Main tab
    NotFoundPage(),
    CollaborateScreen(), // Index 3: Main tab
    MyProfile(),
    Following(), // Index 4: Could be an inner page
    Placeholder(), // Index 5: Another page
    // DetailPage(),         // Index 6: Inner/param page
  ];

  @override
  void initState() {
    super.initState();
    _selectedIndex = widget.index; // Start with the initial index
    _lastMainTabIndex =
        widget.index.clamp(0, 3); // Clamp to main tab range (0-3)
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index; // Update the current page
      if (index >= 0 && index <= 3) {
        // If it's a main tab
        _lastMainTabIndex = index; // Update the last main tab
      }
      // If index > 3 (e.g., 6), _lastMainTabIndex stays the same
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: CustomAppBar(),
      body: Padding(
        padding: const EdgeInsets.only(top: 8, right: 8, left: 8),
        child: AnimatedSwitcher(
          duration: Duration(milliseconds: 500),
          child: _pages[_selectedIndex], // Show the current page
        ),
      ),
      bottomNavigationBar: CustomBottomNavBar(
        selectedIndex: _lastMainTabIndex, // Highlight the last main tab
        onItemTapped: _onItemTapped,
      ),
    );
  }
}
