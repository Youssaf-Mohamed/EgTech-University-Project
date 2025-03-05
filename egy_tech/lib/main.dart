// lib/main.dart
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
// import 'package:my_app/providers/providers.dart'; 
import 'package:my_app/screens/LocationScreen.dart';
import 'package:my_app/screens/MyCollection.dart';
import 'package:my_app/screens/collaborate_screen.dart';
import 'package:my_app/screens/home.dart';
import 'package:my_app/screens/location_map_screen.dart';
import 'package:my_app/screens/login_screen.dart';
import 'package:my_app/screens/register_screen.dart';
import 'package:my_app/screens/walkthrough.dart';
import 'package:my_app/screens/404.dart';
import 'package:my_app/screens/MyProfile.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  // Check walkthrough status from shared preferences
  final prefs = await SharedPreferences.getInstance();
  final bool walkthroughCompleted = prefs.getBool('walkthroughCompleted') ?? false;
  
  runApp(
    ProviderScope( // Wrap your entire app in ProviderScope for Riverpod.
      child: MyApp(walkthroughCompleted: walkthroughCompleted),
    ),
  );
}

class MyApp extends StatelessWidget {
  final bool walkthroughCompleted;
  const MyApp({Key? key, required this.walkthroughCompleted}) : super(key: key);
  
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
        '/walkthrough': (context) => const Walkthrough(),
        '/login': (context) =>  LoginScreen(),
        '/signup': (context) => RegisterScreen(),
        '/': (context) => MainScreen(),
        '/myprofile': (context) => MyProfile(),
        '/mycollection': (context) => MyCollection(),
        '/collaborate': (context) => CollaborateScreen(),
        // '/location': (context) => LocationScreen(),
        // '/LocationMapScreen': (context) => LocationMapScreen(),
      },
      onGenerateRoute: (settings) {
        return MaterialPageRoute(
          builder: (context) =>  NotFoundPage(),
        );
      },
    );
  }
}
