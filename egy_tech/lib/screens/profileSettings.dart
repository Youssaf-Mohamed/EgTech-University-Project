// ignore_for_file: prefer_const_constructors, prefer_const_literals_to_create_immutables

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:my_app/providers/providers.dart';

class ProfileSettings extends ConsumerWidget {
  const ProfileSettings({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Profile Settings'),
      ),
      body: Column(
        children: [
          _buildOptionTile(
            context,
            title: 'Edit Profile',
            icon: Icons.person,
            onTap: () =>
                _openFullScreenDialog(context, const EditProfileScreen()),
          ),
          _buildOptionTile(
            context,
            title: 'Change Password',
            icon: Icons.lock,
            onTap: () =>
                _openFullScreenDialog(context, const ChangePasswordScreen()),
          ),
          _buildOptionTile(
            context,
            title: 'Theme',
            icon: Icons.color_lens,
            onTap: () => _showThemeBottomSheet(context),
          ),
          _buildOptionTile(
            context,
            title: 'Notifications',
            icon: Icons.notifications,
            onTap: () => _showSmallDialog(
                context, 'Notifications', 'Toggle notification settings here.'),
          ),
          _buildOptionTile(
            context,
            title: 'Privacy',
            icon: Icons.privacy_tip,
            onTap: () => _showSmallDialog(
                context, 'Privacy', 'Adjust privacy settings here.'),
          ),
          _buildOptionTile(
            context,
            title: 'Logout',
            icon: Icons.logout,
            color: Colors.red[700],
            onTap: () => _showLogoutConfirmation(context, ref),
          ),
        ],
      ),
    );
  }

  Widget _buildOptionTile(
    BuildContext context, {
    required String title,
    required IconData icon,
    required VoidCallback onTap,
    Color? color,
  }) {
    return ListTile(
      title: Text(title, style: TextStyle(fontWeight: FontWeight.w600)),
      leading: Icon(icon, color: color ?? Colors.black87),
      onTap: onTap,
    );
  }

  void _openFullScreenDialog(BuildContext context, Widget page) {
    Navigator.push(
      context,
      PageRouteBuilder(
        pageBuilder: (context, animation, secondaryAnimation) => page,
        transitionsBuilder: (context, animation, secondaryAnimation, child) {
          return FadeTransition(opacity: animation, child: child);
        },
      ),
    );
  }

  void _showSmallDialog(BuildContext context, String title, String content) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(title),
        content: Text(content),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context), child: Text('Close')),
        ],
      ),
    );
  }

  void _showThemeBottomSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('Select Theme',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            ListTile(
              title: Text('Light Mode'),
              leading: Icon(Icons.light_mode),
              onTap: () => Navigator.pop(context),
            ),
            ListTile(
              title: Text('Dark Mode'),
              leading: Icon(Icons.dark_mode),
              onTap: () => Navigator.pop(context),
            ),
          ],
        ),
      ),
    );
  }

  void _showLogoutConfirmation(BuildContext context, WidgetRef ref) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Logout'),
        content: Text('Are you sure you want to log out?'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context), child: Text('Cancel')),
          ElevatedButton(
            onPressed: () => _logout(context, ref),
            child: Text('Logout'),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red[700]),
          ),
        ],
      ),
    );
  }

  Future<void> _logout(BuildContext context, WidgetRef ref) async {
    final authRepository = ref.read(authRepositoryProvider);
    await authRepository.logout();

    if (context.mounted) {
      Navigator.pushNamedAndRemoveUntil(context, "/login", (route) => false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Logged out successfully')),
      );
    }
  }
}

class EditProfileScreen extends StatelessWidget {
  const EditProfileScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Edit Profile')),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(decoration: InputDecoration(labelText: 'Name')),
            TextField(decoration: InputDecoration(labelText: 'Email')),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {},
              child: Text('Save Changes'),
            ),
          ],
        ),
      ),
    );
  }
}

class ChangePasswordScreen extends StatelessWidget {
  const ChangePasswordScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Change Password')),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(
                obscureText: true,
                decoration: InputDecoration(labelText: 'Current Password')),
            TextField(
                obscureText: true,
                decoration: InputDecoration(labelText: 'New Password')),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: () {},
              child: Text('Update Password'),
            ),
          ],
        ),
      ),
    );
  }
}
