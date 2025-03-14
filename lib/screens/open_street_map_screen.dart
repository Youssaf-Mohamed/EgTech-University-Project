import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:dio/dio.dart';

class OpenStreetMapScreen extends StatefulWidget {
  const OpenStreetMapScreen({super.key});

  @override
  _OpenStreetMapScreenState createState() => _OpenStreetMapScreenState();
}

class _OpenStreetMapScreenState extends State<OpenStreetMapScreen> {
  LatLng? _selectedPosition;
  final MapController _mapController = MapController();
  final Dio _dio = Dio();

  Future<String> _reverseGeocode(LatLng position) async {
    try {
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
      ].where((part) => part != null).join(', ');
    } catch (e) {
      return 'Unknown Location';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Select Location'),
        actions: [
          IconButton(
            icon: const Icon(Icons.check),
            onPressed: () async {
              if (_selectedPosition != null) {
                final address = await _reverseGeocode(_selectedPosition!);
                Navigator.pop(context, address);
              }
            },
          )
        ],
      ),
      body: FlutterMap(
        mapController: _mapController,
        options: MapOptions(
          center: const LatLng(0, 0),
          zoom: 2.0,
          onTap: (_, LatLng position) {
            setState(() => _selectedPosition = position);
          },
        ),
        children: [
          TileLayer(
            urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            userAgentPackageName: 'com.example.app',
          ),
          if (_selectedPosition != null)
            MarkerLayer(
              markers: [
                Marker(
                  point: _selectedPosition!,
                  child: const Icon(Icons.location_pin, size: 40),
                )
              ],
            )
        ],
      ),
    );
  }
}