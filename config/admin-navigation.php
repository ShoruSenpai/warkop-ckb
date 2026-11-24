<?php

return [
    "groups" => [
        [
            "label" => "MENU UTAMA",

            "items" => [
                [
                    "label" => "Dashboard",
                    "route" => "dashboard.index",
                    "active" => ["dashboard.index"],
                    "icon" => "fas fa-chart-pie",
                    "roles" => ["owner", "admin"],
                ],

                [
                    "label" => "Manajemen Produk",
                    "route" => "products.index",
                    "active" => ["products.*"],
                    "icon" => "fas fa-box-open",
                    "roles" => ["owner", "admin"],

                    // Breadcrumb untuk halaman turunan.
                    "pages" => [
                        "products.create" => "Tambah Produk",
                        "products.edit" => "Edit Produk",
                    ],
                ],

                [
                    "label" => "Bahan Baku",
                    "route" => "raw-material.index",
                    "active" => ["raw-material.*"],
                    "icon" => "fas fa-cubes",
                    "roles" => ["owner", "admin"],
                ],

                [
                    "label" => "Pembelian",
                    "route" => "supplier.index",
                    "active" => ["supplier.*"],
                    "icon" => "fas fa-truck-loading",
                    "roles" => ["owner", "admin"],
                ],
            ],
        ],

        [
            "label" => "LAPORAN",
            //            "icon" => "fas fa-file-invoice-dollar",
            //            "roles" => ["owner", "admin"],

            "items" => [
                //                [
                //                    "label" => "Riwayat Transaksi",
                //                    "route" => "reports.transactions",
                //                    "active" => ["reports.transactions*"],
                //                    "icon" => "fas fa-file-invoice-dollar",
                //                ],

                [
                    "label" => "Laporan Penjualan",
                    "route" => "reports.sales.index",
                    "active" => ["reports.sales*"],
                    "icon" => "fas fa-chart-line",
                ],

                [
                    "label" => "Laporan Pembelian",
                    "route" => "reports.purchases.index",
                    "active" => ["reports.purchases*"],
                    "icon" => "fas fa-wallet",
                ],

                [
                    "label" => "Laporan Presensi",
                    "route" => "reports.attendance.index",
                    "active" => ["reports.attendance*"],
                    "icon" => "fas fa-file-invoice-dollar",
                ],
            ],
        ],

        [
            "label" => "SYSTEM",

            "items" => [
                [
                    "label" => "User & Karyawan",
                    "route" => "users.index",
                    "active" => ["users"],
                    "icon" => "fas fa-users-cog",
                    "roles" => ["owner"],
                ],
                [
                    "label" => "Pengaturan",
                    "route" => "settings.index",
                    "active" => ["settings.*"],
                    "icon" => "fas fa-cog",
                    "roles" => ["owner", "admin"],
                ],
            ],
        ],

        [
            "label" => "TESTING",

            "items" => [
                [
                    "label" => "Testing 503",
                    "route" => "testing.index",
                    "active" => ["testing.*"],
                    "icon" => "fas fa-road-barrier",
                    "roles" => ["owner", "admin"],
                ],
            ],
        ],
    ],
];
