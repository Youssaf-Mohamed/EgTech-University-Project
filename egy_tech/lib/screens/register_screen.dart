// ignore_for_file: prefer_const_constructors, use_build_context_synchronously

import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:my_app/responses/RegisterResponse.dart';
import 'package:my_app/providers/providers.dart';
import 'package:my_app/widgets/LocationPickerDialog.dart';

enum Gender { male, female }

extension GenderExtension on Gender {
  String get value {
    switch (this) {
      case Gender.male:
        return 'male';
      case Gender.female:
        return 'female';
    }
  }
}

class RegisterScreen extends ConsumerStatefulWidget {
  const RegisterScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends ConsumerState<RegisterScreen> {
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _firstnameController = TextEditingController();
  final TextEditingController _lastnameController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController =
      TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();

  bool _isLoading = false;
  bool _obscureText = true;
  bool _obscureConfirmText = true;
  Gender? _selectedGender;
  File? _selectedImage;
  int _currentStep = 0;

  // Validation states
  bool _isStep1Valid = false;
  bool _isStep2Valid = false;
  bool _isStep3Valid = false;
  bool _isStep4Valid = false;

  bool get isFirstStep => _currentStep == 0;
  bool get isLastStep => _currentStep == steps().length - 1;

  bool validateStep1() {
    return true;
  }

  bool validateStep2() {
    return _firstnameController.text.isNotEmpty &&
        _lastnameController.text.isNotEmpty &&
        _phoneController.text.isNotEmpty &&
        _selectedGender != null &&
        _selectedImage != null;
  }

  bool validateStep3() {
    bool emailValid = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$')
        .hasMatch(_emailController.text);
    bool passwordValid = _passwordController.text.isNotEmpty &&
        _passwordController.text.length >= 6;
    bool passwordsMatch =
        _passwordController.text == _confirmPasswordController.text;
    return emailValid && passwordValid && passwordsMatch;
  }

  bool validateStep4() {
    return _addressController.text.isNotEmpty;
  }

  Future<void> _pickImage() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.gallery);

