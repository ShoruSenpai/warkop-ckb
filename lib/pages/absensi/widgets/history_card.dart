import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_text_styles.dart';

class AttendanceLogItem {
  final String dateLabel; // "Kemarin (23 Okt)"
  final String timeRange; // "07:55 - 16:05 WIB"
  final String statusLabel; // "Tepat Waktu"

  const AttendanceLogItem({
    required this.dateLabel,
    required this.timeRange,
    required this.statusLabel,
  });
}

/// Riwayat kehadiran singkat (section 6 pada HTML asli).
class HistoryCard extends StatelessWidget {
  final List<AttendanceLogItem> items;
  final VoidCallback onSeeAll;

  const HistoryCard({super.key, required this.items, required this.onSeeAll});

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
              Text('Riwayat Terakhir', style: AppTextStyles.labelMd),
              GestureDetector(
                onTap: onSeeAll,
                child: Text(
                  'Lihat Semua',
                  style: AppTextStyles.labelSm.copyWith(
                    color: AppColors.primary,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          const Divider(height: 1, color: AppColors.outlineVariant),
          for (final item in items) _HistoryRow(item: item),
        ],
      ),
    );
  }
}

class _HistoryRow extends StatelessWidget {
  final AttendanceLogItem item;
  const _HistoryRow({required this.item});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 8),
      decoration: const BoxDecoration(
        border: Border(bottom: BorderSide(color: AppColors.outlineVariant)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(item.dateLabel, style: AppTextStyles.labelSm.copyWith(color: AppColors.onSurface)),
              Text(item.timeRange, style: AppTextStyles.bodySm.copyWith(fontSize: 12)),
            ],
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
            decoration: BoxDecoration(
              color: AppColors.emerald50,
              borderRadius: BorderRadius.circular(4),
              border: Border.all(color: AppColors.emerald200),
            ),
            child: Text(
              item.statusLabel,
              style: AppTextStyles.labelSm.copyWith(fontSize: 11, color: AppColors.emerald800),
            ),
          ),
        ],
      ),
    );
  }
}
