import 'package:flutter/material.dart';

class DummyData {
  // Categories Data
  static final List<Map<String, dynamic>> categories = [
    {'id': '1', 'name': 'Artisan'},
    {'id': '2', 'name': 'Designer'},
    {'id': '3', 'name': 'Craft'},
    {'id': '4', 'name': 'Painter'},
    {'id': '5', 'name': 'Sculptor'},
  ];

  // Collaboration Content
  static final Map<String, dynamic> collaborateContent = {
    'title': 'Custom Designed Products',
    'description': 'Any Idea/ Design can be executed from our exquisite range...',
    'orderInfo': 'Our Order quantity varies from...',
  };

  // Bottom Navigation Items
  static final List<Map<String, dynamic>> navItems = [
    {'icon': Icons.home, 'label': 'Home'},
    {'icon': Icons.category, 'label': 'Category'},
    {'icon': Icons.widgets, 'label': 'Sludo'},
    {'icon': Icons.group, 'label': 'Collaborate'},
    {'icon': Icons.person, 'label': 'Profile'},
  ];
}