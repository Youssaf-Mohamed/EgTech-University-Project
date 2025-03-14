import 'package:flutter/material.dart';
import 'dart:ui';
import 'package:flutter/services.dart';
import '../data/dummy_data.dart';

class CollaborateScreen extends StatelessWidget {
  const CollaborateScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final screenSize = MediaQuery.of(context).size;

    return Scaffold(
      body: Stack(
        children: [
          _buildScrollableContent(context, screenSize),
          _buildTopNavBar(screenSize.width),
          _buildBottomNavBar(screenSize.width),
        ],
      ),
    );
  }

  // Scrollable main content area
  Widget _buildScrollableContent(BuildContext context, Size screenSize) {
    return SingleChildScrollView(
      child: Column(
        children: [
          SizedBox(height: screenSize.height * 0.1),
          _buildCategorySection(screenSize),
          _buildCollaborateContent(context, screenSize),
          _buildCollaborationButton(screenSize),
        ],
      ),
    );
  }

  // Category section with horizontal scroll
  Widget _buildCategorySection(Size screenSize) {
    return Padding(
      padding: EdgeInsets.symmetric(
        horizontal: screenSize.width * 0.04,
        vertical: screenSize.height * 0.02,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Section title
          _buildAnimatedText(
            'Collaborate by Category',
            style: TextStyle(
              fontSize: screenSize.width * 0.045,
              fontWeight: FontWeight.bold,
            ),
          ),
          SizedBox(height: screenSize.height * 0.02),
          // Animated
          SizedBox(
            height: screenSize.height * 0.22,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: DummyData.categories.length,
              separatorBuilder: (_, __) =>
                  SizedBox(width: screenSize.width * 0.03),
              itemBuilder: (_, index) =>
                  _buildAnimatedCategoryItem(index, screenSize),
            ),
          ),
        ],
      ),
    );
  }

  // Animated category item widget
  Widget _buildAnimatedCategoryItem(int index, Size screenSize) {
    return AnimatedContainer(
      duration: Duration(milliseconds: 300 + (index * 100)),
      curve: Curves.easeInOut,
      width: screenSize.width * 0.4,
      decoration: BoxDecoration(
        color: const Color(0xFFFFE7E9),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Center(
        child: Text(
          DummyData.categories[index]['name'],
          style: TextStyle(
            fontSize: screenSize.width * 0.04,
            fontWeight: FontWeight.w500,
          ),
        ),
      ),
    );
  }

  // Main content
  Widget _buildCollaborateContent(BuildContext context, Size screenSize) {
    return Padding(
      padding: EdgeInsets.all(screenSize.width * 0.05),
      child: AnimatedSwitcher(
        duration: const Duration(milliseconds: 500),
        child: Column(
          key: UniqueKey(),
          children: [
            _buildAnimatedText(
              DummyData.collaborateContent['title'],
              style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                    fontSize: screenSize.width * 0.05,
                  ),
            ),
            SizedBox(height: screenSize.height * 0.02),
            _buildAnimatedText(
              DummyData.collaborateContent['description'],
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    fontSize: screenSize.width * 0.035,                    
                  ),
            ),
            SizedBox(height: screenSize.height * 0.03),
            _buildAnimatedText(
              DummyData.collaborateContent['orderInfo'],
              style: Theme.of(context).textTheme.labelSmall?.copyWith(
                    fontSize: screenSize.width * 0.03,
                  ),
            ),
          ],
        ),
      ),
    );
  }

  // Animated collaboration button
  Widget _buildCollaborationButton(Size screenSize) {
    return AnimatedContainer(
      duration: const Duration(milliseconds: 300),
      margin: EdgeInsets.only(
        top: screenSize.height * 0.05,
        bottom: screenSize.height * 0.1,
        left: screenSize.width * 0.04,
        right: screenSize.width * 0.04,
      ),
      decoration: BoxDecoration(
        color: const Color(0xFFE50010),
        borderRadius: BorderRadius.circular(8),
        boxShadow: [
          BoxShadow(
            color: Colors.red.withOpacity(0.3),
            blurRadius: 10,
            spreadRadius: 2,
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () {},
          borderRadius: BorderRadius.circular(8),
          onHighlightChanged: (pressed) {
            if (pressed) {
              // -> handel event pressed
            }
          },
          child: Container(
            width: double.infinity,
            height: screenSize.height * 0.06,
            alignment: Alignment.center,
            child: const Text(
              'Collaboration Form',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildAnimatedText(String text, {TextStyle? style}) {
    return TweenAnimationBuilder(
      tween: Tween<double>(begin: 0, end: 1),
      duration: const Duration(milliseconds: 500),
      builder: (context, value, child) {
        return Opacity(
          opacity: value,
          child: Transform.translate(
            offset: Offset(0, (1 - value) * 20),
            child: child,
          ),
        );
      },
      child: Text(
        text,
        style: style,
        textAlign: TextAlign.center,
      ),
    );
  }

  Widget _buildTopNavBar(double width) {
    return Positioned(
      top: 0,
      child: Container(
        width: width,
        height: 88,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.8),
        ),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('9:41',
                    style: TextStyle(fontWeight: FontWeight.bold)),
                const Text('Collaborate', style: TextStyle(fontSize: 18)),
                IconButton(
                  icon: const Icon(Icons.search),
                  onPressed: () {},
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // Bottom navigation bar
  Widget _buildBottomNavBar(double width) {
    return Positioned(
      bottom: 0,
      child: Container(
        width: width,
        height: 83,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.8),
        ),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: DummyData.navItems.map((item) {
              return _buildAnimatedNavItem(item);
            }).toList(),
          ),
        ),
      ),
    );
  }

  Widget _buildAnimatedNavItem(Map<String, dynamic> item) {
    return TweenAnimationBuilder(
      tween: Tween<double>(begin: 0, end: 1),
      duration: const Duration(milliseconds: 500),
      builder: (context, value, child) {
        return Opacity(
          opacity: value,
          child: Transform.translate(
            offset: Offset(0, (1 - value) * 10),
            child: child,
          ),
        );
      },
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          IconButton(
            icon: Icon(item['icon']),
            onPressed: () {},
          ),
          Text(
            item['label'],
            style: const TextStyle(fontSize: 10),
          ),
        ],
      ),
    );
  }
}
