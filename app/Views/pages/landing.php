<?php
$currentLang = $_SESSION['lang'] ?? 'ar';
$pageDir = ($currentLang === 'en') ? 'ltr' : 'rtl';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $pageDir ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تسوّق الأجهزة الذكية بثقة وأمان</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.lp-root {
  --lp-blue: #2563eb;
  --lp-blue-600: #1d4ed8;
  --lp-blue-400: #60a5fa;
  --lp-blue-100: #dbeafe;
  --lp-blue-50: #eff6ff;
  --lp-ink: #0f172a;
  --lp-text: #334155;
  --lp-muted: #64748b;
  --lp-line: #e2e8f0;
  --lp-bg: #f8fafc;
  --lp-white: #ffffff;
  --lp-green: #16a34a;
  --lp-amber: #f59e0b;
  --lp-radius: 20px;
  --lp-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.04);
  --lp-shadow-md: 0 10px 30px rgba(37, 99, 235, 0.10), 0 2px 6px rgba(15, 23, 42, 0.05);
  --lp-shadow-lg: 0 30px 60px rgba(37, 99, 235, 0.18), 0 8px 20px rgba(15, 23, 42, 0.08);
  --lp-flip: 1;
  font-family: "Cairo", "Inter", system-ui, -apple-system, "Segoe UI", Tahoma, sans-serif;
  color: var(--lp-text);
  background: var(--lp-white);
  line-height: 1.7;
  overflow-x: clip;
  -webkit-font-smoothing: antialiased;
  text-align: start;
}
:where([dir="rtl"]) .lp-root { --lp-flip: -1; }
.lp-root *, .lp-root *::before, .lp-root *::after { box-sizing: border-box; }
.lp-root h1, .lp-root h2, .lp-root h3, .lp-root p, .lp-root ul { margin: 0; padding: 0; }
.lp-root ul { list-style: none; }
.lp-root a { color: inherit; text-decoration: none; }
.lp-root svg { display: block; flex-shrink: 0; }

.lp-container {
  width: 100%;
  max-inline-size: 1200px;
  margin-inline: auto;
  padding-inline: clamp(16px, 4vw, 32px);
}
.lp-section { padding-block: clamp(64px, 9vw, 120px); position: relative; }
.lp-section--alt { background: var(--lp-bg); }

.lp-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding-block: 6px;
  padding-inline: 14px;
  border-radius: 999px;
  background: var(--lp-blue-50);
  color: var(--lp-blue-600);
  font-weight: 700;
  font-size: 0.85rem;
  border: 1px solid var(--lp-blue-100);
}
.lp-eyebrow-dot {
  inline-size: 8px;
  block-size: 8px;
  border-radius: 50%;
  background: var(--lp-blue);
  box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.6);
  animation: lp-dot 2s infinite;
}
.lp-head { max-inline-size: 680px; margin-inline: auto; text-align: center; margin-block-end: clamp(40px, 6vw, 64px); }
.lp-title {
  font-size: clamp(1.75rem, 4vw, 2.75rem);
  line-height: 1.3;
  font-weight: 800;
  color: var(--lp-ink);
  margin-block: 16px;
  letter-spacing: -0.01em;
}
.lp-title em { font-style: normal; color: var(--lp-blue); }
.lp-lead { font-size: clamp(1rem, 1.6vw, 1.15rem); color: var(--lp-muted); }

.lp-btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding-block: 14px;
  padding-inline: 28px;
  border-radius: 14px;
  border: 1px solid transparent;
  font-family: inherit;
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease, color 0.25s ease;
  isolation: isolate;
}
.lp-btn svg { transition: transform 0.25s ease; transform: scaleX(var(--lp-flip)); }
.lp-btn:hover svg { transform: scaleX(var(--lp-flip)) translateX(4px); }
.lp-btn:active { transform: translateY(1px) scale(0.98); }
.lp-btn--primary {
  background: linear-gradient(135deg, var(--lp-blue) 0%, var(--lp-blue-600) 100%);
  color: var(--lp-white);
  box-shadow: 0 10px 24px rgba(37, 99, 235, 0.35);
}
.lp-btn--primary:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(37, 99, 235, 0.45); }
.lp-btn--pulse::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  z-index: -1;
  animation: lp-pulse 2.2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
.lp-btn--ghost {
  background: var(--lp-white);
  color: var(--lp-ink);
  border-color: var(--lp-line);
  box-shadow: var(--lp-shadow-sm);
}
.lp-btn--ghost:hover { transform: translateY(-3px); border-color: var(--lp-blue-400); color: var(--lp-blue-600); }
.lp-btn--white {
  background: var(--lp-white);
  color: var(--lp-blue-600);
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.25);
}
.lp-btn--white:hover { transform: translateY(-3px); }
.lp-btn--lg { padding-block: 18px; padding-inline: 40px; font-size: 1.2rem; border-radius: 16px; }
.lp-btn--sm { padding-block: 10px; padding-inline: 20px; font-size: 0.95rem; border-radius: 12px; }

.lp-header {
  position: sticky;
  inset-block-start: 0;
  z-index: 50;
  background: rgba(255, 255, 255, 0.72);
  backdrop-filter: saturate(180%) blur(14px);
  -webkit-backdrop-filter: saturate(180%) blur(14px);
  border-block-end: 1px solid transparent;
  transition: border-color 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}
.lp-header.lp-scrolled { border-block-end-color: var(--lp-line); box-shadow: 0 6px 24px rgba(15, 23, 42, 0.05); background: rgba(255, 255, 255, 0.9); }
.lp-header-inner { display: flex; align-items: center; justify-content: space-between; gap: 24px; block-size: 72px; }
.lp-brand { display: inline-flex; align-items: center; gap: 10px; font-weight: 900; font-size: 1.35rem; color: var(--lp-ink); }
.lp-brand-mark {
  inline-size: 38px;
  block-size: 38px;
  border-radius: 11px;
  background: linear-gradient(135deg, var(--lp-blue), var(--lp-blue-600));
  display: grid;
  place-items: center;
  color: var(--lp-white);
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
}
.lp-nav { display: flex; align-items: center; gap: 6px; }
.lp-nav a {
  padding-block: 8px;
  padding-inline: 14px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--lp-text);
  transition: background 0.2s ease, color 0.2s ease;
}
.lp-nav a:hover { background: var(--lp-blue-50); color: var(--lp-blue-600); }

