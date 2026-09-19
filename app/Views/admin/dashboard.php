<style>
/* ===== تصميم عصري وناعم للداش بورد ===== */
.stats-grid-beautiful {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.b-card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 24px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow:
        0 10px 30px -12px rgba(15, 23, 42, 0.12),
        0 4px 12px -6px rgba(15, 23, 42, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.b-card::before {
    content: "";
    position: absolute;
    inset-block: 0;
    inset-inline-start: 0;
    width: 6px;
    background: var(--card-accent, #6366f1);
    opacity: 0.9;
}

.b-card::after {
    content: "";
    position: absolute;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--card-accent-soft, rgba(99, 102, 241, 0.12));
    inset-block-start: -60px;
    inset-inline-start: -40px;
    opacity: 0.7;
    pointer-events: none;
}

.b-card:hover {
    transform: translateY(-6px);
    box-shadow:
        0 22px 40px -18px rgba(15, 23, 42, 0.22),
        0 8px 20px -10px rgba(15, 23, 42, 0.12);
    border-color: rgba(148, 163, 184, 0.45);
}

.b-icon-wrapper {
    position: relative;
    z-index: 1;
    width: 68px;
    height: 68px;
    border-radius: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 28px;
    flex-shrink: 0;
    color: var(--card-accent, #6366f1);
    background: var(--card-accent-soft, #eef2ff);
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.6),
        0 8px 18px -10px var(--card-accent, #6366f1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.b-card:hover .b-icon-wrapper {
    transform: scale(1.05) rotate(-2deg);
    box-shadow:
        inset 0 0 0 1px rgba(255, 255, 255, 0.8),
        0 12px 22px -10px var(--card-accent, #6366f1);
}

.b-info {
    position: relative;
    z-index: 1;
}

.b-info h3 {
    margin: 0 0 6px 0;
    font-size: 15px;
    color: #64748b;
    font-weight: 700;
    letter-spacing: 0.2px;
}

.b-info p {
    margin: 0;
    font-size: 32px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1;
    letter-spacing: -1px;
}

/* ألوان مخصصة لكل كارت */
.c-products {
    --card-accent: #0284c7;
    --card-accent-soft: #e0f2fe;
}

.c-categories {
    --card-accent: #7c3aed;
    --card-accent-soft: #ede9fe;
}

.c-orders {
    --card-accent: #ea580c;
    --card-accent-soft: #ffedd5;
}

.c-comments {
    --card-accent: #d97706;
    --card-accent-soft: #fef3c7;
}

.c-messages {
    --card-accent: #e11d48;
    --card-accent-soft: #ffe4e6;
}

.c-visitors {
    --card-accent: #16a34a;
    --card-accent-soft: #dcfce7;
}

/* ===== كارت الترحيب ===== */
.welcome-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    padding: 28px 30px;
    box-shadow: 0 18px 40px -24px rgba(15, 23, 42, 0.25);
    color: #334155;
    line-height: 1.9;
}

.welcome-card h2 {
    margin: 0 0 12px;
    font-size: 22px;
    font-weight: 900;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.welcome-card p {
    margin: 0 0 18px;
    color: #475569;
    font-size: 15px;
}

.welcome-card ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 10px;
}

.welcome-card li {
    position: relative;
    padding: 12px 16px;
    background: #f8fafc;
    border: 1px solid #eef2f7;
    border-radius: 14px;
    color: #334155;
    transition: background 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
}

.welcome-card li::before {
    content: "•";
    color: #6366f1;
    font-weight: 900;
    margin-inline-end: 8px;
}

.welcome-card li:hover {
    background: #ffffff;
    border-color: #dbeafe;
    transform: translateX(-3px);
}

/* ===== التجاوب مع الشاشات الصغيرة ===== */
@media (max-width: 640px) {
    .b-card {
        padding: 20px;
        gap: 16px;
        border-radius: 20px;
    }

    .b-icon-wrapper {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        font-size: 24px;
    }

    .b-info p {
        font-size: 28px;
    }

    .welcome-card {
        padding: 22px;
    }

    .welcome-card h2 {
        font-size: 20px;
    }
}
</style>