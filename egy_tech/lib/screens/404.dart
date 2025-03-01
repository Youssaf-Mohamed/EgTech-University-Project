import 'package:flutter/material.dart';

class NotFoundPage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
      body: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment
              .spaceBetween, // This will push the button to the bottom
          children: [
            Expanded(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Image.asset("assets/images/404.png"),
                  const Text(
                    'Your page didn’t respond',
                    style: TextStyle(fontSize: 24),
                  ),
                  const SizedBox(
                    height: 10,
                  ),
                  const Text(
                    'This page doesn’t exist or maybe fall asleep! We suggest you back to home',
                    style: TextStyle(
                      fontSize: 14,
                      color:
                          Colors.grey, // Change the color to your desired color
                    ),
                    textAlign: TextAlign
                        .center, // Center the text horizontally within the Text widget
                  ),
                ],
              ),
            ),
            ElevatedButton(
              onPressed: () {
                // Navigate back to the home page or any other page
                Navigator.pushReplacementNamed(context, '/');
              },
              style: ElevatedButton.styleFrom(
                backgroundColor:
                    Colors.red[700], // Set the background color to red
                minimumSize: const Size(
                    double.infinity, 60), // Full width and height of 50
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10), // Set border radius
                ),
              ),
              child: const Text(
                'Back Home',
                style: TextStyle(color: Colors.white),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