.lp-hero {
  position: relative;
  padding-block-start: clamp(48px, 7vw, 96px);
  padding-block-end: clamp(64px, 9vw, 120px);
  background:
    radial-gradient(900px 500px at 85% -10%, rgba(37, 99, 235, 0.14), transparent 60%),
    radial-gradient(700px 420px at 0% 30%, rgba(96, 165, 250, 0.16), transparent 60%),
    var(--lp-white);
  overflow: hidden;
}
.lp-hero::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(var(--lp-line) 1px, transparent 1px),
    linear-gradient(90deg, var(--lp-line) 1px, transparent 1px);
  background-size: 56px 56px;
  opacity: 0.45;
  mask-image: radial-gradient(ellipse at 50% 30%, #000 20%, transparent 72%);
  -webkit-mask-image: radial-gradient(ellipse at 50% 30%, #000 20%, transparent 72%);
  pointer-events: none;
}
.lp-hero-grid {
  position: relative;
  display: grid;
  grid-template-columns: 1.05fr 1fr;
  align-items: center;
  gap: clamp(32px, 5vw, 72px);
}
.lp-hero-title {
  font-size: clamp(2.2rem, 5.4vw, 4rem);
  line-height: 1.2;
  font-weight: 900;
  color: var(--lp-ink);
  letter-spacing: -0.02em;
  margin-block: 20px;
}
.lp-hero-title span {
  background: linear-gradient(135deg, var(--lp-blue) 0%, var(--lp-blue-400) 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.lp-hero-text { font-size: clamp(1.02rem, 1.7vw, 1.2rem); color: var(--lp-muted); max-inline-size: 560px; }
.lp-hero-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-block-start: 32px; }
.lp-hero-stats {
  display: flex;
  flex-wrap: wrap;
  gap: clamp(20px, 4vw, 44px);
  margin-block-start: 44px;
  padding-block-start: 28px;
  border-block-start: 1px solid var(--lp-line);
}
.lp-stat strong { display: block; font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; color: var(--lp-ink); line-height: 1.1; font-family: "Inter", "Cairo", sans-serif; }
.lp-stat span { font-size: 0.9rem; color: var(--lp-muted); font-weight: 600; }

.lp-stage { position: relative; block-size: clamp(380px, 46vw, 560px); }
.lp-stage-glow {
  position: absolute;
  inset: 8% 6%;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.22), transparent 65%);
  filter: blur(30px);
  animation: lp-breathe 6s ease-in-out infinite;
}
.lp-float { position: absolute; animation: lp-float 7s ease-in-out infinite; }
.lp-float--b { animation-duration: 9s; animation-delay: -2s; }
.lp-float--c { animation-duration: 8s; animation-delay: -4s; }
.lp-float--d { animation-duration: 6s; animation-delay: -1s; }

.lp-laptop { inset-inline-start: 2%; inset-block-start: 14%; inline-size: 68%; }
.lp-laptop-screen {
  aspect-ratio: 16 / 10;
  border-radius: 16px 16px 6px 6px;
  background: var(--lp-ink);
  padding: 10px;
  box-shadow: var(--lp-shadow-lg);
  border: 1px solid #1e293b;
}
.lp-laptop-display {
  inline-size: 100%;
  block-size: 100%;
  border-radius: 8px;
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 40%, #93c5fd 100%);
  position: relative;
  overflow: hidden;
  padding: 14px;
  display: grid;
  grid-template-rows: auto 1fr;
  gap: 10px;
}
.lp-bar { block-size: 8px; border-radius: 99px; background: rgba(255, 255, 255, 0.85); }
.lp-bar--s { inline-size: 40%; }
.lp-bar--m { inline-size: 65%; }
.lp-tiles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.lp-tiles i { border-radius: 8px; background: rgba(255, 255, 255, 0.75); display: block; min-block-size: 36px; }
.lp-tiles i:nth-child(2) { background: var(--lp-blue); }
.lp-laptop-base {
  block-size: 12px;
  margin-inline: -4%;
  border-radius: 0 0 18px 18px;
  background: linear-gradient(180deg, #cbd5e1, #94a3b8);
  box-shadow: 0 18px 30px rgba(15, 23, 42, 0.2);
}

.lp-phone { inset-inline-end: 4%; inset-block-start: 0; inline-size: 30%; min-inline-size: 110px; }
.lp-phone-body {
  aspect-ratio: 9 / 18.5;
  border-radius: 28px;
  background: var(--lp-ink);
  padding: 7px;
  box-shadow: var(--lp-shadow-lg);
  border: 1px solid #1e293b;
  transform: rotate(calc(var(--lp-flip) * 6deg));
}
.lp-phone-screen {
  inline-size: 100%;
  block-size: 100%;
  border-radius: 22px;
  background: linear-gradient(160deg, var(--lp-blue-400), var(--lp-blue-600));
  position: relative;
  overflow: hidden;
}
.lp-phone-screen::before {
  content: "";
  position: absolute;
  inset-block-start: 8px;
  inset-inline: 34%;
  block-size: 12px;
  border-radius: 99px;
  background: var(--lp-ink);
}
.lp-phone-screen::after {
  content: "";
  position: absolute;
  inset-inline: 12%;
  inset-block-end: 14%;
  block-size: 38%;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.22);
  backdrop-filter: blur(6px);
}

