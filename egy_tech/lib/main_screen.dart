import 'package:flutter/material.dart';
import 'package:my_app/screens/404.dart';
import 'package:my_app/screens/MyProfile.dart';
import 'package:my_app/widgets/custom_appbar.dart';
import './screens/home.dart';
import './screens/collaborate_screen.dart';
import './widgets/custom_bottom_nav.dart';

class MainScreen extends StatefulWidget {
  @override
  _MainScreenState createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;

  final List<Widget> _pages = [
    HomeScreen(),
    NotFoundPage(),
    CollaborateScreen(),
    MyProfile(),
  ];

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: false,
      backgroundColor: Colors.white,
      appBar: CustomAppBar(),
      body: AnimatedSwitcher(
        duration: Duration(milliseconds: 500),
        child: _pages[_selectedIndex],
      ),
      bottomNavigationBar: CustomBottomNavBar(
        selectedIndex: _selectedIndex,
        onItemTapped: _onItemTapped,
      ),
    );
  }
}
