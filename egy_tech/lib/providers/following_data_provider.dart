import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:my_app/repositories/following_repository.dart';
import 'package:my_app/models/FollowingData.dart';

final apiRepositoryProvider = Provider<FollowingRepository>((ref) => FollowingRepository());

final followingDataProvider = FutureProvider<ApiResponse>((ref) async {
  final repository = ref.watch(apiRepositoryProvider);
  return repository.fetchFollowingData();
});