.lp-watch { inset-inline-end: 8%; inset-block-end: 6%; inline-size: 20%; min-inline-size: 76px; }
.lp-watch-face {
  aspect-ratio: 1;
  border-radius: 28%;
  background: var(--lp-ink);
  padding: 6px;
  box-shadow: var(--lp-shadow-lg);
  border: 3px solid #cbd5e1;
}
.lp-watch-screen {
  inline-size: 100%;
  block-size: 100%;
  border-radius: 22%;
  background: radial-gradient(circle at 50% 40%, var(--lp-blue-400), #1e3a8a);
  display: grid;
  place-items: center;
}
.lp-watch-ring { inline-size: 56%; aspect-ratio: 1; border-radius: 50%; border: 3px solid rgba(255, 255, 255, 0.9); border-inline-end-color: transparent; animation: lp-spin 5s linear infinite; }

.lp-buds { inset-inline-start: 6%; inset-block-end: 4%; inline-size: 24%; min-inline-size: 90px; }
.lp-buds-case {
  aspect-ratio: 1.15;
  border-radius: 38%;
  background: linear-gradient(145deg, #ffffff, #e2e8f0);
  box-shadow: var(--lp-shadow-lg);
  border: 1px solid var(--lp-line);
  position: relative;
}
.lp-buds-case::after {
  content: "";
  position: absolute;
  inset-inline: 30%;
  inset-block-start: 46%;
  block-size: 4px;
  border-radius: 99px;
  background: var(--lp-blue);
  box-shadow: 0 0 10px var(--lp-blue-400);
}

.lp-chip {
  position: absolute;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding-block: 10px;
  padding-inline: 14px;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(10px);
  border: 1px solid var(--lp-line);
  border-radius: 16px;
  box-shadow: var(--lp-shadow-md);
  font-weight: 700;
  font-size: 0.88rem;
  color: var(--lp-ink);
  white-space: nowrap;
}
.lp-chip small { display: block; font-weight: 600; color: var(--lp-muted); font-size: 0.74rem; line-height: 1.3; }
.lp-chip-ico { inline-size: 34px; block-size: 34px; border-radius: 10px; display: grid; place-items: center; background: var(--lp-blue-50); color: var(--lp-blue); }
.lp-chip-ico--g { background: #dcfce7; color: var(--lp-green); }
.lp-chip-ico--a { background: #fef3c7; color: var(--lp-amber); }
.lp-chip--1 { inset-inline-start: -2%; inset-block-start: 8%; }
.lp-chip--2 { inset-inline-end: -2%; inset-block-start: 52%; }
.lp-chip--3 { inset-inline-start: 24%; inset-block-end: -2%; }

.lp-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.lp-card {
  position: relative;
  background: var(--lp-white);
  border: 1px solid var(--lp-line);
  border-radius: var(--lp-radius);
  padding: clamp(24px, 3vw, 36px);
  box-shadow: var(--lp-shadow-sm);
  transition: transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.35s ease, border-color 0.35s ease;
  overflow: hidden;
}
.lp-card::before {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(360px circle at var(--lp-mx, 50%) var(--lp-my, 0%), rgba(37, 99, 235, 0.10), transparent 60%);
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
}
.lp-card:hover { transform: translateY(-8px); box-shadow: var(--lp-shadow-md); border-color: var(--lp-blue-100); }
.lp-card:hover::before { opacity: 1; }
.lp-card-ico {
  inline-size: 56px;
  block-size: 56px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, var(--lp-blue-50), var(--lp-blue-100));
  color: var(--lp-blue);
  margin-block-end: 22px;
  transition: transform 0.35s ease, background 0.35s ease, color 0.35s ease;
}
.lp-card:hover .lp-card-ico { transform: scale(1.08) rotate(calc(var(--lp-flip) * -6deg)); background: var(--lp-blue); color: var(--lp-white); }
.lp-card h3 { font-size: 1.25rem; font-weight: 800; color: var(--lp-ink); margin-block-end: 10px; }
.lp-card p { color: var(--lp-muted); font-size: 0.98rem; }
.lp-card-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-block-start: 18px; }
.lp-tag {
  padding-block: 4px;
  padding-inline: 12px;
  border-radius: 999px;
  background: var(--lp-bg);
  border: 1px solid var(--lp-line);
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--lp-text);
}

.lp-trustbar {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 14px 36px;
  margin-block-start: 48px;
  padding-block: 22px;
  padding-inline: 28px;
  border-radius: var(--lp-radius);
  background: var(--lp-white);
  border: 1px dashed var(--lp-blue-100);
}
.lp-trustbar span { display: inline-flex; align-items: center; gap: 10px; font-weight: 700; color: var(--lp-ink); font-size: 0.95rem; }
.lp-trustbar svg { color: var(--lp-green); }

.lp-split { display: grid; grid-template-columns: 1fr 1.05fr; align-items: center; gap: clamp(32px, 6vw, 80px); }
.lp-checks { display: grid; gap: 16px; margin-block-start: 28px; }
.lp-check {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  padding: 18px;
  border-radius: 16px;
  background: var(--lp-white);
  border: 1px solid var(--lp-line);
  transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
}
.lp-check:hover { transform: translateX(calc(var(--lp-flip) * 8px)); border-color: var(--lp-blue-100); box-shadow: var(--lp-shadow-md); }
.lp-check-ico { inline-size: 44px; block-size: 44px; border-radius: 12px; background: var(--lp-blue-50); color: var(--lp-blue); display: grid; place-items: center; }
.lp-check h3 { font-size: 1.05rem; font-weight: 800; color: var(--lp-ink); margin-block-end: 2px; }
.lp-check p { font-size: 0.93rem; color: var(--lp-muted); }

.lp-panel {
  position: relative;
  padding: clamp(20px, 3vw, 32px);
  border-radius: 28px;
  background: linear-gradient(160deg, var(--lp-white), var(--lp-blue-50));
  border: 1px solid var(--lp-blue-100);
  box-shadow: var(--lp-shadow-lg);
}
.lp-panel-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-block-end: 20px; }
.lp-live { display: inline-flex; align-items: center; gap: 8px; font-weight: 800; color: var(--lp-ink); }
.lp-live i { inline-size: 10px; block-size: 10px; border-radius: 50%; background: var(--lp-green); animation: lp-live 1.8s infinite; }
.lp-badge-247 {
  padding-block: 6px;
  padding-inline: 14px;
  border-radius: 999px;
  background: var(--lp-blue);
  color: var(--lp-white);
  font-weight: 800;
  font-size: 0.85rem;
  font-family: "Inter", sans-serif;
  letter-spacing: 0.02em;
}
.lp-reviews { display: grid; gap: 14px; }
.lp-review {
  background: var(--lp-white);
  border: 1px solid var(--lp-line);
  border-radius: 16px;
  padding: 16px;
  box-shadow: var(--lp-shadow-sm);
  animation: lp-drift 6s ease-in-out infinite;
}
.lp-review:nth-child(2) { animation-delay: -2s; margin-inline-start: 24px; }
.lp-review:nth-child(3) { animation-delay: -4s; }
.lp-review-head { display: flex; align-items: center; gap: 12px; margin-block-end: 8px; }
.lp-avatar { inline-size: 38px; block-size: 38px; border-radius: 50%; display: grid; place-items: center; color: var(--lp-white); font-weight: 800; background: linear-gradient(135deg, var(--lp-blue-400), var(--lp-blue-600)); }
.lp-review-head b { display: block; color: var(--lp-ink); font-size: 0.95rem; line-height: 1.3; }
.lp-review-head small { color: var(--lp-muted); font-weight: 600; font-size: 0.78rem; }
.lp-stars { display: inline-flex; gap: 2px; color: var(--lp-amber); margin-inline-start: auto; }
.lp-review p { font-size: 0.92rem; color: var(--lp-text); }
.lp-reply {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-block-start: 10px;
  padding-block: 8px;
  padding-inline: 12px;
  border-radius: 10px;
  background: var(--lp-blue-50);
  color: var(--lp-blue-600);
  font-size: 0.82rem;
  font-weight: 700;
}
.lp-panel-foot { display: flex; flex-wrap: wrap; gap: 10px; margin-block-start: 18px; }
.lp-pill { display: inline-flex; align-items: center; gap: 6px; padding-block: 6px; padding-inline: 12px; border-radius: 999px; background: var(--lp-white); border: 1px solid var(--lp-line); font-size: 0.8rem; font-weight: 700; color: var(--lp-text); }
.lp-pill svg { color: var(--lp-green); }

