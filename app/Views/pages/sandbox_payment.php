<div class="container" style="padding: 150px 20px 80px; min-height: 80vh; display: flex; justify-content: center; align-items: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 30px; text-align: center; border: 1px solid #e2e8f0;">
        <div style="margin-bottom: 20px;">
            <i class="fa-solid fa-shield-halved" style="font-size: 40px; color: #10b981;"></i>
            <h2 style="margin: 15px 0 5px; color: #0f172a; font-size: 22px;">بوابة الدفع الآمنة (وضع الاختبار)</h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">هذه الصفحة تحاكي بوابة الدفع الفعلية لعرض إمكانيات المنصة.</p>
        </div>

        <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 25px; text-align: right; border: 1px dashed #cbd5e1;">
            <p style="margin: 0 0 10px; color: #334155;"><strong>رقم الطلب:</strong> #<?php echo $order['id']; ?></p>
            <p style="margin: 0 0 10px; color: #334155;"><strong>العميل:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p style="margin: 0; color: #334155; font-size: 18px;"><strong>المبلغ المطلوب:</strong> <span style="color: #059669; font-weight: 900;"><?php echo htmlspecialchars($order['total_price']); ?> ج.م</span></p>
        </div>

        <form action="/payment/process-sandbox" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            
            <div style="margin-bottom: 20px; text-align: right;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #475569;">رقم البطاقة (للاختبار فقط)</label>
                <input type="text" value="4242 4242 4242 4242" readonly style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f1f5f9; color: #94a3b8; font-family: monospace; font-size: 16px; text-align: left; box-sizing: border-box;" dir="ltr">
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: #0f172a; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 10px;" onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#0f172a'">
                <i class="fa-solid fa-lock"></i> دفع <?php echo htmlspecialchars($order['total_price']); ?> ج.م الآن
            </button>
        </form>
        
        <p style="margin-top: 20px; font-size: 12px; color: #94a3b8;"><i class="fa-solid fa-info-circle"></i> عند شراء المنصة، يتم استبدال هذه الواجهة بنافذة الدفع الحقيقية (Iframe) الخاصة بشركة الدفع.</p>
    </div>
</div>