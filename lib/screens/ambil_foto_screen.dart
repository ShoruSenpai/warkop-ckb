import 'dart:convert';
import 'dart:typed_data';

import 'package:camera/camera.dart';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';

import '../services/app_data.dart';
import '../services/app_session.dart';
import '../services/api_service.dart';
import '../theme/stitch_theme.dart';
import 'absensi_sukses_screen.dart';

class AmbilFotoScreen extends StatefulWidget {
  const AmbilFotoScreen({
    super.key,
    required this.latitude,
    required this.longitude,
  });

  final double latitude;
  final double longitude;

  @override
  State<AmbilFotoScreen> createState() => _AmbilFotoScreenState();
}

class _AmbilFotoScreenState extends State<AmbilFotoScreen>
    with WidgetsBindingObserver {
  CameraController? _cameraController;
  bool _cameraReady = false;
  bool _initializingCamera = false;
  bool _isLoading = false;
  bool _useFrontCamera = true;
  XFile? _capturedPhoto;
  String? _cameraError;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _initCamera();
  }

  Future<void> _initCamera() async {
    if (_initializingCamera || _cameraReady) return;
    _initializingCamera = true;
    try {
      final cameras = await availableCameras();
      if (cameras.isEmpty) {
        throw Exception('Kamera tidak tersedia di perangkat ini.');
      }
      final desiredDirection = _useFrontCamera
          ? CameraLensDirection.front
          : CameraLensDirection.back;
      final selectedCamera =
          cameras
              .where((camera) => camera.lensDirection == desiredDirection)
              .firstOrNull ??
          cameras.first;
      final controller = CameraController(
        selectedCamera,
        ResolutionPreset.high,
        enableAudio: false,
      );
      await controller.initialize();
      if (!mounted) {
        await controller.dispose();
        return;
      }
      setState(() {
        _cameraController = controller;
        _cameraReady = true;
        _cameraError = null;
      });
    } catch (error) {
      if (mounted) {
        setState(
          () => _cameraError = error.toString().replaceFirst('Exception: ', ''),
        );
      }
    } finally {
      _initializingCamera = false;
    }
  }

  Future<void> _switchCamera() async {
    if (_isLoading || _initializingCamera) return;
    final oldController = _cameraController;
    setState(() {
      _useFrontCamera = !_useFrontCamera;
      _cameraReady = false;
      _capturedPhoto = null;
      _cameraError = null;
    });
    _cameraController = null;
    await oldController?.dispose();
    await _initCamera();
  }

  Future<void> _capturePhoto() async {
    final controller = _cameraController;
    if (!_cameraReady || controller == null || _isLoading) return;
    setState(() => _isLoading = true);
    try {
      final photo = await controller.takePicture();
      if (mounted) setState(() => _capturedPhoto = photo);
    } catch (error) {
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Foto gagal diambil: $error')));
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _submitPhoto() async {
    final photo = _capturedPhoto;
    final user = AppSession.instance.user;
    if (photo == null || user == null || _isLoading) return;
    setState(() => _isLoading = true);

    try {
      await ApiService.kirimAbsensi(
        karyawanId: user.id,
        tipeAbsen: 'Masuk',
        latitude: widget.latitude,
        longitude: widget.longitude,
        fotoBase64: base64Encode(await photo.readAsBytes()),
      );
      if (!mounted) return;
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (_) => AbsensiSuksesScreen(
            latitude: widget.latitude,
            longitude: widget.longitude,
            waktu: DateTime.now(),
          ),
        ),
      );
    } catch (error) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Absensi belum terkirim: $error')),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    final controller = _cameraController;
    if (state == AppLifecycleState.inactive ||
        state == AppLifecycleState.paused) {
      if (controller != null) {
        controller.dispose();
        _cameraController = null;
        if (mounted) {
          setState(() {
            _cameraReady = false;
            _capturedPhoto = null;
          });
        }
      }
    } else if (state == AppLifecycleState.resumed) {
      _initCamera();
    }
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _cameraController?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: StitchTheme.surfaceWhite,
        elevation: 0,
        iconTheme: const IconThemeData(color: StitchTheme.textDark),
        title: const Text(
          'Cak Kebo  ·  Absen',
          style: TextStyle(
            color: StitchTheme.textDark,
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
        actions: const [
          Padding(
            padding: EdgeInsets.only(right: 16),
            child: CircleAvatar(
              radius: 19,
              backgroundColor: StitchTheme.primaryGreen,
              child: Icon(Icons.person_rounded, color: Colors.white),
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 10, 20, 18),
          child: Column(
            children: [
              Row(
                children: [
                  const CircleAvatar(
                    radius: 4,
                    backgroundColor: StitchTheme.primaryGreen,
                  ),
                  const SizedBox(width: 8),
                  const Expanded(
                    child: Text(
                      'Verifikasi Foto (3/3)',
                      style: TextStyle(
                        color: StitchTheme.textMuted,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  IconButton(
                    onPressed: () => Navigator.pop(context),
                    tooltip: 'Tutup',
                    icon: const Icon(Icons.close_rounded),
                  ),
                ],
              ),
              const SizedBox(height: 4),
              const LinearProgressIndicator(
                value: 1,
                minHeight: 4,
                color: StitchTheme.primaryGreen,
                backgroundColor: StitchTheme.borderSubtle,
              ),
              const SizedBox(height: 16),
              Text(
                _capturedPhoto == null
                    ? 'Ambil Foto Kehadiran'
                    : 'Periksa Foto Anda',
                style: const TextStyle(
                  fontSize: 21,
                  fontWeight: FontWeight.bold,
                  color: StitchTheme.textDark,
                ),
              ),
              const SizedBox(height: 5),
              Text(
                _capturedPhoto == null
                    ? 'Posisikan wajah Anda tepat di dalam bingkai.'
                    : 'Pastikan wajah terlihat jelas sebelum mengirim.',
                style: const TextStyle(color: StitchTheme.textMuted),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 14),
              Expanded(
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(24),
                  child: Container(
                    width: double.infinity,
                    color: StitchTheme.textDark,
                    child: Stack(
                      fit: StackFit.expand,
                      alignment: Alignment.center,
                      children: [
                        if (_capturedPhoto != null)
                          FutureBuilder<Uint8List>(
                            future: _capturedPhoto!.readAsBytes(),
                            builder: (context, snapshot) => snapshot.hasData
                                ? Image.memory(
                                    snapshot.data!,
                                    fit: BoxFit.cover,
                                  )
                                : const Center(
                                    child: CircularProgressIndicator(
                                      color: Colors.white,
                                    ),
                                  ),
                          )
                        else if (_cameraReady && _cameraController != null)
                          CameraPreview(_cameraController!)
                        else
                          Center(
                            child: _cameraError != null
                                ? Padding(
                                    padding: const EdgeInsets.all(24),
                                    child: Text(
                                      _cameraError!,
                                      textAlign: TextAlign.center,
                                      style: const TextStyle(
                                        color: Colors.white,
                                      ),
                                    ),
                                  )
                                : const CircularProgressIndicator(
                                    color: Colors.white,
                                  ),
                          ),
                        if (_capturedPhoto == null && _cameraReady)
                          Center(
                            child: Container(
                              width: 190,
                              height: 240,
                              decoration: BoxDecoration(
                                border: Border.all(
                                  color: Colors.white.withValues(alpha: 0.75),
                                  width: 2,
                                ),
                                borderRadius: BorderRadius.circular(100),
                              ),
                              child: const Align(
                                alignment: Alignment.bottomCenter,
                                child: Padding(
                                  padding: EdgeInsets.only(bottom: 16),
                                  child: Text(
                                    'Posisikan Wajah',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        Positioned(
                          top: 16,
                          left: 16,
                          right: 16,
                          child: Row(
                            children: [
                              const CircleAvatar(
                                radius: 4,
                                backgroundColor: Color(0xFF21B989),
                              ),
                              const SizedBox(width: 8),
                              Text(
                                _capturedPhoto == null
                                    ? (_cameraReady
                                          ? 'Kamera aktif'
                                          : 'Menyiapkan kamera')
                                    : 'Foto berhasil diambil',
                                style: const TextStyle(color: Colors.white),
                              ),
                              const Spacer(),
                              const Icon(
                                Icons.location_on_outlined,
                                size: 17,
                                color: Colors.white,
                              ),
                              const SizedBox(width: 4),
                              Text(
                                '${Geolocator.distanceBetween(outletLatitude, outletLongitude, widget.latitude, widget.longitude).round()} m',
                                style: const TextStyle(color: Colors.white),
                              ),
                            ],
                          ),
                        ),
                        if (_cameraError != null && !_cameraReady)
                          Positioned(
                            bottom: 16,
                            child: TextButton.icon(
                              onPressed: _initCamera,
                              icon: const Icon(Icons.refresh_rounded),
                              label: const Text('Coba Lagi'),
                            ),
                          ),
                      ],
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 12),
              const Text(
                'Foto hanya digunakan untuk verifikasi kehadiran.',
                textAlign: TextAlign.center,
                style: TextStyle(color: StitchTheme.textMuted, fontSize: 13),
              ),
              const SizedBox(height: 12),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  IconButton.filledTonal(
                    onPressed: _isLoading ? null : _switchCamera,
                    tooltip: 'Ganti kamera',
                    icon: const Icon(Icons.flip_camera_android_rounded),
                  ),
                  SizedBox(
                    width: 76,
                    height: 76,
                    child: IconButton.filled(
                      onPressed:
                          _isLoading || !_cameraReady || _capturedPhoto != null
                          ? null
                          : _capturePhoto,
                      style: IconButton.styleFrom(
                        backgroundColor: StitchTheme.primaryGreen,
                        foregroundColor: Colors.white,
                        shape: const CircleBorder(),
                        side: const BorderSide(
                          color: StitchTheme.primaryGreen,
                          width: 5,
                        ),
                      ),
                      icon: _isLoading
                          ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: Colors.white,
                              ),
                            )
                          : const Icon(Icons.camera_alt_rounded, size: 30),
                    ),
                  ),
                  IconButton.filledTonal(
                    onPressed: _capturedPhoto == null || _isLoading
                        ? null
                        : () => setState(() => _capturedPhoto = null),
                    tooltip: 'Ulangi foto',
                    icon: const Icon(Icons.replay_rounded),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              SizedBox(
                width: double.infinity,
                height: 54,
                child: ElevatedButton(
                  onPressed: _isLoading || _capturedPhoto == null
                      ? null
                      : _submitPhoto,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: StitchTheme.primaryGreen,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(15),
                    ),
                  ),
                  child: _isLoading
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Text(
                          'Kirim Absensi',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                ),
              ),
              TextButton.icon(
                onPressed: () => Navigator.pop(context),
                icon: const Icon(Icons.arrow_back_rounded),
                label: const Text('Batal'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
