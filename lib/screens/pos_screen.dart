import 'package:flutter/material.dart';

import '../services/app_data.dart';
import '../services/app_session.dart';
import '../theme/stitch_theme.dart';

class CartLine {
  CartLine(this.item, this.variant);

  final MenuItem item;
  final String variant;
  int quantity = 1;
  int get total => item.price * quantity;
}

class PosScreen extends StatefulWidget {
  const PosScreen({super.key});

  @override
  State<PosScreen> createState() => _PosScreenState();
}

class _PosScreenState extends State<PosScreen> {
  final List<CartLine> _cart = [];
  String _category = 'Semua';
  double _discount = 0;
  String _payment = 'Tunai';

  int get _subtotal => _cart.fold(0, (sum, line) => sum + line.total);
  int get _discountValue => (_subtotal * _discount).round();
  int get _grandTotal => _subtotal - _discountValue;

  Future<void> _addItem(MenuItem item) async {
    final variant = await showDialog<String>(
      context: context,
      builder: (context) => SimpleDialog(
        title: Text(item.name),
        children: item.variants
            .map(
              (value) => SimpleDialogOption(
                onPressed: () => Navigator.pop(context, value),
                child: Text(value),
              ),
            )
            .toList(),
      ),
    );
    if (variant == null) return;

    setState(() {
      CartLine? existing;
      for (final line in _cart) {
        if (line.item.name == item.name && line.variant == variant) {
          existing = line;
          break;
        }
      }
      if (existing != null) {
        existing.quantity++;
      } else {
        _cart.add(CartLine(item, variant));
      }
    });
  }

