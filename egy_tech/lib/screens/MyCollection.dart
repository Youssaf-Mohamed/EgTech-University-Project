import 'package:flutter/material.dart';
import 'package:my_app/widgets/CollectionCard.dart';
import 'package:my_app/widgets/CollectionDetailScreen.dart';

class MyCollection extends StatefulWidget {
  const MyCollection({super.key});

  @override
  State<MyCollection> createState() => _MyCollectionState();
}

class _MyCollectionState extends State<MyCollection> {
  final List<Map<String, dynamic>> collections = [
    {
      'title': 'Festive fits',
      'images': [
        'https://i.ibb.co/Xrw0t5rT/1.png',
        'https://i.ibb.co/7dkJ9cfh/2.png',
        'https://i.ibb.co/yBgx76mz/3.png',
        'https://i.ibb.co/XZ1qWjV0/4.png',
        'https://i.ibb.co/Xrw0t5rT/1.png',
      ]
    },
    {
      'title': 'Eco Lights',
      'images': [
        'https://i.ibb.co/5xBc5fNF/5.png',
        'https://i.ibb.co/NgvBdbhY/6.png',
        'https://i.ibb.co/DHnvFp9s/7.png',
      ]
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: const Text(
          'My Collection',
          style: TextStyle(
            fontFamily: 'Satoshi',
            fontSize: 17,
            fontWeight: FontWeight.w500,
            color: Colors.black,
          ),
        ),
        centerTitle: true,
      ),
      body: Container(
        height: MediaQuery.of(context).size.height,
        color: Colors.white,
        padding: const EdgeInsets.all(16.0),
        child: GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
            childAspectRatio: 1,
          ),
          itemCount: collections.length,
          itemBuilder: (context, index) {
            return GestureDetector(
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => CollectionDetailScreen(
                      collection: collections[index],
                    ),
                  ),
                );
              },
              child: MouseRegion(
                cursor: SystemMouseCursors.click,
                child: CollectionCard(collection: collections[index]),
              ),
            );
          },
        ),
      ),
    );
  }
}
