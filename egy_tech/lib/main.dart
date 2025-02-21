import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'repositories/auth_repository.dart';
import 'screens/login_screen.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        Provider(create: (_) => AuthRepository()),
      ],
      child: MyApp(),
    ),
  );
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Login App',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.red),
        useMaterial3: true,
      ),
      home: LoginScreen(), // تشغيل شاشة تسجيل الدخول
    );
  }
}
