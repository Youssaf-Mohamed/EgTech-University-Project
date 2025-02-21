import 'package:flutter/material.dart';
import 'package:my_app/screens/auth_screen.dart';
import 'package:provider/provider.dart';
import 'repositories/auth_repository.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';

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
      initialRoute: '/',
      routes: {
        '/': (context) => ScreenWithAppBar(
              child: LoginScreen(),
              // title: 'Login'
            ),
        '/signup': (context) => ScreenWithAppBar(
              child: RegisterScreen(),
            ),
        '/main': (context) => ScreenWithAppBar(
              child: UserScreen(),
            ),
      },
    );
  }
}

class ScreenWithAppBar extends StatelessWidget {
  final String? title;
  final Widget child;
  final Color backgroundColor;

  ScreenWithAppBar(
      {this.title,
      required this.child,
      this.backgroundColor = Colors.transparent});

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
