<?php
$payMethod = in_array($order['payment_method'] ?? 'cod', ['online', 'online_card', 'online_wallet']) 
    ? 'إلكتروني (بطاقة بنكية / محفظة)' 
    : 'الدفع نقداً عند الاستلام (COD)';
$payStatus = ($order['payment_status'] ?? 'pending') === 'paid' ? 'مدفوع' : 'معلق';
?>

<style>
  .invoice-wrapper {
    padding: 120px 20px 80px;
    min-height: 80vh;
    background: #f1f5f9;
  }
  .invoice-card {
    max-width: 900px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    overflow: hidden;
  }
  .invoice-header {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    padding: 30px 40px;
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .invoice-title h2 {
    margin: 0 0 5px 0;
    font-size: 26px;
    color: #38bdf8;
  }
  .invoice-title span {
    font-size: 14px;
    color: #94a3b8;
  }
  .invoice-actions {
    display: flex;
    gap: 12px;
  }
  .btn-print {
    background: #38bdf8;
    color: #0f172a;
    border: none;
    padding: 10px 22px;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    text-decoration: none;
  }
  .btn-print:hover {
    background: #0284c7;
    color: #ffffff;
  }
  .btn-back {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 10px 20px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .btn-back:hover {
    background: rgba(255, 255, 255, 0.2);
  }
  .invoice-body {
    padding: 35px 40px;
  }
  .invoice-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }
  .info-box {
    background: #f8fafc;
    border-radius: 12px;
    padding: 18px;
    border: 1px solid #e2e8f0;
  }
  .info-box h4 {
    margin: 0 0 12px 0;
    font-size: 15px;
    color: #0f172a;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .info-box p {
    margin: 6px 0;
    font-size: 14px;
    color: #475569;
  }
  .info-box strong {
    color: #1e293b;
  }
  .invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
  }
  .invoice-table th {
    background: #f8fafc;
    color: #475569;
    padding: 14px;
    font-size: 14px;
    border-bottom: 2px solid #e2e8f0;
    text-align: right;
  }
  .invoice-table td {
    padding: 16px 14px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
    vertical-align: middle;
  }
  .invoice-total-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px 25px;
    border: 2px dashed #cbd5e1;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .invoice-total-card span {
    font-size: 18px;
    font-weight: 700;
    color: #334155;
  }
  .invoice-total-card strong {
    font-size: 26px;
    font-weight: 900;
    color: #059669;
  }

  @media print {
    body {
      background: #ffffff !important;
    }
    .safe-header,
    .footer,
    .invoice-actions,
    .no-print {
      display: none !important;
    }
    .invoice-wrapper {
      padding: 0 !important;
      background: #ffffff !important;
    }
    .invoice-card {
      border: none !important;
      box-shadow: none !important;
      max-width: 100% !important;
    }
    .invoice-header {
      background: #0f172a !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
  }
</style>

<div class="invoice-wrapper">
  <div class="invoice-card">
    <div class="invoice-header">
      <div class="invoice-title">
        <h2>فاتورة شراء رسمية #<?= $order['id'] ?></h2>
        <span>تاريخ الإصدار: <?= date('Y-m-d h:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="invoice-actions no-print">
        <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> طباعة الفاتورة</button>
        <a href="/my-orders" class="btn-back"><i class="fa-solid fa-arrow-right"></i> العودة لطلباتي</a>
      </div>
    </div>

    <div class="invoice-body">
      <div class="invoice-grid">
        <div class="info-box">
          <h4><i class="fa-solid fa-user" style="color:#0ea5e9;"></i> بيانات العميل</h4>
          <p><strong>الاسم:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
          <p><strong>الهاتف:</strong> <span style="direction:ltr; display:inline-block;"><?= htmlspecialchars($order['phone']) ?></span></p>
          <p><strong>حالة الطلب:</strong> <?= htmlspecialchars($order['status']) ?></p>
        </div>

        <div class="info-box">
          <h4><i class="fa-solid fa-truck-fast" style="color:#ec4899;"></i> عنوان التوصيل</h4>
          <p><strong>المنطقة:</strong> <?= htmlspecialchars($order['governorate']) ?> - <?= htmlspecialchars($order['city']) ?></p>
          <p><strong>العنوان 1:</strong> <?= htmlspecialchars($order['address_line1']) ?></p>
          <p><strong>العنوان 2:</strong> <?= htmlspecialchars($order['address_line2'] ?: 'لا يوجد') ?></p>
          <p><strong>الرمز البريدي:</strong> <?= htmlspecialchars($order['zip_code'] ?: 'لا يوجد') ?></p>
        </div>

        <div class="info-box">
          <h4><i class="fa-solid fa-credit-card" style="color:#f59e0b;"></i> تفاصيل السداد</h4>
          <p><strong>طريقة الدفع:</strong> <?= $payMethod ?></p>
          <p><strong>حالة الدفع:</strong> <?= $payStatus ?></p>
          <p><strong>رقم العملية:</strong> <?= htmlspecialchars($order['transaction_id'] ?: '---') ?></p>
        </div>
      </div>

      <table class="invoice-table">
        <thead>
          <tr>
            <th>المنتج</th>
            <th>سعر الوحدة</th>
            <th style="text-align:center;">الكمية</th>
            <th style="text-align:left;">المجموع الفرعي</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($products)): foreach ($products as $item): 
            $title = $item['title'] ?? 'منتج غير معروف';
            $qty = (int)($item['quantity'] ?? $item['number'] ?? 1);
            $cleanPrice = (float)str_replace([',', 'ج.م', ' '], '', $item['price'] ?? 0);
            $subtotal = $cleanPrice * $qty;
            $rawSrc = $item['src'] ?? 'images/logos/logo.png';
            $imgUrl = str_starts_with($rawSrc, 'http') ? $rawSrc : BASE_URL . ltrim($rawSrc, '/');
          ?>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:12px;">
                <img src="<?= $imgUrl ?>" width="45" height="45" style="border-radius:8px; object-fit:contain; border:1px solid #e2e8f0; background:#fff;">
                <span style="font-weight:700; color:#0f172a;"><?= htmlspecialchars($title) ?></span>
              </div>
            </td>
            <td><?= number_format($cleanPrice, 2) ?> ج.م</td>
            <td style="text-align:center; font-weight:700;"><?= $qty ?></td>
            <td style="text-align:left; font-weight:800; color:#0f172a;"><?= number_format($subtotal, 2) ?> ج.م</td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>

      <div class="invoice-total-card">
        <span>المبلغ الإجمالي المستحق:</span>
        <strong><?= number_format((float)$order['total_price'], 2) ?> ج.م</strong>
      </div>
    </div>
  </div>
</div>