.lp-store { display: grid; grid-template-columns: repeat(12, 1fr); gap: 20px; }
.lp-tile {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-block-size: 230px;
  padding: 28px;
  border-radius: 24px;
  background: var(--lp-white);
  border: 1px solid var(--lp-line);
  overflow: hidden;
  isolation: isolate;
  transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s ease, border-color 0.4s ease;
}
.lp-tile:hover { transform: translateY(-8px) scale(1.01); box-shadow: var(--lp-shadow-lg); border-color: var(--lp-blue-100); }
.lp-tile::after {
  content: "";
  position: absolute;
  inset-inline-end: -40px;
  inset-block-end: -40px;
  inline-size: 180px;
  block-size: 180px;
  border-radius: 50%;
  background: radial-gradient(circle, var(--lp-blue-100), transparent 70%);
  z-index: -1;
  transition: transform 0.5s ease;
}
.lp-tile:hover::after { transform: scale(1.7); }
.lp-tile--phones { grid-column: span 6; grid-row: span 2; background: linear-gradient(160deg, var(--lp-blue-50), var(--lp-white)); }
.lp-tile--laptops { grid-column: span 6; }
.lp-tile--acc { grid-column: span 3; }
.lp-tile--fix { grid-column: span 3; }
.lp-tile--support { grid-column: span 12; flex-direction: row; align-items: center; justify-content: space-between; gap: 24px; min-block-size: 0; background: linear-gradient(135deg, var(--lp-blue), var(--lp-blue-600)); border-color: transparent; color: var(--lp-white); }
.lp-tile--support::after { background: radial-gradient(circle, rgba(255, 255, 255, 0.22), transparent 70%); }
.lp-tile--support h3, .lp-tile--support p { color: var(--lp-white); }
.lp-tile--support p { opacity: 0.88; }
.lp-tile-ico { inline-size: 52px; block-size: 52px; border-radius: 15px; display: grid; place-items: center; background: var(--lp-blue-50); color: var(--lp-blue); border: 1px solid var(--lp-blue-100); }
.lp-tile--support .lp-tile-ico { background: rgba(255, 255, 255, 0.18); border-color: rgba(255, 255, 255, 0.3); color: var(--lp-white); }
.lp-tile-kind { font-size: 0.78rem; font-weight: 800; letter-spacing: 0.04em; color: var(--lp-blue); }
.lp-tile--support .lp-tile-kind { color: rgba(255, 255, 255, 0.85); }
.lp-tile h3 { font-size: 1.4rem; font-weight: 800; color: var(--lp-ink); margin-block: 6px; }
.lp-tile p { color: var(--lp-muted); font-size: 0.95rem; max-inline-size: 420px; }
.lp-tile-link { display: inline-flex; align-items: center; gap: 8px; margin-block-start: 18px; font-weight: 800; color: var(--lp-blue-600); }
.lp-tile-link svg { transition: transform 0.25s ease; transform: scaleX(var(--lp-flip)); }
.lp-tile:hover .lp-tile-link svg { transform: scaleX(var(--lp-flip)) translateX(6px); }
.lp-tile--phones .lp-mini-phones { position: absolute; inset-inline-end: 24px; inset-block-start: 24px; display: flex; gap: 12px; z-index: -1; }
.lp-mini-phone { inline-size: 74px; aspect-ratio: 9 / 18; border-radius: 18px; background: var(--lp-ink); padding: 4px; box-shadow: var(--lp-shadow-md); animation: lp-float 6s ease-in-out infinite; }
.lp-mini-phone:nth-child(2) { animation-delay: -2s; margin-block-start: 28px; }
.lp-mini-phone i { display: block; inline-size: 100%; block-size: 100%; border-radius: 14px; background: linear-gradient(160deg, var(--lp-blue-400), var(--lp-blue-600)); }
.lp-mini-phone:nth-child(2) i { background: linear-gradient(160deg, #e2e8f0, #94a3b8); }

.lp-final { padding-block: clamp(56px, 8vw, 100px); }
.lp-final-box {
  position: relative;
  overflow: hidden;
  text-align: center;
  padding-block: clamp(48px, 7vw, 88px);
  padding-inline: clamp(20px, 5vw, 64px);
  border-radius: 36px;
  background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
  color: var(--lp-white);
  box-shadow: var(--lp-shadow-lg);
  isolation: isolate;
}
.lp-final-box::before, .lp-final-box::after { content: ""; position: absolute; border-radius: 50%; z-index: -1; background: rgba(255, 255, 255, 0.10); animation: lp-breathe 8s ease-in-out infinite; }
.lp-final-box::before { inline-size: 420px; block-size: 420px; inset-inline-start: -140px; inset-block-start: -180px; }
.lp-final-box::after { inline-size: 320px; block-size: 320px; inset-inline-end: -100px; inset-block-end: -140px; animation-delay: -3s; }
.lp-final-box h2 { font-size: clamp(1.8rem, 4.4vw, 3.1rem); font-weight: 900; line-height: 1.3; margin-block-end: 16px; color: var(--lp-white); }
.lp-final-box p { max-inline-size: 620px; margin-inline: auto; font-size: clamp(1rem, 1.6vw, 1.15rem); opacity: 0.92; margin-block-end: 34px; }
.lp-final-points { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px 28px; margin-block-start: 30px; font-weight: 700; font-size: 0.95rem; }
.lp-final-points span { display: inline-flex; align-items: center; gap: 8px; }
.lp-final-box .lp-btn--pulse::before { animation-name: lp-pulse-w; }

.lp-footer { padding-block: 32px; border-block-start: 1px solid var(--lp-line); background: var(--lp-white); }
.lp-footer-inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; font-size: 0.9rem; color: var(--lp-muted); font-weight: 600; }
.lp-footer-links { display: flex; flex-wrap: wrap; gap: 20px; }
.lp-footer-links a:hover { color: var(--lp-blue-600); }

.lp-js .lp-reveal { opacity: 0; transform: translateY(32px); transition: opacity 0.8s cubic-bezier(0.2, 0.8, 0.2, 1), transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); transition-delay: var(--lp-d, 0ms); will-change: opacity, transform; }
.lp-js .lp-reveal--start { transform: translateX(calc(var(--lp-flip) * -40px)); }
.lp-js .lp-reveal--end { transform: translateX(calc(var(--lp-flip) * 40px)); }
.lp-js .lp-reveal--zoom { transform: scale(0.92); }
.lp-js .lp-reveal.lp-in { opacity: 1; transform: none; }