  void _finishTransaction() {
    if (_cart.isEmpty) return;
    final transaction = TransactionRecord(
      id: 'TRX-${DateTime.now().millisecondsSinceEpoch}',
      total: _grandTotal,
      payment: _payment,
      createdAt: DateTime.now(),
    );
    completedTransactions.add(transaction);
    showDialog<void>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Transaksi selesai'),
        content: Text(
          'No. ${transaction.id}\nTotal ${formatCurrency(transaction.total)}\nPembayaran: ${transaction.payment}\nKasir: ${AppSession.instance.user?.name ?? '-'}\n\nStruk siap dicetak.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Tutup'),
          ),
          FilledButton.icon(
            onPressed: () {
              Navigator.pop(context);
              setState(() => _cart.clear());
            },
            icon: const Icon(Icons.print_outlined),
            label: const Text('Cetak struk'),
          ),
        ],
      ),
    );
  }

  Widget _buildProductsPanel() {
    final categories = [
      'Semua',
      ...menuItems.map((item) => item.category).toSet(),
    ];
    final visibleItems = _category == 'Semua'
        ? menuItems
        : menuItems.where((item) => item.category == _category).toList();

    return Padding(
      padding: const EdgeInsets.all(14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: categories
                  .map(
                    (category) => Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(category),
                        selected: _category == category,
                        onSelected: (_) => setState(() => _category = category),
                      ),
                    ),
                  )
                  .toList(),
            ),
          ),
          const SizedBox(height: 10),
          Expanded(
            child: LayoutBuilder(
              builder: (context, constraints) {
                final columns = (constraints.maxWidth / 170).floor().clamp(
                  2,
                  5,
                );
                return GridView.builder(
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: columns,
                    childAspectRatio: 1.35,
                    crossAxisSpacing: 10,
                    mainAxisSpacing: 10,
                  ),
                  itemCount: visibleItems.length,
                  itemBuilder: (context, index) {
                    final item = visibleItems[index];
                    return Card(
                      child: InkWell(
                        onTap: () => _addItem(item),
                        borderRadius: BorderRadius.circular(16),
                        child: Padding(
                          padding: const EdgeInsets.all(12),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                item.category.toUpperCase(),
                                style: const TextStyle(
                                  fontSize: 10,
                                  color: StitchTheme.textMuted,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              Text(
                                item.name,
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              Text(
                                formatCurrency(item.price),
                                style: const TextStyle(
                                  color: StitchTheme.primaryGreen,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCartPanel({required bool compact, required bool wide}) {
    final spacing = compact ? 6.0 : 10.0;
    final paymentPicker = DropdownButtonFormField<String>(
      initialValue: _payment,
      isExpanded: true,
      decoration: InputDecoration(
        labelText: compact ? null : 'Metode pembayaran',
        contentPadding: EdgeInsets.symmetric(
          horizontal: 10,
          vertical: compact ? 8 : 12,
        ),
      ),
      items: const [
        DropdownMenuItem(value: 'Tunai', child: Text('Tunai')),
        DropdownMenuItem(value: 'QRIS', child: Text('QRIS')),
      ],
      onChanged: (value) => setState(() => _payment = value ?? 'Tunai'),
    );
    final payButton = FilledButton.icon(
      onPressed: _cart.isEmpty ? null : _finishTransaction,
      icon: const Icon(Icons.receipt_long_outlined),
      label: Text(compact ? 'Bayar' : 'Bayar & Cetak Struk'),
    );

    return Container(
      color: StitchTheme.surfaceWhite,
      padding: EdgeInsets.all(compact ? 10 : 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Expanded(
                child: Text(
                  'Pesanan',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
              ),
              Text(
                '${_cart.length} item',
                style: const TextStyle(color: StitchTheme.textMuted),
              ),
            ],
          ),
          SizedBox(height: spacing),
          Expanded(
            child: _cart.isEmpty
                ? const Center(
                    child: Text(
                      'Pilih menu untuk memulai transaksi',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: StitchTheme.textMuted),
                    ),
                  )
                : ListView.builder(
                    itemCount: _cart.length,
                    itemBuilder: (context, index) {
                      final line = _cart[index];
                      return ListTile(
                        dense: compact,
                        contentPadding: EdgeInsets.zero,
                        title: Text(
                          line.item.name,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        subtitle: Text('${line.variant} • ${line.quantity}x'),
                        trailing: Text(formatCurrency(line.total)),
                      );
                    },
                  ),
          ),
          const Divider(height: 1),
          SizedBox(height: spacing),
          Row(
            children: [
              const Expanded(child: Text('Diskon')),
              DropdownButton<double>(
                value: _discount,
                items: const [
                  DropdownMenuItem(value: 0, child: Text('0%')),
                  DropdownMenuItem(value: .1, child: Text('10%')),
                  DropdownMenuItem(value: .2, child: Text('20%')),
                ],
                onChanged: (value) => setState(() => _discount = value ?? 0),
              ),
            ],
          ),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Total',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
              Text(
                formatCurrency(_grandTotal),
                style: const TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: StitchTheme.primaryGreen,
                ),
              ),
            ],
          ),
          SizedBox(height: spacing),
          if (compact && wide)
            Row(
              children: [
                Expanded(child: paymentPicker),
                const SizedBox(width: 8),
                payButton,
              ],
            )
          else ...[
            paymentPicker,
            SizedBox(height: spacing),
            SizedBox(width: double.infinity, child: payButton),
          ],
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('POS Kasir'),
        actions: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Center(child: Text('${_cart.length} item')),
          ),
        ],
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final wide = constraints.maxWidth >= 760;
          final compact = constraints.maxHeight < 480;
          if (wide) {
            return Row(
              children: [
                Expanded(flex: 6, child: _buildProductsPanel()),
                const VerticalDivider(width: 1),
                Expanded(
                  flex: 4,
                  child: _buildCartPanel(compact: compact, wide: true),
                ),
              ],
            );
          }
          return Column(
            children: [
              Expanded(flex: 5, child: _buildProductsPanel()),
              const Divider(height: 1),
              Expanded(
                flex: 6,
                child: _buildCartPanel(compact: compact, wide: false),
              ),
            ],
          );
        },
      ),
    );
  }
}

String formatCurrency(int value) =>
    'Rp ${value.toString().replaceAllMapped(RegExp(r'(?=(\d{3})+(?!\d))'), (match) => '.')}';
