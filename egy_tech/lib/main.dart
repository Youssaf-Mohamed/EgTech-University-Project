import 'package:flutter/material.dart';
import 'login_screen.dart'; // استيراد شاشة تسجيل الدخول

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Login App',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.red),
        useMaterial3: true,
      ),
      home: LoginScreen(), // تشغيل شاشة تسجيل الدخول
    );
  }
}