@keyframes lp-float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-16px); }
}
@keyframes lp-drift {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-6px); }
}
@keyframes lp-breathe {
  0%, 100% { transform: scale(1); opacity: 0.9; }
  50% { transform: scale(1.08); opacity: 1; }
}
@keyframes lp-pulse {
  0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.55); }
  70% { box-shadow: 0 0 0 22px rgba(37, 99, 235, 0); }
  100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
}
@keyframes lp-pulse-w {
  0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.55); }
  70% { box-shadow: 0 0 0 24px rgba(255, 255, 255, 0); }
  100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}
@keyframes lp-dot {
  0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.55); }
  70% { box-shadow: 0 0 0 8px rgba(37, 99, 235, 0); }
  100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
}
@keyframes lp-live {
  0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.55); }
  70% { box-shadow: 0 0 0 9px rgba(22, 163, 74, 0); }
  100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
}
@keyframes lp-spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 1024px) {
  .lp-hero-grid, .lp-split { grid-template-columns: 1fr; }
  .lp-hero-copy { text-align: center; }
  .lp-hero-text { margin-inline: auto; }
  .lp-hero-actions, .lp-hero-stats { justify-content: center; }
  .lp-stage { max-inline-size: 620px; inline-size: 100%; margin-inline: auto; }
  .lp-grid-3 { grid-template-columns: 1fr 1fr; }
  .lp-tile--phones { grid-column: span 12; grid-row: auto; }
  .lp-tile--laptops { grid-column: span 12; }
  .lp-tile--acc, .lp-tile--fix { grid-column: span 6; }
}
@media (max-width: 760px) {
  .lp-nav { display: none; }
  .lp-grid-3 { grid-template-columns: 1fr; }
  .lp-tile--acc, .lp-tile--fix { grid-column: span 12; }
  .lp-tile--support { flex-direction: column; align-items: flex-start; }
  .lp-chip--2 { inset-inline-end: 0; }
  .lp-chip--1 { inset-inline-start: 0; }
  .lp-hero-actions .lp-btn { inline-size: 100%; }
  .lp-review:nth-child(2) { margin-inline-start: 0; }
  .lp-tile--phones .lp-mini-phones { opacity: 0.35; }
}
@media (prefers-reduced-motion: reduce) {
  .lp-root *, .lp-root *::before, .lp-root *::after { animation: none !important; transition: none !important; }
  .lp-js .lp-reveal { opacity: 1; transform: none; }
}
</style>
</head>
<body>
<div class="lp-root" id="lp-root">

  <header class="lp-header" id="lp-header">
    <div class="lp-container lp-header-inner">
      <a href="#lp-top" class="lp-brand" aria-label="الصفحة الرئيسية">
        <span class="lp-brand-mark">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="3"/><path d="M11 18h2"/></svg>
        </span>
        <span>تك ستور</span>
      </a>
      <nav class="lp-nav" aria-label="التنقل الرئيسي">
        <a href="#lp-trust">الأمان</a>
        <a href="#lp-transparency">الشفافية</a>
        <a href="#lp-store">المتجر</a>
      </nav>
      <a href="/store" class="lp-btn lp-btn--primary lp-btn--sm">دخول المتجر</a>
    </div>
  </header>

  <main id="lp-top">

    <section class="lp-hero">
      <div class="lp-container lp-hero-grid">
        <div class="lp-hero-copy">
          <span class="lp-eyebrow lp-reveal"><i class="lp-eyebrow-dot"></i>منصتك الموثوقة للأجهزة الذكية</span>
          <h1 class="lp-hero-title lp-reveal" style="--lp-d:100ms">تسوّق أحدث الأجهزة الذكية <span>بثقة كاملة</span> وأمان لا مثيل له</h1>
          <p class="lp-hero-text lp-reveal" style="--lp-d:200ms">نجمع لك أفضل الهواتف والحواسيب والإكسسوارات الأصلية في مكان واحد، مع إدارة تراقب كل طلب على مدار الساعة، وتقييمات حقيقية معلنة، وحماية كاملة لبياناتك وحقوقك.</p>
          <div class="lp-hero-actions lp-reveal" style="--lp-d:300ms">
            <a href="/store" class="lp-btn lp-btn--primary lp-btn--pulse lp-btn--lg">
              <span>ابدأ التسوق الآن</span>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
            </a>
            <a href="#lp-trust" class="lp-btn lp-btn--ghost lp-btn--lg">لماذا نحن؟</a>
          </div>
          <div class="lp-hero-stats lp-reveal" style="--lp-d:400ms">
            <div class="lp-stat"><strong><span data-lp-count="50000">0</span>+</strong><span>عميل راضٍ</span></div>
            <div class="lp-stat"><strong><span data-lp-count="4.9" data-lp-decimals="1">0</span>/5</strong><span>متوسط التقييمات</span></div>
            <div class="lp-stat"><strong>24/7</strong><span>دعم متواصل</span></div>
          </div>
        </div>

        <div class="lp-stage lp-reveal lp-reveal--zoom" style="--lp-d:200ms" aria-hidden="true">
          <div class="lp-stage-glow"></div>

          <div class="lp-float lp-laptop">
            <div class="lp-laptop-screen">
              <div class="lp-laptop-display">
                <div>
                  <div class="lp-bar lp-bar--m"></div>
                  <div class="lp-bar lp-bar--s" style="margin-block-start:8px"></div>
                </div>
                <div class="lp-tiles"><i></i><i></i><i></i><i></i><i></i><i></i></div>
              </div>
            </div>
            <div class="lp-laptop-base"></div>
          </div>

          <div class="lp-float lp-float--b lp-phone">
            <div class="lp-phone-body"><div class="lp-phone-screen"></div></div>
          </div>

          <div class="lp-float lp-float--c lp-watch">
            <div class="lp-watch-face"><div class="lp-watch-screen"><div class="lp-watch-ring"></div></div></div>
          </div>

          <div class="lp-float lp-float--d lp-buds">
            <div class="lp-buds-case"></div>
          </div>

          <div class="lp-chip lp-chip--1 lp-float lp-float--c" style="position:absolute">
            <span class="lp-chip-ico lp-chip-ico--g"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.4 9.3 8 11 4.6-1.7 8-6 8-11V5l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg></span>
            <span>دفع آمن 100%<small>تشفير كامل للمعاملات</small></span>
          </div>
          <div class="lp-chip lp-chip--2 lp-float lp-float--d" style="position:absolute">
            <span class="lp-chip-ico lp-chip-ico--a"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg></span>
            <span>+12,000 تقييم<small>معلنة وظاهرة للجميع</small></span>
          </div>
          <div class="lp-chip lp-chip--3 lp-float" style="position:absolute">
            <span class="lp-chip-ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
            <span>إدارة متاحة الآن<small>رد خلال دقائق</small></span>
          </div>
        </div>
      </div>
    </section>

    <section class="lp-section" id="lp-trust">
      <div class="lp-container">
        <div class="lp-head">
          <span class="lp-eyebrow lp-reveal"><i class="lp-eyebrow-dot"></i>الثقة والأمان</span>
          <h2 class="lp-title lp-reveal" style="--lp-d:100ms">كل ما تحتاجه <em>لتشتري بطمأنينة</em></h2>
          <p class="lp-lead lp-reveal" style="--lp-d:200ms">بنينا منصتنا على ثلاثة أعمدة لا نتنازل عنها: دفع محمي، حقوق مصونة، وبيانات في أيدٍ أمينة.</p>
        </div>

        <div class="lp-grid-3">
          <article class="lp-card lp-reveal" style="--lp-d:0ms">
            <div class="lp-card-ico">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>
            </div>
            <h3>مدفوعات آمنة ومشفّرة</h3>
            <p>جميع عمليات الدفع تتم عبر بوابات معتمدة وبتشفير عالي المستوى، ولا نحتفظ بأي بيانات بطاقات على خوادمنا إطلاقاً.</p>
            <div class="lp-card-tags"><span class="lp-tag">تشفير SSL</span><span class="lp-tag">دفع متعدد</span><span class="lp-tag">حماية من الاحتيال</span></div>
          </article>

          <article class="lp-card lp-reveal" style="--lp-d:120ms">
            <div class="lp-card-ico">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M5 7h14"/><path d="m5 7-3 7a4 4 0 0 0 6 0L5 7Z"/><path d="m19 7-3 7a4 4 0 0 0 6 0l-3-7Z"/></svg>
            </div>
            <h3>حماية كاملة لحقوق المستخدم</h3>
            <p>سياسات استرجاع واستبدال واضحة، وضمان أصالة المنتجات، ونظام شكاوى يضمن لك حقك حتى آخر خطوة بعد الشراء.</p>
            <div class="lp-card-tags"><span class="lp-tag">استرجاع مرن</span><span class="lp-tag">ضمان أصلي</span><span class="lp-tag">حل النزاعات</span></div>
          </article>

          <article class="lp-card lp-reveal" style="--lp-d:240ms">
            <div class="lp-card-ico">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><path d="M12 15v2"/></svg>
            </div>
            <h3>سلامة وخصوصية بياناتك</h3>
            <p>بياناتك الشخصية محمية بأعلى معايير الأمان، ولا تُشارك مع أي طرف ثالث دون موافقتك الصريحة، ولك التحكم الكامل بها.</p>
            <div class="lp-card-tags"><span class="lp-tag">خصوصية تامة</span><span class="lp-tag">نسخ احتياطي</span><span class="lp-tag">تحكم كامل</span></div>
          </article>
        </div>

        <div class="lp-trustbar lp-reveal">
          <span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>منتجات أصلية 100%</span>
          <span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>شحن سريع وتتبع مباشر</span>
          <span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>ضمان استرجاع الأموال</span>
          <span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-6"/></svg>مراجعة إدارية لكل طلب</span>
        </div>
      </div>
    </section>

    <section class="lp-section lp-section--alt" id="lp-transparency">
      <div class="lp-container lp-split">
        <div>
          <span class="lp-eyebrow lp-reveal"><i class="lp-eyebrow-dot"></i>الشفافية والإدارة</span>
          <h2 class="lp-title lp-reveal" style="--lp-d:100ms">إدارة حاضرة <em>معك في كل لحظة</em></h2>
          <p class="lp-lead lp-reveal" style="--lp-d:200ms">لا نختبئ خلف الشاشات. فريق الإدارة يتابع الطلبات والتقييمات ويتواصل معك مباشرة، لأن ثقتك هي أغلى ما نملك.</p>

          <div class="lp-checks">
            <div class="lp-check lp-reveal lp-reveal--start" style="--lp-d:100ms">
              <span class="lp-check-ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
              <div><h3>إدارة متاحة 24/7</h3><p>فريق مختص يستقبل استفساراتك وشكاواك في أي ساعة، طوال أيام الأسبوع، بلا إجازات.</p></div>
            </div>
            <div class="lp-check lp-reveal lp-reveal--start" style="--lp-d:200ms">
              <span class="lp-check-ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg></span>
              <div><h3>كل تقييم معلن ومُقدَّر</h3><p>نعرض جميع آراء العملاء كما هي دون حذف أو تجميل، ونرد على كل ملاحظة بجدية واحترام.</p></div>
            </div>
            <div class="lp-check lp-reveal lp-reveal--start" style="--lp-d:300ms">
              <span class="lp-check-ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.4 9.3 8 11 4.6-1.7 8-6 8-11V5l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg></span>
              <div><h3>حقوقك مصونة بصرامة</h3><p>أي مخالفة بحق العميل تُعالج فوراً من الإدارة العليا، وفق سياسات معلنة وملزمة للجميع.</p></div>
            </div>
          </div>
        </div>

        <div class="lp-panel lp-reveal lp-reveal--end" style="--lp-d:150ms">
          <div class="lp-panel-top">
            <span class="lp-live"><i></i>لوحة الشفافية المباشرة</span>
            <span class="lp-badge-247">24/7</span>
          </div>
          <div class="lp-reviews">
            <div class="lp-review">
              <div class="lp-review-head">
                <span class="lp-avatar">أ</span>
                <div><b>أحمد محمود</b><small>هاتف ذكي · طلب موثّق</small></div>
                <span class="lp-stars" aria-label="5 من 5">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg>
                </span>
              </div>
              <p>وصلني الجهاز في وقت قياسي وبحالة ممتازة، والدعم ردّ عليّ خلال دقائق.</p>
              <div class="lp-reply"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>ردّ الإدارة: شكراً لثقتك، سعداء بخدمتك دائماً.</div>
            </div>

            <div class="lp-review">
              <div class="lp-review-head">
                <span class="lp-avatar">س</span>
                <div><b>سارة عبد الله</b><small>حاسوب محمول · طلب موثّق</small></div>
                <span class="lp-stars" aria-label="5 من 5">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg>
                </span>
              </div>
              <p>تجربة شراء مريحة وشفافة، وتعاملوا مع ملاحظتي البسيطة بجدية تامة.</p>
              <div class="lp-reply"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>ردّ الإدارة: تمت معالجة ملاحظتك وتحسين الخدمة.</div>
            </div>

            <div class="lp-review">
              <div class="lp-review-head">
                <span class="lp-avatar">خ</span>
                <div><b>خالد إبراهيم</b><small>إكسسوارات · طلب موثّق</small></div>
                <span class="lp-stars" aria-label="5 من 5">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3 6.5 7 .9-5.1 4.9 1.3 7L12 17.8 5.8 21.3l1.3-7L2 9.4l7-.9L12 2Z"/></svg>
                </span>
              </div>
              <p>منتجات أصلية وأسعار عادلة، وأكثر ما أعجبني وضوح السياسات.</p>
            </div>
          </div>
          <div class="lp-panel-foot">
            <span class="lp-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>تقييمات موثّقة</span>
            <span class="lp-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>ردود الإدارة معلنة</span>
            <span class="lp-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>بلا حذف أو تعديل</span>
          </div>
        </div>
      </div>
    </section>

    <section class="lp-section" id="lp-store">
      <div class="lp-container">
        <div class="lp-head">
          <span class="lp-eyebrow lp-reveal"><i class="lp-eyebrow-dot"></i>نظرة على المتجر</span>
          <h2 class="lp-title lp-reveal" style="--lp-d:100ms">كل ما تحتاجه من <em>التقنية</em> في مكان واحد</h2>
          <p class="lp-lead lp-reveal" style="--lp-d:200ms">منتجات أصلية مختارة بعناية، وخدمات متخصصة تبقي أجهزتك في أفضل حال.</p>
        </div>

        <div class="lp-store">
          <a href="/store" class="lp-tile lp-tile--phones lp-reveal">
            <div class="lp-mini-phones" aria-hidden="true">
              <div class="lp-mini-phone"><i></i></div>
              <div class="lp-mini-phone"><i></i></div>
            </div>
            <div>
              <span class="lp-tile-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="3"/><path d="M11 18h2"/></svg></span>
            </div>
            <div>
              <span class="lp-tile-kind">المنتجات</span>
              <h3>الهواتف الذكية</h3>
              <p>أحدث الإصدارات من أفضل العلامات العالمية بضمان رسمي وأسعار منافسة.</p>
              <span class="lp-tile-link">تصفح الهواتف <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg></span>
            </div>
          </a>

          <a href="/store" class="lp-tile lp-tile--laptops lp-reveal" style="--lp-d:120ms">
            <div>
              <span class="lp-tile-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M2 20h20"/></svg></span>
            </div>
            <div>
              <span class="lp-tile-kind">المنتجات</span>
              <h3>الحواسيب المحمولة</h3>
              <p>أداء قوي للعمل والدراسة والإبداع، بخيارات تناسب كل ميزانية.</p>
              <span class="lp-tile-link">تصفح الحواسيب <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg></span>
            </div>
          </a>

          <a href="/store" class="lp-tile lp-tile--acc lp-reveal" style="--lp-d:200ms">
            <div>
              <span class="lp-tile-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 14v-2a9 9 0 0 1 18 0v2"/><rect x="2" y="14" width="5" height="7" rx="2"/><rect x="17" y="14" width="5" height="7" rx="2"/></svg></span>
            </div>
            <div>
              <span class="lp-tile-kind">المنتجات</span>
              <h3>الإكسسوارات</h3>
              <p>سماعات وشواحن وملحقات أصلية.</p>
            </div>
          </a>

          <a href="/store" class="lp-tile lp-tile--fix lp-reveal" style="--lp-d:280ms">
            <div>
              <span class="lp-tile-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.4 2.4-2.6-.6-.6-2.6 2.6-2.2Z"/></svg></span>
            </div>
            <div>
              <span class="lp-tile-kind">الخدمات</span>
              <h3>صيانة معتمدة</h3>
              <p>فنيون محترفون وقطع أصلية.</p>
            </div>
          </a>

          <div class="lp-tile lp-tile--support lp-reveal" style="--lp-d:100ms">
            <div style="display:flex;gap:20px;align-items:center">
              <span class="lp-tile-ico"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 14v-2a9 9 0 0 1 18 0v2"/><path d="M21 16v2a3 3 0 0 1-3 3h-4"/><rect x="2" y="13" width="4" height="6" rx="2"/><rect x="18" y="13" width="4" height="6" rx="2"/></svg></span>
              <div>
                <span class="lp-tile-kind">الخدمات</span>
                <h3>الدعم التقني على مدار الساعة</h3>
                <p>مختصون جاهزون لمساعدتك في الإعداد والاستخدام وحل أي مشكلة تقنية فوراً.</p>
              </div>
            </div>
            <a href="/store" class="lp-btn lp-btn--white lp-btn--sm">تواصل معنا</a>
          </div>
        </div>
      </div>
    </section>

    <section class="lp-final">
      <div class="lp-container">
        <div class="lp-final-box lp-reveal lp-reveal--zoom">
          <span class="lp-eyebrow" style="background:rgba(255,255,255,0.16);color:#fff;border-color:rgba(255,255,255,0.3)"><i class="lp-eyebrow-dot" style="background:#fff"></i>جاهز للبدء؟</span>
          <h2 style="margin-block-start:20px">ادخل المتجر الآن وتسوّق وأنت مطمئن</h2>
          <p>انضم إلى آلاف العملاء الذين اختاروا الأمان والشفافية والدعم المتواصل. تجربتك القادمة تبدأ بنقرة واحدة.</p>
          <a href="/store" class="lp-btn lp-btn--white lp-btn--pulse lp-btn--lg">
            <span>ابدأ التسوق الآن</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
          </a>
          <div class="lp-final-points">
            <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>دفع آمن</span>
            <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>حقوق محمية</span>
            <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5 9-10"/></svg>دعم 24/7</span>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer class="lp-footer">
    <div class="lp-container lp-footer-inner">
      <span>© 2026 تك ستور. جميع الحقوق محفوظة.</span>
      <div class="lp-footer-links">
        <a href="#lp-trust">سياسة الخصوصية</a>
        <a href="#lp-transparency">الشروط والأحكام</a>
        <a href="#lp-store">تواصل معنا</a>
      </div>
    </div>
  </footer>

