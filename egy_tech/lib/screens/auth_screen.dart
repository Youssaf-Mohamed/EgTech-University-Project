import 'package:flutter/material.dart';
import 'package:my_app/objects/User.dart';
import 'package:my_app/services/AuthService.dart';

class UserScreen extends StatefulWidget {
  @override
  _UserScreenState createState() => _UserScreenState();
}

class _UserScreenState extends State<UserScreen> {
  User? _user;
  bool _isLoading = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _fetchUserData();
  }

  Future<void> _fetchUserData() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final authService = AuthService();
      final user = await authService.fetchUser();
      setState(() {
        _user = user;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
      });
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('User Profile'),
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Text('Error: $_error'))
              : _user == null
                  ? Center(child: Text('No user data found'))
                  : Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (_user!.profilePicture != null)
                            CircleAvatar(
                              backgroundImage: NetworkImage(_user!.profilePicture!),
                              radius: 50,
                            ),
                          SizedBox(height: 16),
                          Text('ID: ${_user!.id}'),
                          Text('Name: ${_user!.name}'),
                          Text('Email: ${_user!.email}'),
                        ],
                      ),
                    ),
    );
  }
}