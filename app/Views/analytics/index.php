<div class="analytics-container">
    <h2>إحصائيات الزوار</h2>
    <p>إجمالي الزيارات: <?php echo $totalVisits; ?></p>
    
    <table border="1" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>IP Address</th>
                <th>المتصفح والجهاز</th>
                <th>وقت الزيارة</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visits as $visit): ?>
            <tr>
                <td><?php echo $visit['ip_address']; ?></td>
                <td><?php echo htmlspecialchars($visit['user_agent']); ?></td>
                <td><?php echo $visit['visited_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>