</div>

<script>
(function () {
  var root = document.getElementById("lp-root");
  if (!root) return;
  root.classList.add("lp-js");

  var header = document.getElementById("lp-header");
  var onScroll = function () {
    if (window.scrollY > 12) header.classList.add("lp-scrolled");
    else header.classList.remove("lp-scrolled");
  };
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  var animateCount = function (el) {
    var target = parseFloat(el.getAttribute("data-lp-count"));
    var decimals = parseInt(el.getAttribute("data-lp-decimals") || "0", 10);
    var duration = 1800;
    var start = null;
    var step = function (ts) {
      if (start === null) start = ts;
      var p = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      var val = target * eased;
      el.textContent = decimals ? val.toFixed(decimals) : Math.floor(val).toLocaleString("en-US");
      if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };

  var revealEls = root.querySelectorAll(".lp-reveal");
  var counters = root.querySelectorAll("[data-lp-count]");

  if ("IntersectionObserver" in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("lp-in");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -6% 0px" });
    revealEls.forEach(function (el) { io.observe(el); });

    var co = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          co.unobserve(entry.target);
        }
      });
    }, { threshold: 0.6 });
    counters.forEach(function (el) { co.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add("lp-in"); });
    counters.forEach(function (el) {
      el.textContent = el.getAttribute("data-lp-count");
    });
  }

  root.querySelectorAll(".lp-card").forEach(function (card) {
    card.addEventListener("pointermove", function (e) {
      var r = card.getBoundingClientRect();
      card.style.setProperty("--lp-mx", (e.clientX - r.left) + "px");
      card.style.setProperty("--lp-my", (e.clientY - r.top) + "px");
    });
  });

  root.querySelectorAll('a[href^="#lp-"]').forEach(function (a) {
    a.addEventListener("click", function (e) {
      var target = document.querySelector(a.getAttribute("href"));
      if (!target) return;
      e.preventDefault();
      var offset = header ? header.offsetHeight : 0;
      var top = target.getBoundingClientRect().top + window.pageYOffset - offset + 1;
      window.scrollTo({ top: top, behavior: "smooth" });
    });
  });

  var stage = root.querySelector(".lp-stage");
  if (stage && window.matchMedia("(pointer: fine)").matches) {
    var floaters = stage.querySelectorAll(".lp-laptop, .lp-phone, .lp-watch, .lp-buds");
    var depth = [10, 18, 24, 14];
    stage.addEventListener("pointermove", function (e) {
      var r = stage.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width - 0.5;
      var y = (e.clientY - r.top) / r.height - 0.5;
      floaters.forEach(function (el, i) {
        el.style.translate = (x * depth[i]) + "px " + (y * depth[i]) + "px";
      });
    });
    stage.addEventListener("pointerleave", function () {
      floaters.forEach(function (el) { el.style.translate = "0 0"; });
    });
  }
})();
</script>
</body>
</html>