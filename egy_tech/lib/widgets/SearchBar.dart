import 'package:flutter/material.dart';

class SearchBarWidget extends StatelessWidget {
  const SearchBarWidget({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 36,
      decoration: BoxDecoration(
        color: Colors.grey[200], // Background color similar to image
        borderRadius: BorderRadius.circular(8),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 10),
      child: Row(
        children: [
          const Icon(Icons.search, color: Colors.black),
          const SizedBox(width: 8),
          Expanded(
            child: Center(
              // Center the TextField vertically
              child: TextField(
                decoration: const InputDecoration(
                  hintText: "Search for crafts, products.....",
                  hintStyle: TextStyle(color: Colors.grey),
                  border: InputBorder.none,
                  isCollapsed:
                      true, // Ensures the height is as minimal as possible
                ),
                style: const TextStyle(color: Colors.black),
              ),
            ),
          ),
          IconButton(
            icon: const Icon(Icons.tune, color: Colors.red),
            onPressed: () {},
          ),
        ],
      ),
    );
  }
}
