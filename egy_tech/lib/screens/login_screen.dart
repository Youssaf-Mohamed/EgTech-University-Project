import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:my_app/repositories/auth_repository.dart';
import 'package:my_app/models/LoginResponse.dart';

import 'package:my_app/widgets/TextDivider.dart';

class LoginScreen extends StatefulWidget {
  LoginScreen({super.key});

  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _obscureText = true;
  bool isChecked = false;
  bool _isCheckingSession = true; // Added to track session check state

  @override
  void initState() {
    super.initState();
    _checkSession();
  }

  void _checkSession() async {
    final authRepository = Provider.of<AuthRepository>(context, listen: false);
    bool hasSession = await authRepository.isUserLoggedIn(); // Check session

    if (hasSession) {
      Navigator.pushReplacementNamed(context, '/');
    } else {
      setState(() {
        _isCheckingSession = false; // Allow login screen to render
      });
    }
  }

  void _login() async {
    String email = _emailController.text;
    String password = _passwordController.text;

    if (email.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill in all fields')),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      final authRepository =
          Provider.of<AuthRepository>(context, listen: false);
      LoginResponse response = await authRepository.login(email, password);

      if (response.status) {
        Navigator.pushReplacementNamed(context, '/');
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Login successful')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Login failed')),
        );
      }
    } catch (e) {
      print(e);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('An error has occurred: $e')),
      );
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return _isCheckingSession
        ? const Scaffold(
            body: Center(child: CircularProgressIndicator()), // Show loader while checking session
          )
        : Scaffold(
            body: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              child: SizedBox(
                height: MediaQuery.of(context).size.height,
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 45, 20, 20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Login",
                            style: GoogleFonts.poppins(
                              fontSize: 24,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(height: 10),
                          Text(
                            "Enter the email address you’d like to use to sign in to HandmadeHive. ",
                            style: GoogleFonts.poppins(fontSize: 14, color: Colors.grey),
                          ),
                          const SizedBox(height: 35),
                          TextField(
                            controller: _emailController,
                            decoration: const InputDecoration(
                              labelText: "Email@example.com",
                              border: OutlineInputBorder(),
                            ),
                          ),
                          const SizedBox(height: 15),
                          TextField(
                            controller: _passwordController,
                            obscureText: _obscureText,
                            decoration: InputDecoration(
                              labelText: "Password",
                              border: const OutlineInputBorder(),
                              suffixIcon: IconButton(
                                icon: Icon(_obscureText ? Icons.visibility : Icons.visibility_off),
                                onPressed: () {
                                  setState(() {
                                    _obscureText = !_obscureText;
                                  });
                                },
                              ),
                            ),
                          ),
                          const SizedBox(height: 10),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  Checkbox(
                                    value: isChecked,
                                    onChanged: (bool? value) {
                                      setState(() {
                                        isChecked = value ?? false;
                                      });
                                    },
                                    activeColor: Colors.green,
                                    checkColor: Colors.white,
                                  ),
                                  Text(
                                    "Remember me",
                                    style: TextStyle(color: Colors.grey[700]),
                                  ),
                                ],
                              ),
                              TextButton(
                                onPressed: () {},
                                child: Text(
                                  "Forgot Password?",
                                  style: TextStyle(color: Colors.red[700]),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 10),
                          ElevatedButton(
                            onPressed: _isLoading ? null : _login,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.red[700],
                              minimumSize: const Size(double.infinity, 60),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                            child: _isLoading
                                ? const CircularProgressIndicator(color: Colors.white)
                                : const Text(
                                    "Login",
                                    style: TextStyle(color: Colors.white, fontSize: 16),
                                  ),
                          ),
                          const SizedBox(height: 15),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              const Text("Don't have an account?"),
                              TextButton(
                                onPressed: () {
                                  Navigator.pushNamed(context, '/signup');
                                },
                                child: const Text("Sign Up", style: TextStyle(color: Colors.red)),
                              ),
                            ],
                          ),
                          const SizedBox(height: 75),
                          TextWithDividers(
                            text: "Or Continue with",
                            textStyle: GoogleFonts.poppins(fontSize: 14),
                            dividerColor: Colors.grey,
                            dividerThickness: 2,
                            padding: 8.0,
                          ),
                          const SizedBox(height: 15),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              IconButton(
                                onPressed: () {},
                                icon: Image.asset('assets/icons/google.png', width: 30),
                              ),
                              const SizedBox(width: 20),
                              IconButton(
                                onPressed: () {},
                                icon: Image.asset('assets/icons/apple-icon-4.png', width: 40),
                              ),
                              const SizedBox(width: 20),
                              IconButton(
                                onPressed: () {},
                                icon: Image.asset('assets/icons/facebook-icon.jpg', width: 30),
                              ),
                            ],
                          ),
                        ],
                      ),
                      Align(
                        alignment: Alignment.bottomCenter,
                        child: TextButton(
                          onPressed: () {},
                          child: const Text("Continue as Guest", style: TextStyle(color: Colors.red)),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          );
  }
}
