import 'package:animated_splash_screen/animated_splash_screen.dart';
import 'package:flutter/material.dart';
import 'package:my_app/screens/auth_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:provider/provider.dart';
import 'repositories/auth_repository.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
import 'screens/walkthrough.dart';
// import 'screens/user_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Check walkthrough status
  final prefs = await SharedPreferences.getInstance();
  final bool walkthroughCompleted = false; // Debugging: Always show walkthrough

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
      home: SplashScreen(walkthroughCompleted: walkthroughCompleted),
      routes: {
        '/walkthrough': (context) => const Walkthrough(),
        '/signup': (context) => ScreenWithAppBar(child: RegisterScreen()),
        '/main': (context) => ScreenWithAppBar(child: UserScreen()),
      },
    );
  }
}

class SplashScreen extends StatelessWidget {
  final bool walkthroughCompleted;

  const SplashScreen({super.key, required this.walkthroughCompleted});

  @override
  Widget build(BuildContext context) {
    return AnimatedSplashScreen(
      splash: Container(
        height: double.infinity,
        width: double.infinity,
          color: Colors.white,
        child: Image.asset("assets/icons/logo.png", width: 500,),
      ),
      nextScreen: walkthroughCompleted ? LoginScreen() : Walkthrough(),
      splashTransition: SplashTransition.fadeTransition,
      duration: 2000,
    );
  }
}

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
