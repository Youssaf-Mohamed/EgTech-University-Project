import 'package:flutter/material.dart';
import 'package:my_app/screens/auth_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:provider/provider.dart';
import 'repositories/auth_repository.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
import 'screens/walkthrough.dart'; // Import the updated walkthrough file
import 'screens/404.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Check walkthrough status
  final prefs = await SharedPreferences.getInstance();
  // final bool walkthroughCompleted = prefs.getBool('walkthroughCompleted') ?? false;
  final bool walkthroughCompleted = false; // for debugging
  runApp(
    MultiProvider(
      providers: [
        Provider(create: (_) => AuthRepository()),
      ],
      child: MyApp(walkthroughCompleted: walkthroughCompleted),
    ),
  );
}

class MyApp extends StatelessWidget {
  final bool walkthroughCompleted;

  const MyApp({super.key, required this.walkthroughCompleted});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Login App',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.red),
        useMaterial3: true,
      ),
      initialRoute: walkthroughCompleted ? '/login' : '/walkthrough',
      routes: {
        '/walkthrough': (context) => const Walkthrough(), // Single walkthrough route
        '/login': (context) => LoginScreen(),
        '/signup': (context) => RegisterScreen(),
        '/': (context) => ScreenWithAppBar(child: UserScreen()),
      },
      // Handle unknown routes (404 page)
      onGenerateRoute: (settings) {
        return MaterialPageRoute(
          builder: (context) => NotFoundPage(), // Show the 404 page
        );
      },
    );
  }
}



// ScreenWithAppBar Widget (unchanged)
class ScreenWithAppBar extends StatelessWidget {
  final String? title;
  final Widget child;
  final Color backgroundColor;

  const ScreenWithAppBar({
    this.title,
    required this.child,
    this.backgroundColor = Colors.transparent,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: title != null ? Text(title!) : null,
        backgroundColor: backgroundColor,
      ),
      body: child,
    );
  }
}