    if (pickedFile != null) {
      setState(() {
        _selectedImage = File(pickedFile.path);
        _isStep2Valid = validateStep2();
      });
    }
  }

  void _register() async {
    String name = "${_firstnameController.text} ${_lastnameController.text}";
    String email = _emailController.text;
    String password = _passwordController.text;
    String confirmPassword = _confirmPasswordController.text;
    String address = _addressController.text;
    String phone = _phoneController.text;

    if (email.isEmpty ||
        password.isEmpty ||
        confirmPassword.isEmpty ||
        _selectedGender == null ||
        address.isEmpty ||
        phone.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill in all fields')),
      );
      return;
    }

    if (password != confirmPassword) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Passwords do not match')),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      // Use Riverpod's ref to read the authRepositoryProvider.
      final authRepository = ref.read(authRepositoryProvider);
      String genderString = _selectedGender!.value;

      // Upload the image and other data to the server
      RegisterResponse response = await authRepository.register(
        name: name,
        email: email,
        password: password,
        gender: genderString,
        image: _selectedImage,
        phone: phone,
        address: address,
      );

      if (response.status) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Registration successful')),
        );
        Navigator.pushNamed(context, '/'); // Redirect to login screen or home
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Registration failed')),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('An error has occurred: $e')),
      );
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  List<Step> steps() => [
        Step(
            state: _isStep1Valid ? StepState.complete : StepState.indexed,
            isActive: _currentStep >= 0,
            title: const Text("Getting started"),
            content: Container(
              width: double.infinity,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: const [
                  Text(
                    "Welcome to EgyTech!",
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.w600),
                  ),
                  Text(
                    "want to join us?",
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600),
                  )
                ],
              ),
            )),
        Step(
          state: _isStep2Valid ? StepState.complete : StepState.indexed,
          isActive: _currentStep >= 1,
          title: const Text("Personal"),
          content: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: GestureDetector(
                  onTap: _pickImage,
                  child: CircleAvatar(
                    radius: 50,
                    backgroundImage: _selectedImage != null
                        ? FileImage(_selectedImage!)
                        : null,
                    child: _selectedImage == null
                        ? const Icon(Icons.camera_alt,
                            size: 40, color: Colors.grey)
                        : null,
                  ),
                ),
              ),
              const SizedBox(height: 20),
              TextField(
                controller: _firstnameController,
                onChanged: (value) =>
                    setState(() => _isStep2Valid = validateStep2()),
                decoration: const InputDecoration(
                  labelText: "First Name",
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 15),
              TextField(
                controller: _lastnameController,
                onChanged: (value) =>
                    setState(() => _isStep2Valid = validateStep2()),
                decoration: const InputDecoration(
                  labelText: "Last Name",
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 15),
              TextField(
                controller: _phoneController,
                onChanged: (value) =>
                    setState(() => _isStep2Valid = validateStep2()),
                decoration: const InputDecoration(
                  labelText: "Phone Number",
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 15),
              DropdownButtonFormField<Gender>(
                value: _selectedGender,
                onChanged: (Gender? newValue) {
                  setState(() {
                    _selectedGender = newValue;
                    _isStep2Valid = validateStep2();
                  });
                },
                items: Gender.values.map((Gender gender) {
                  return DropdownMenuItem<Gender>(
                    value: gender,
                    child: Text(gender.value),
                  );
                }).toList(),
                decoration: const InputDecoration(
                  labelText: "Gender",
                  border: OutlineInputBorder(),
                ),
              ),
            ],
          ),
        ),
        Step(
          state: _isStep3Valid ? StepState.complete : StepState.indexed,
          isActive: _currentStep >= 2,
          title: const Text("Account"),
          content: Column(
            children: [
              const SizedBox(height: 15),
              TextField(
                controller: _emailController,
                onChanged: (value) =>
                    setState(() => _isStep3Valid = validateStep3()),
                decoration: const InputDecoration(
                  labelText: "Email@example.com",
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 15),
              TextField(
                controller: _passwordController,
                obscureText: _obscureText,
                onChanged: (value) =>
                    setState(() => _isStep3Valid = validateStep3()),
                decoration: InputDecoration(
                  labelText: "Password",
                  border: const OutlineInputBorder(),
                  suffixIcon: IconButton(
                    icon: Icon(
                        _obscureText ? Icons.visibility : Icons.visibility_off),
                    onPressed: () =>
                        setState(() => _obscureText = !_obscureText),
                  ),
                ),
              ),
              const SizedBox(height: 15),
              TextField(
                controller: _confirmPasswordController,
                obscureText: _obscureConfirmText,
                onChanged: (value) =>
                    setState(() => _isStep3Valid = validateStep3()),
                decoration: InputDecoration(
                  labelText: "Confirm Password",
                  border: const OutlineInputBorder(),
                  suffixIcon: IconButton(
                    icon: Icon(_obscureConfirmText
                        ? Icons.visibility
                        : Icons.visibility_off),
                    onPressed: () => setState(
                        () => _obscureConfirmText = !_obscureConfirmText),
                  ),
                ),
              ),
            ],
          ),
        ),
        Step(
          state: _isStep4Valid ? StepState.complete : StepState.indexed,
          isActive: _currentStep >= 3,
          title: const Text("Address"),
          content: Column(
            children: [
              const SizedBox(height: 15),
              ElevatedButton(
                onPressed: () async {
                  final selectedAddress = await showModalBottomSheet<String>(
                    context: context,
                    builder: (context) => const LocationPickerDialog(),
                  );
                  if (selectedAddress != null) {
                    _addressController.text = selectedAddress;
                    setState(() => _isStep4Valid = validateStep4());
                  }
                },
                child: const Text("Share Location"),
              ),
              if (_addressController.text.isNotEmpty)
                Padding(
                  padding: const EdgeInsets.only(top: 8.0),
                  child: Text(_addressController.text),
                ),
            ],
          ),
        ),
      ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        child: Theme(
          data: Theme.of(context).copyWith(
              colorScheme: Theme.of(context)
                  .colorScheme
                  .copyWith(onSurface: Colors.grey, primary: Colors.red[700])),
          child: Padding(
            padding: const EdgeInsets.only(top: 45, bottom: 20),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.start,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: const EdgeInsets.only(left: 20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        "Register",
                        style: GoogleFonts.poppins(
                          fontSize: 24,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        "Create a new account.",
                        style: GoogleFonts.poppins(
                            fontSize: 14, color: Colors.grey),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
                Stepper(
                  currentStep: _currentStep,
                  onStepContinue: () {
                    if (_currentStep < steps().length - 1) {
                      setState(() => _currentStep++);
                    } else {
                      _register();
                    }
                  },
                  onStepCancel: () {
                    if (_currentStep > 0) {
                      setState(() => _currentStep--);
                    }
                  },
                  onStepTapped: (step) => setState(() => _currentStep = step),
                  controlsBuilder: (context, details) => Padding(
                    padding: const EdgeInsets.only(top: 32),
                    child: Row(
                      children: [
                        if (isLastStep)
                          Expanded(
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.red[700],
                                minimumSize: const Size(double.infinity, 60),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(10),
                                ),
                              ),
                              onPressed: _isLoading ? null : _register,
                              child: const Text(
                                "Submit",
                                style: TextStyle(
                                    color: Colors.white, fontSize: 16),
                              ),
                            ),
                          ),
                        if (!isLastStep)
                          Expanded(
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.red[700],
                                minimumSize: const Size(double.infinity, 60),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(10),
                                ),
                              ),
                              onPressed: details.onStepContinue,
                              child: Text(
                                _currentStep == 0
                                    ? "Continue signing up!"
                                    : "Next",
                                style: const TextStyle(
                                    color: Colors.white, fontSize: 16),
                              ),
                            ),
                          ),
                        const SizedBox(width: 16),
                        if (!isFirstStep)
                          Expanded(
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.red[700],
                                minimumSize: const Size(double.infinity, 60),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(10),
                                ),
                              ),
                              onPressed: details.onStepCancel,
                              child: const Text(
                                "Back",
                                style: TextStyle(
                                    color: Colors.white, fontSize: 16),
                              ),
                            ),
                          ),
                      ],
                    ),
                  ),
                  steps: steps(),
                ),
                Padding(
                  padding: const EdgeInsets.only(left: 20, right: 20),
                  child: Column(
                    children: [
                      Center(
                        child: RichText(
                          textAlign: TextAlign.center,
                          text: TextSpan(
                            style: TextStyle(
                              fontSize: 13.3,
                              color: Colors.grey[700],
                            ),
                            children: [
                              const TextSpan(
                                  text: 'By signing up, you agree to our '),
                              TextSpan(
                                text: 'Terms & Conditions',
                                style: TextStyle(
                                  color: Colors.red[700],
                                ),
                              ),
                              const TextSpan(text: ' and '),
                              TextSpan(
                                text: 'Privacy Policy',
                                style: TextStyle(
                                  color: Colors.red[700],
                                ),
                              ),
                              const TextSpan(text: '.'),
                            ],
                          ),
                        ),
                      ),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Text("Already have an account?"),
                          TextButton(
                            onPressed: () {
                              Navigator.pushReplacementNamed(context, '/login');
                            },
                            child: Text("Login",
                                style: TextStyle(color: Colors.red[700])),
                          ),
                        ],
                      ),
                    ],
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
