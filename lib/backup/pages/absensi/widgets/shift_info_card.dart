import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_text_styles.dart';

/// Kartu ringkas jadwal shift hari ini (section 2 pada HTML asli).
class ShiftInfoCard extends StatelessWidget {
  final String shiftLabel; // "Pagi • 8 Jam"
  final String timeRange; // "08:00 - 16:00"
  final String station; // "Bar Station Utama"
  final String toleranceLabel; // "Toleransi s/d 08:15"

  const ShiftInfoCard({
    super.key,
    required this.shiftLabel,
    required this.timeRange,
    required this.station,
    required this.toleranceLabel,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.surfaceContainerLowest,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.outlineVariant),
        boxShadow: const [
          BoxShadow(color: Color(0x0A000000), blurRadius: 4, offset: Offset(0, 1)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'SHIFT HARI INI',
                style: AppTextStyles.labelSm.copyWith(letterSpacing: 0.6),
              ),
              _Badge(text: shiftLabel),
            ],
          ),
          const SizedBox(height: 4),
          Row(
            crossAxisAlignment: CrossAxisAlignment.baseline,
            textBaseline: TextBaseline.alphabetic,
            children: [
              Text(timeRange, style: AppTextStyles.headlineLg),
              const SizedBox(width: 6),
              Text('WIB', style: AppTextStyles.labelSm),
            ],
          ),
          const SizedBox(height: 8),
          const Divider(height: 1, color: AppColors.outlineVariant),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Row(
                  children: [
                    const Icon(Icons.storefront, size: 17, color: AppColors.onSurfaceVariant),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        station,
                        style: AppTextStyles.bodySm.copyWith(fontSize: 13),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ),
              Text(
                toleranceLabel,
                style: AppTextStyles.bodySm.copyWith(
                  fontSize: 12,
                  color: AppColors.amber800,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _Badge extends StatelessWidget {
  final String text;
  const _Badge({required this.text});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(
        color: AppColors.emerald50,
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: AppColors.emerald200),
      ),
      child: Text(
        text,
        style: AppTextStyles.labelSm.copyWith(fontSize: 11, color: AppColors.emerald800),
      ),
    );
  }
}
