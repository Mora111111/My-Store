<?php
class AdminFinanceController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    private function resolveRange(): array {
        $range = $_GET['range'] ?? '7d';
        if (!in_array($range, ['today', '7d', '30d'], true)) {
            $range = '7d';
        }
        $end = date('Y-m-d H:i:s');
        if ($range === 'today') {
            $start = date('Y-m-d 00:00:00');
        } elseif ($range === '30d') {
            $start = date('Y-m-d H:i:s', strtotime('-30 days'));
        } else {
            $start = date('Y-m-d H:i:s', strtotime('-7 days'));
        }
        return [$range, $start, $end];
    }

    private function financeData(string $start, string $end): array {
        $orderModel = new Order();
        return [
            'kpis' => $orderModel->getRevenueSummary($start, $end),
            'daily' => $orderModel->getDailyRevenue($start, $end),
            'donut' => $orderModel->getStatusBreakdown($start, $end)
        ];
    }

    public function index(): void {
        [$range, $start, $end] = $this->resolveRange();
        $finance = $this->financeData($start, $end);
        $kpis = $finance['kpis'];
        $daily = $finance['daily'];
        $donut = $finance['donut'];
        $activeRange = $range;

        $showSearch = false;
        $pageIcon = 'fa-chart-line';
        $pageTitle = lang('finance_title');

        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/finance/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function data(): void {
        [$range, $start, $end] = $this->resolveRange();
        header('Content-Type: application/json');
        echo json_encode(array_merge(['range' => $range], $this->financeData($start, $end)));
        exit;
    }
}
