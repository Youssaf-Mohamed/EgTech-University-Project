import 'package:flutter/material.dart';
import 'package:my_app/widgets/IconWithBadge.dart';

class Mainappbar extends StatefulWidget {
  const Mainappbar({super.key});

  @override
  State<Mainappbar> createState() => _MainappbarState();
}

class _MainappbarState extends State<Mainappbar> {
  @override
  Widget build(BuildContext context) {
    return Container(
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Image.asset(
            "assets/icons/IconLogo.png",
            height: 30,
          ),
          Container(
            width: 100,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                IconWithBadge(
                    icon: Icons.notifications_outlined,
                    Iconsize: 24,
                    badgeCount: 2),
                Icon(Icons.favorite_border, size: 24, color: Colors.black),
                IconWithBadge(
                    icon: Icons.shopping_bag_outlined,
                    Iconsize: 24,
                    badgeCount: 5),
              ],
            ),
          )
        ],
      ),
    );
  }
}
