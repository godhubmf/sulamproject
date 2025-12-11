<?php
/**
 * Dashboard Controller
 * Handles dashboard view for admin and regular users
 */

require_once __DIR__ . '/../../../shared/controllers/BaseController.php';

class DashboardController extends BaseController {
    
    public function showAdminDashboard() {
        $this->requireAdmin();
        
        $username = $_SESSION['username'] ?? 'Admin';
        
        $pageHeader = [
            'title' => 'Dashboard',
            'subtitle' => 'Hi, ' . $username . ' (Admin)',
            'breadcrumb' => [
                ['label' => 'Home', 'url' => null]
            ]
        ];
        
        // Fetch Statistics
        global $mysqli;
        if (!$mysqli) {
            require_once __DIR__ . '/../../../shared/lib/database/mysqli-db.php';
        }
        
        // 1. Total Residents (Using Users table - non-admin users)
        $resQuery = $mysqli->query("SELECT COUNT(*) as count FROM users WHERE roles != 'admin'");
        $totalResidents = $resQuery ? $resQuery->fetch_assoc()['count'] : 0;

        // 2. Active Donations (count of active donation campaigns)
        $donationsQuery = $mysqli->query("SELECT COUNT(*) as count FROM donations WHERE is_active = 1");
        $activeDonations = $donationsQuery ? $donationsQuery->fetch_assoc()['count'] : 0;
        
        // 3. Active Events This Month
        $currentMonth = date('Y-m');
        $eventsQuery = $mysqli->query("
            SELECT COUNT(*) as count 
            FROM events 
            WHERE is_active = 1 
            AND DATE_FORMAT(event_date, '%Y-%m') = '$currentMonth'
        ");
        $activeEvents = $eventsQuery ? $eventsQuery->fetch_assoc()['count'] : 0;
        
        // 4. Current Total Balance (Cash + Bank) - same calculation as Cash Book
        $currentYear = (int)date('Y');
        
        // Get opening balances
        $settingsQuery = $mysqli->query("SELECT opening_cash_balance, opening_bank_balance FROM financial_settings WHERE fiscal_year = $currentYear LIMIT 1");
        $settings = $settingsQuery ? $settingsQuery->fetch_assoc() : null;
        $openingCash = (float)($settings['opening_cash_balance'] ?? 0);
        $openingBank = (float)($settings['opening_bank_balance'] ?? 0);
        
        // Calculate total deposits (IN)
        $depositSumClause = "COALESCE(geran_kerajaan, 0) + COALESCE(sumbangan_derma, 0) + COALESCE(tabung_masjid, 0) + COALESCE(kutipan_jumaat_sadak, 0) + COALESCE(kutipan_aidilfitri_aidiladha, 0) + COALESCE(sewa_peralatan_masjid, 0) + COALESCE(hibah_faedah_bank, 0) + COALESCE(faedah_simpanan_tetap, 0) + COALESCE(sewa_rumah_kedai_tadika_menara, 0) + COALESCE(lain_lain_terimaan, 0)";
        
        $depositQuery = $mysqli->query("
            SELECT 
                SUM(CASE WHEN payment_method = 'cash' THEN ($depositSumClause) ELSE 0 END) as cash_deposits,
                SUM(CASE WHEN payment_method = 'bank' THEN ($depositSumClause) ELSE 0 END) as bank_deposits
            FROM financial_deposit_accounts
            WHERE YEAR(tx_date) = $currentYear
        ");
        $deposits = $depositQuery ? $depositQuery->fetch_assoc() : ['cash_deposits' => 0, 'bank_deposits' => 0];
        
        // Calculate total payments (OUT)
        $paymentSumClause = "COALESCE(perayaan_islam, 0) + COALESCE(pengimarahan_aktiviti_masjid, 0) + COALESCE(penyelenggaraan_masjid, 0) + COALESCE(keperluan_kelengkapan_masjid, 0) + COALESCE(gaji_upah_saguhati_elaun, 0) + COALESCE(sumbangan_derma, 0) + COALESCE(mesyuarat_jamuan, 0) + COALESCE(utiliti, 0) + COALESCE(alat_tulis_percetakan, 0) + COALESCE(pengangkutan_perjalanan, 0) + COALESCE(caj_bank, 0) + COALESCE(lain_lain_perbelanjaan, 0)";
        
        $paymentQuery = $mysqli->query("
            SELECT 
                SUM(CASE WHEN payment_method = 'cash' THEN ($paymentSumClause) ELSE 0 END) as cash_payments,
                SUM(CASE WHEN payment_method = 'bank' THEN ($paymentSumClause) ELSE 0 END) as bank_payments
            FROM financial_payment_accounts
            WHERE YEAR(tx_date) = $currentYear
        ");
        $payments = $paymentQuery ? $paymentQuery->fetch_assoc() : ['cash_payments' => 0, 'bank_payments' => 0];
        
        // Calculate current balances
        $currentCashBalance = $openingCash + (float)($deposits['cash_deposits'] ?? 0) - (float)($payments['cash_payments'] ?? 0);
        $currentBankBalance = $openingBank + (float)($deposits['bank_deposits'] ?? 0) - (float)($payments['bank_payments'] ?? 0);
        $totalBalance = $currentCashBalance + $currentBankBalance;

        // 5. Fetch Prayer Times from Aladhan API
        $prayerTimes = [];
        $hijriDate = '';
        try {
            $apiUrl = "https://api.aladhan.com/v1/timingsByCity?city=Kota%20Samarahan&country=Malaysia&method=3";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5 // 5 second timeout
                ]
            ]);
            $response = @file_get_contents($apiUrl, false, $context);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data['code']) && $data['code'] == 200 && isset($data['data']['timings'])) {
                    $timings = $data['data']['timings'];
                    $prayerTimes = [
                        'Subuh' => $timings['Fajr'] ?? '-',
                        'Syuruk' => $timings['Sunrise'] ?? '-',
                        'Zohor' => $timings['Dhuhr'] ?? '-',
                        'Asar' => $timings['Asr'] ?? '-',
                        'Maghrib' => $timings['Maghrib'] ?? '-',
                        'Isyak' => $timings['Isha'] ?? '-'
                    ];
                    
                    // Extract Hijri date and translate to Malay
                    if (isset($data['data']['date']['hijri'])) {
                        $hijri = $data['data']['date']['hijri'];
                        
                        // Hijri month translation to Malay (by month number)
                        $hijriMonthsMalay = [
                            1 => 'Muharram',
                            2 => 'Safar',
                            3 => 'Rabiulawal',
                            4 => 'Rabiulakhir',
                            5 => 'Jamadilawal',
                            6 => 'Jamadilakhir',
                            7 => 'Rejab',
                            8 => 'Syaaban',
                            9 => 'Ramadan',
                            10 => 'Syawal',
                            11 => 'Zulkaedah',
                            12 => 'Zulhijjah'
                        ];
                        
                        $monthNumber = (int)($hijri['month']['number'] ?? 0);
                        $monthMalay = $hijriMonthsMalay[$monthNumber] ?? '';
                        
                        $hijriDate = $hijri['day'] . ' ' . $monthMalay . ' ' . $hijri['year'];
                    }
                }
            }
        } catch (Exception $e) {
            // Silently fail - prayer times will be empty
        }

        ob_start();
        include __DIR__ . '/../views/admin-overview.php';
        $content = ob_get_clean();
        
        ob_start();
        include __DIR__ . '/../../../shared/components/layouts/app-layout.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Dashboard';
        $additionalStyles = [
            url('features/shared/assets/css/bento-grid.css'),
            url('features/dashboard/admin/assets/admin-dashboard.css')
        ];
        include __DIR__ . '/../../../shared/components/layouts/base.php';
    }
    
    public function showUserDashboard() {
        $this->requireAuth();
        
        $username = $_SESSION['username'] ?? 'User';
        
        $pageHeader = [
            'title' => 'Dashboard',
            'subtitle' => 'Hi, ' . $username,
            'breadcrumb' => [
                ['label' => 'Home', 'url' => null]
            ]
        ];

        ob_start();
        include __DIR__ . '/../../user/views/user-overview.php';
        $content = ob_get_clean();
        
        ob_start();
        include __DIR__ . '/../../../shared/components/layouts/app-layout.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Dashboard';
        $additionalStyles = [url('features/dashboard/user/assets/user-dashboard.css')];
        include __DIR__ . '/../../../shared/components/layouts/base.php';
    }
}
