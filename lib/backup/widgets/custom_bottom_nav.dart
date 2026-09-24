import 'package:flutter/material.dart';
import '../core/constants/app_colors.dart';
import '../core/constants/app_text_styles.dart';

class NavItemData {
  final String label;
  final IconData icon;
  const NavItemData({required this.label, required this.icon});
}

/// Bottom navigation bar dengan 4 tab: Absensi, Jadwal, POS, Profil.
class CustomBottomNav extends StatelessWidget {
  static const items = [
    NavItemData(label: 'Absensi', icon: Icons.how_to_reg),
    NavItemData(label: 'Jadwal', icon: Icons.calendar_month),
    NavItemData(label: 'POS', icon: Icons.point_of_sale),
    NavItemData(label: 'Profil', icon: Icons.account_circle),
  ];

  final int currentIndex;
  final ValueChanged<int> onTap;

  const CustomBottomNav({super.key, required this.currentIndex, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      top: false,
      child: Container(
        height: 64,
        decoration: BoxDecoration(
          color: AppColors.surfaceContainerLowest.withOpacity(0.85),
          boxShadow: const [
            BoxShadow(color: Color(0x0A000000), blurRadius: 8, offset: Offset(0, -1)),
          ],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: List.generate(items.length, (i) {
            final selected = i == currentIndex;
            final color = selected ? AppColors.primary : AppColors.onSurfaceVariant;
            return InkWell(
              onTap: () => onTap(i),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(items[i].icon, size: 22, color: color),
                    const SizedBox(height: 2),
                    Text(
                      items[i].label,
                      style: AppTextStyles.labelSm.copyWith(
                        color: color,
                        fontWeight: selected ? FontWeight.w600 : FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
            );
          }),
        ),
      ),
    );
  }
}
