import 'package:flutter/material.dart';

class CustomListView extends StatelessWidget {
  final List<Map<String, String>> list;

  const CustomListView({super.key, required this.list});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 150,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        physics: const BouncingScrollPhysics(),
        padding: EdgeInsets.symmetric(vertical: 5),
        itemCount: list.length * 2,
        itemBuilder: (context, index) {
          final data = list[index % list.length];
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8),
            child: Container(
              width: 130,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(10),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.1),
                    spreadRadius: 1,
                    blurRadius: 6,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(6),
                    child: Image.network(
                      data["image"]!,
                      width: 120,
                      height: 100,
                      fit: BoxFit.cover,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    data["name"]!.length > 18
                        ? "${data["name"]!.substring(0, 18)}..." 
                        : data["name"]!,
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w500,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
