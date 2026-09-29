class ShiftItem {
  const ShiftItem({
    required this.name,
    required this.role,
    required this.day,
    required this.time,
    required this.status,
  });

  final String name;
  final String role;
  final String day;
  final String time;
  final String status;
}

class TransactionRecord {
  const TransactionRecord({
    required this.id,
    required this.total,
    required this.payment,
    required this.createdAt,
  });

  final String id;
  final int total;
  final String payment;
  final DateTime createdAt;
}

const outletName = 'Kos Bu Mirza pink';
const outletLatitude = -8.1565529;
const outletLongitude = 113.7189992;
const outletGeofenceRadiusMeters = 150.0;

class MenuItem {
  const MenuItem({
    required this.name,
    required this.category,
    required this.price,
    required this.variants,
  });

  final String name;
  final String category;
  final int price;
  final List<String> variants;
}

const shifts = [
  ShiftItem(
    name: 'Alisa Yasmin',
    role: 'Barista',
    day: 'Senin, 25 Sep',
    time: '08:00 - 16:00',
    status: 'Aktif',
  ),
  ShiftItem(
    name: 'Raka Pratama',
    role: 'Kasir',
    day: 'Senin, 25 Sep',
    time: '16:00 - 00:00',
    status: 'Terjadwal',
  ),
  ShiftItem(
    name: 'Dimas Saputra',
    role: 'Kasir',
    day: 'Selasa, 26 Sep',
    time: '08:00 - 16:00',
    status: 'Terjadwal',
  ),
  ShiftItem(
    name: 'Nadia Putri',
    role: 'Barista',
    day: 'Selasa, 26 Sep',
    time: '16:00 - 00:00',
    status: 'Terjadwal',
  ),
];

final List<TransactionRecord> completedTransactions = [];

const menuItems = [
  MenuItem(
    name: 'Kopi Susu Gula Aren',
    category: 'Kopi',
    price: 22000,
    variants: ['Normal', 'Less Ice', 'Hot'],
  ),
  MenuItem(
    name: 'Americano',
    category: 'Kopi',
    price: 18000,
    variants: ['Hot', 'Iced'],
  ),
  MenuItem(
    name: 'Matcha Latte',
    category: 'Non-kopi',
    price: 24000,
    variants: ['Normal', 'Less Sugar'],
  ),
  MenuItem(
    name: 'Croissant Butter',
    category: 'Makanan',
    price: 19000,
    variants: ['Original', 'Almond'],
  ),
  MenuItem(
    name: 'French Fries',
    category: 'Makanan',
    price: 20000,
    variants: ['Original', 'Cheese'],
  ),
];
