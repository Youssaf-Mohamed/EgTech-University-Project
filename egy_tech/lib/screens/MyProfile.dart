// lib/screens/MyProfile.dart
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart'; // Riverpod import
import 'package:my_app/objects/User.dart';
import 'package:my_app/services/AuthService.dart';
import 'package:my_app/widgets/custom_bottom_nav.dart';
import 'package:my_app/widgets/profile_option_tile.dart';
import 'package:my_app/providers/providers.dart'; // Import your providers

class MyProfile extends ConsumerStatefulWidget {
  const MyProfile({Key? key}) : super(key: key);

  @override
  ConsumerState<MyProfile> createState() => _MyProfileState();
}

class _MyProfileState extends ConsumerState<MyProfile> {
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

  Future<void> _logout() async {
    // Use Riverpod to read the authRepositoryProvider.
    final authRepository = ref.read(authRepositoryProvider);
    await authRepository.logout();
    if (mounted) {
      Navigator.pushNamedAndRemoveUntil(context, "/login", (route) => false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Logged out successfully')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        backgroundColor: Colors.white,
        elevation: 0,
        title: const Text(
          'Profile',
          style: TextStyle(
            fontFamily: 'Satoshi',
            fontSize: 17,
            fontWeight: FontWeight.normal,
            color: Colors.black,
          ),
        ),
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.favorite_border, color: Colors.black),
            onPressed: () {},
          ),
          IconButton(
            icon: const Icon(Icons.shopping_bag_outlined, color: Colors.black),
            onPressed: () {},
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Text('Error: $_error'))
              : _user == null
                  ? const Center(child: Text('No user data found'))
                  : Container(
                      color: Colors.white,
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          children: [
                            Column(
                              children: [
                                Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      _user!.name,
                                      style: const TextStyle(
                                        fontFamily: 'Satoshi',
                                        fontSize: 23,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.black,
                                      ),
                                    ),
                                    CircleAvatar(
                                      radius: 40,
                                      backgroundImage: _user!.profilePicture != null
                                          ? NetworkImage(_user!.profilePicture)
                                          : const AssetImage('assets/images/default_profile.png')
                                              as ImageProvider,
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                Row(
                                  crossAxisAlignment: CrossAxisAlignment.center,
                                  children: [
                                    Text(
                                      'Logged in via ${_user!.email}',
                                      style: const TextStyle(
                                        fontFamily: 'Satoshi',
                                        fontSize: 11,
                                        fontWeight: FontWeight.normal,
                                        color: Colors.grey,
                                      ),
                                    ),
                                    const SizedBox(width: 5),
                                    const Flexible(
                                      child: Divider(
                                        color: Colors.grey,
                                        thickness: 0.5,
                                        height: 1,
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                            const SizedBox(height: 20),
                            Expanded(
                              child: SingleChildScrollView(
                                child: Column(
                                  children: [
                                    ProfileOptionTile(
                                      title: "My Orders",
                                      subtitle:
                                          "Check your order status (track, return, cancel, etc.)",
                                      icon: Icons.shopping_bag_outlined,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/orders");
                                      },
                                    ),
                                    const SizedBox(height: 10),
                                    ProfileOptionTile(
                                      title: "My Projects",
                                      subtitle:
                                          "Check your project status (customize, track, return, cancel, etc.)",
                                      icon: Icons.groups,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/projects");
                                      },
                                    ),
                                    ProfileOptionTile(
                                      title: "Wishlist",
                                      subtitle:
                                          "Buy or collaborate from items and makers saved in Wishlist",
                                      icon: Icons.favorite_border,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/Wishlist");
                                      },
                                    ),
                                    ProfileOptionTile(
                                      title: "Following",
                                      subtitle:
                                          "Browse through interesting Artisan & Designer Profiles",
                                      icon: Icons.person_add_alt,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/following");
                                      },
                                    ),
                                    ProfileOptionTile(
                                      title: "Collections",
                                      subtitle:
                                          "Save and refer your collections, crafts, and craftspeople",
                                      icon: Icons.bookmark_border,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/mycollection");
                                      },
                                    ),
                                    ProfileOptionTile(
                                      title: "Coupons",
                                      subtitle:
                                          "Browse coupons to get discounts on HandmadeHive",
                                      icon: Icons.discount_outlined,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/coupons");
                                      },
                                    ),
                                    ProfileOptionTile(
                                      title: "Wallet",
                                      subtitle:
                                          "Check your Handmade Hive wallet balance",
                                      icon: Icons.wallet,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/wallet");
                                      },
                                    ),
                                    ProfileOptionTile(
                                      title: "Help and Support",
                                      subtitle:
                                          "Get help for your account or orders",
                                      icon: Icons.help_outline,
                                      onTap: () {
                                        Navigator.pushNamed(context, "/help");
                                      },
                                    ),
                                    ProfileOptionTile(
                                      title: "Logout",
                                      subtitle: "Sign out from your account",
                                      icon: Icons.exit_to_app,
                                      onTap: () async {
                                        await _logout();
                                      },
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
      bottomNavigationBar: CustomBottomNavBar(
        selectedIndex: 4,
        onItemTapped: (index) {},
      ),
    );
  }
}
