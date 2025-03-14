import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart';
import 'package:latlong2/latlong.dart';
import 'package:my_app/screens/open_street_map_screen.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:geocoding/geocoding.dart';

class LocationPickerDialog extends StatefulWidget {
  const LocationPickerDialog({super.key});

  @override
  State<LocationPickerDialog> createState() => _LocationPickerDialogState();
}

class _LocationPickerDialogState extends State<LocationPickerDialog> {
  final Dio _dio = Dio();

  Future<String> _getCurrentLocation() async {
    try {
      Position position = await Geolocator.getCurrentPosition(
          desiredAccuracy: LocationAccuracy.high);
      return await _reverseGeocode(
          LatLng(position.latitude, position.longitude));
    } catch (e) {
      print("Error getting location: $e");
      return "Unknown location";
    }
  }

  Future<String> _reverseGeocode(LatLng position) async {
    final response = await _dio.get(
      'https://nominatim.openstreetmap.org/reverse',
      queryParameters: {
        'format': 'json',
        'lat': position.latitude,
        'lon': position.longitude,
        'zoom': 18,
        'addressdetails': 1,
      },
    );
    final address = response.data['address'];
    return [
      address,
      // address['road'],
      // address['village'] ?? address['town'],
      // address['country']
    ]
        .where((part) => part != null)
        .join(', '); // Ensure a String is always returned
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text("Select Location",
              style: Theme.of(context).textTheme.titleMedium),
          const SizedBox(height: 20),
          ElevatedButton.icon(
            icon: const Icon(Icons.my_location),
            label: const Text("Use Current Location"),
            onPressed: () async {
              final status = await Permission.location.request();
              if (status.isGranted) {
                final address = await _getCurrentLocation();
                if (address != null) Navigator.pop(context, address);
              } else {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
                    content: Text("Location permission denied")));
              }
            },
          ),
          const SizedBox(height: 10),
          // In LocationPickerDialog widget
          ElevatedButton.icon(
            icon: const Icon(Icons.map),
            label: const Text("Choose on Map"),
            onPressed: () async {
              final selectedAddress = await Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const OpenStreetMapScreen(),
                ),
              );
              if (selectedAddress != null) {
                Navigator.pop(context, selectedAddress);
              }
            },
          ),
          const SizedBox(height: 20),
        ],
      ),
    );
  }
}
