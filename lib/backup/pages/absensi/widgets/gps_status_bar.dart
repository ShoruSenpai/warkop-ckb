import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_text_styles.dart';

/// Status validasi GPS real-time (section 3 pada HTML asli).
class GpsStatusBar extends StatelessWidget {
  final String label; // "Radius Outlet Valid (12m)"
  final bool isValid;
  final VoidCallback onRefresh;

  const GpsStatusBar({
    super.key,
    required this.label,
    required this.onRefresh,
    this.isValid = true,
  });

  @override
  Widget build(BuildContext context) {
    final bg = isValid ? AppColors.emerald50 : const Color(0xFFFFDAD6);
    final border = isValid ? AppColors.emerald200 : AppColors.error;
    final textColor = isValid ? AppColors.emerald950 : AppColors.error;

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: border.withOpacity(0.7)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: Row(
              children: [
                Icon(Icons.near_me, size: 18, color: AppColors.primary),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    label,
                    style: AppTextStyles.labelSm.copyWith(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: textColor,
                    ),
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ),
          TextButton(
            onPressed: onRefresh,
            style: TextButton.styleFrom(
              padding: EdgeInsets.zero,
              minimumSize: const Size(0, 0),
              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
            ),
            child: Text(
              'Refresh GPS',
              style: AppTextStyles.labelSm.copyWith(
                fontSize: 11,
                color: AppColors.primary,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
