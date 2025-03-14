import 'package:flutter/material.dart';
import 'package:my_app/screens/404.dart';
import 'package:my_app/screens/Following.dart';
import 'package:my_app/screens/MyCollection.dart';
import 'package:my_app/screens/MyProfile.dart';
import 'package:my_app/widgets/custom_appbar.dart';
import './screens/home.dart';
import './screens/collaborate_screen.dart';
import './widgets/custom_bottom_nav.dart';

class MainScreen extends StatefulWidget {
  final int index;

  const MainScreen({super.key, this.index = 0});

  @override
  _MainScreenState createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  late int _selectedIndex;

  final List<Widget> _pages = [
    HomeScreen(),
    Following(),
    CollaborateScreen(),
    MyProfile(),
    NotFoundPage(),
  ];

  @override
  void initState() {
    super.initState();
    _selectedIndex = widget.index;
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
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
          child: _pages[_selectedIndex],
        ),
      ),
      bottomNavigationBar: CustomBottomNavBar(
        selectedIndex: _selectedIndex,
        onItemTapped: _onItemTapped,
      ),
    );
  }
}
