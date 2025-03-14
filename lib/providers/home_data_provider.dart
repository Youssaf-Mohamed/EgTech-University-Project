import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:my_app/repositories/home_repository.dart';
import 'package:my_app/models/Homedata.dart';

final apiRepositoryProvider = Provider<HomeRepository>((ref) => HomeRepository());

final homeDataProvider = FutureProvider<ApiResponse>((ref) async {
  final repository = ref.watch(apiRepositoryProvider);
  return repository.fetchHomeData();
});
