import 'package:flutter/material.dart';

class CustomCardList extends StatelessWidget {
  final String imageUrl;
  final String title;
  final String price;
  final String vendorImageUrl;
  final String vendorName;
  final String location;
  final int rating;
  final String discountText;

  const CustomCardList({
    super.key,
    required this.imageUrl,
    required this.title,
    required this.price,
    required this.vendorImageUrl,
    required this.vendorName,
    required this.location,
    required this.rating,
    required this.discountText,
  });

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
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
          child: Row(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: Image.network(
                  imageUrl,
                  height: 115,
                  width: 115,
                  fit: BoxFit.cover,
                ),
              ),
              const SizedBox(width: 20),
              Column(
                mainAxisAlignment: MainAxisAlignment.start,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontFamily: 'Satoshi',
                      fontSize: 13.3,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    "\$${price}",
                    style: const TextStyle(color: Colors.grey, fontSize: 11.1),
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 13,
                        backgroundImage: NetworkImage(vendorImageUrl),
                      ),
                      const SizedBox(width: 7),
                      Text(
                        vendorName,
                        style: const TextStyle(color: Colors.grey, fontSize: 11.1),
                      ),
                    ],
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      const Icon(
                        Icons.location_on_outlined,
                        size: 17,
                        color: Colors.grey,
                      ),
                      const SizedBox(width: 2),
                      Text(
                        location,
                        style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                      ),
                    ],
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      for (int i = 0; i < rating; i++)
                        const Icon(color: Colors.amber, size: 12, Icons.star),
                      for (int i = 0; i < (5 - rating); i++)
                        const Icon(color: Colors.amber, size: 12, Icons.star_border),
                    ],
                  ),
                ],
              ),
            ],
          ),
        ),
        Positioned(
          top: 20,
          right: 0,
          child: Container(
            width: 65,
            height: 20,
            decoration: const BoxDecoration(
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(100),
                bottomLeft: Radius.circular(100),
              ),
              color: Colors.red,
            ),
            child: Center(
              child: Text(
                discountText,
                style: const TextStyle(
                  color: Colors.white,
                  fontFamily: 'Satoshi',
                  fontSize: 11,
                ),
              ),
            ),
          ),
        ),
      ],
    );
  }
}
