import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:my_app/repositories/auth_repository.dart';

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository();
});

final userLoggedInProvider = FutureProvider<bool>((ref) async {
  final authRepo = ref.watch(authRepositoryProvider);
  return await authRepo.isUserLoggedIn();
});

/// An example StateProvider to hold authentication state (logged in or not).
final authStateProvider = StateProvider<bool>((ref) => false);
