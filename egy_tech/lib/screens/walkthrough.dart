import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:my_app/screens/login_screen.dart';
import 'package:my_app/main.dart';

class Walkthrough extends StatefulWidget {
  const Walkthrough({super.key});

  @override
  State<Walkthrough> createState() => _WalkthroughState();
}

class _WalkthroughState extends State<Walkthrough> {
  final PageController _pageController = PageController();
  int _currentPageIndex = 0;

  final List<Map<String, String>> _walkthroughData = [
    {
      'imagePath': 'assets/images/first_splash.png',
      'title': 'Join our community of creators!',
      'description':
          '20,000+ Indian Craftsmen one click away! Connect to the community of Artisans & Designers Directly.',
    },
    {
      'imagePath': 'assets/images/second_splash.png',
      'title': 'Buy from the people not Brands',
      'description':
          'Discover the stories of unique crafts and buy from the makers themselves!',
    },
    {
      'imagePath': 'assets/images/third_splash.png',
      'title': 'Collaborate & Co-Create!',
      'description':
          'Bring your creative vision to life with our makers exceptional craftsmanship!',
    },
  ];

  void _completeWalkthrough() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('walkthroughCompleted', true);
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(
        builder: (context) => LoginScreen(),

      ),
      (Route<dynamic> route) => false,
    );
  }

  void _onPageChanged(int index) {
    setState(() {
      _currentPageIndex = index;
    });
  }

  void _goToNextPage() {
    if (_currentPageIndex < _walkthroughData.length - 1) {
      _pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    } else {
      _completeWalkthrough();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          PageView.builder(
            controller: _pageController,
            onPageChanged: _onPageChanged,
            itemCount: _walkthroughData.length,
            itemBuilder: (context, index) {
              final data = _walkthroughData[index];
              return WalkthroughPage(
                imagePath: data['imagePath']!,
                title: data['title']!,
                description: data['description']!,
              );
            },
          ),
          Positioned(
            top: 40,
            right: 20,
            child: TextButton(
              onPressed: _completeWalkthrough,
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    "Skip",
                    style: TextStyle(
                      color: Colors.red[900],
                      fontSize: 16,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                  const SizedBox(width: 1),
                  Icon(
                    Icons.navigate_next,
                    color: Colors.red[900],
                    size: 24,
                  ),
                ],
              ),
            ),
          ),
          Positioned(
            bottom: 40,
            left: 20,
            right: 20,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: List.generate(_walkthroughData.length, (index) {
                    return Container(
                      margin: const EdgeInsets.symmetric(horizontal: 4),
                      width: index == _currentPageIndex ? 24 : 8,
                      height: 8,
                      decoration: BoxDecoration(
                        color: index == _currentPageIndex
                            ? Colors.red
                            : Colors.grey.shade400,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    );
                  }),
                ),
                Container(
                  decoration: BoxDecoration(
                    color: Colors.red[900],
                    shape: _currentPageIndex == _walkthroughData.length - 1
                        ? BoxShape.rectangle
                        : BoxShape.circle,
                    borderRadius: _currentPageIndex == _walkthroughData.length - 1
                        ? BorderRadius.circular(8)
                        : null,
                  ),
                  child: IconButton(
                    icon: _currentPageIndex == _walkthroughData.length - 1
                        ? const Text(
                            "Get Started",
                            style: TextStyle(color: Colors.white),
                          )
                        : const Icon(
                            Icons.navigate_next,
                            color: Colors.white,
                          ),
                    onPressed: _goToNextPage,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class WalkthroughPage extends StatelessWidget {
  final String imagePath;
  final String title;
  final String description;

  const WalkthroughPage({
    super.key,
    required this.imagePath,
    required this.title,
    required this.description,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        image: DecorationImage(
          image: AssetImage(imagePath),
          fit: BoxFit.cover,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              title,
              style: const TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 40),
            Text(
              description,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 14,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}