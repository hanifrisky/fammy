<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Aplikasi Penilaian Karakter Siswa — Empati, Resilience, Inisiatif, dan 7 Kebiasaan Anak Indonesia Hebat">
    <title>Penilaian Karakter Siswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════════════════════
           CSS VARIABLES & RESET
           ═══════════════════════════════════════════════════════════ */
        :root {
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-card: rgba(17, 24, 39, 0.7);
            --bg-card-hover: rgba(31, 41, 55, 0.8);
            --glass-border: rgba(255, 255, 255, 0.08);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);

            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;

            --accent-gradient: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa);
            --accent-solid: #8b5cf6;
            --accent-hover: #a78bfa;

            --green-bg: rgba(16, 185, 129, 0.12);
            --green-border: rgba(16, 185, 129, 0.3);
            --green-text: #34d399;
            --green-accent: #10b981;

            --blue-bg: rgba(59, 130, 246, 0.12);
            --blue-border: rgba(59, 130, 246, 0.3);
            --blue-text: #60a5fa;
            --blue-accent: #3b82f6;

            --amber-bg: rgba(245, 158, 11, 0.12);
            --amber-border: rgba(245, 158, 11, 0.3);
            --amber-text: #fbbf24;
            --amber-accent: #f59e0b;

            --red-bg: rgba(239, 68, 68, 0.12);
            --red-border: rgba(239, 68, 68, 0.3);
            --red-text: #f87171;
            --red-accent: #ef4444;

            --radius: 16px;
            --radius-sm: 10px;
            --radius-xs: 6px;
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 600px 400px at 20% 20%, rgba(99, 102, 241, 0.08), transparent),
                radial-gradient(ellipse 500px 500px at 80% 80%, rgba(139, 92, 246, 0.06), transparent),
                radial-gradient(ellipse 400px 300px at 50% 50%, rgba(59, 130, 246, 0.04), transparent);
            pointer-events: none;
            z-index: 0;
            animation: bgPulse 8s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            from { opacity: 0.7; }
            to   { opacity: 1; }
        }

        /* ═══════════════════════════════════════════════════════════
           LAYOUT
           ═══════════════════════════════════════════════════════════ */
        .app {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 20px 60px;
        }

        /* ═══════════════════════════════════════════════════════════
           HEADER
           ═══════════════════════════════════════════════════════════ */
        .header {
            text-align: center;
            margin-bottom: 36px;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }

        .header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 6px;
        }

        /* ═══════════════════════════════════════════════════════════
           STEP INDICATOR
           ═══════════════════════════════════════════════════════════ */
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 40px;
        }

        .step-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            border: 2px solid var(--glass-border);
            background: var(--bg-secondary);
            color: var(--text-muted);
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
        }

        .step-dot.active {
            background: var(--accent-gradient);
            border-color: var(--accent-solid);
            color: #fff;
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        }

        .step-dot.done {
            background: rgba(16, 185, 129, 0.2);
            border-color: var(--green-accent);
            color: var(--green-text);
        }

        .step-line {
            width: 60px;
            height: 2px;
            background: var(--glass-border);
            transition: var(--transition);
            flex-shrink: 0;
        }

        .step-line.done {
            background: var(--green-accent);
        }

        /* ═══════════════════════════════════════════════════════════
           CARDS (Glass)
           ═══════════════════════════════════════════════════════════ */
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            padding: 32px;
            box-shadow: var(--glass-shadow);
            transition: var(--transition);
        }

        .card h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-primary);
        }

        .card .subtitle {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* ═══════════════════════════════════════════════════════════
           WIZARD STEPS
           ═══════════════════════════════════════════════════════════ */
        .wizard-step {
            display: none;
            animation: fadeSlideIn 0.4s ease-out;
        }

        .wizard-step.active {
            display: block;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ═══════════════════════════════════════════════════════════
           SEARCHABLE SELECT (Guru)
           ═══════════════════════════════════════════════════════════ */
        .search-select {
            position: relative;
        }

        .search-select input {
            width: 100%;
            padding: 14px 18px;
            padding-right: 44px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
        }

        .search-select input::placeholder {
            color: var(--text-muted);
        }

        .search-select input:focus {
            border-color: var(--accent-solid);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        }

        .search-select .chevron {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            transition: var(--transition);
            font-size: 0.7rem;
        }

        .search-select.open .chevron {
            transform: translateY(-50%) rotate(180deg);
        }

        .search-select .dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            max-height: 260px;
            overflow-y: auto;
            display: none;
            z-index: 100;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .search-select.open .dropdown {
            display: block;
        }

        .search-select .dropdown-item {
            padding: 12px 18px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
            color: var(--text-secondary);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .search-select .dropdown-item:hover,
        .search-select .dropdown-item.highlighted {
            background: rgba(139, 92, 246, 0.12);
            color: var(--text-primary);
        }

        .search-select .dropdown-item.selected {
            color: var(--accent-hover);
            font-weight: 600;
        }

        .search-select .no-results {
            padding: 16px 18px;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-align: center;
        }

        /* Scrollbar */
        .dropdown::-webkit-scrollbar { width: 6px; }
        .dropdown::-webkit-scrollbar-track { background: transparent; }
        .dropdown::-webkit-scrollbar-thumb { background: var(--glass-border); border-radius: 3px; }

        /* ═══════════════════════════════════════════════════════════
           STANDARD SELECT
           ═══════════════════════════════════════════════════════════ */
        .form-select {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
        }

        .form-select:focus {
            border-color: var(--accent-solid);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        }

        .form-select option {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        /* ═══════════════════════════════════════════════════════════
           MONTH CARDS
           ═══════════════════════════════════════════════════════════ */
        .month-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px;
        }

        .month-card {
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid var(--glass-border);
            border-radius: var(--radius-sm);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-secondary);
            user-select: none;
        }

        .month-card:hover {
            border-color: rgba(139, 92, 246, 0.3);
            background: rgba(139, 92, 246, 0.06);
            transform: translateY(-2px);
        }

        .month-card.selected {
            border-color: var(--accent-solid);
            background: rgba(139, 92, 246, 0.12);
            color: var(--text-primary);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.2);
        }

        /* ═══════════════════════════════════════════════════════════
           STUDENT MAPPING (Step 4)
           ═══════════════════════════════════════════════════════════ */
        .student-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media (max-width: 768px) {
            .student-grid { grid-template-columns: 1fr; }
        }

        .category-panel {
            border-radius: var(--radius-sm);
            border: 1px solid;
            overflow: hidden;
            transition: var(--transition);
        }

        .category-panel.cat-konsisten {
            border-color: var(--green-border);
            background: var(--green-bg);
        }
        .category-panel.cat-sering {
            border-color: var(--blue-border);
            background: var(--blue-bg);
        }
        .category-panel.cat-kadang {
            border-color: var(--amber-border);
            background: var(--amber-bg);
        }
        .category-panel.cat-belum {
            border-color: var(--red-border);
            background: var(--red-bg);
        }

        .category-header {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cat-konsisten .category-header { color: var(--green-text); }
        .cat-sering .category-header    { color: var(--blue-text); }
        .cat-kadang .category-header    { color: var(--amber-text); }
        .cat-belum .category-header     { color: var(--red-text); }

        .category-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            padding: 0 7px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .cat-konsisten .category-badge { background: rgba(16,185,129,0.25); color: var(--green-text); }
        .cat-sering .category-badge    { background: rgba(59,130,246,0.25); color: var(--blue-text); }
        .cat-kadang .category-badge    { background: rgba(245,158,11,0.25); color: var(--amber-text); }
        .cat-belum .category-badge     { background: rgba(239,68,68,0.25); color: var(--red-text); }

        .category-search {
            padding: 0 12px 8px;
        }

        .category-search input {
            width: 100%;
            padding: 8px 12px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-xs);
            color: var(--text-primary);
            font-size: 0.8rem;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
        }

        .category-search input:focus {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .category-search input::placeholder {
            color: var(--text-muted);
        }

        .student-list {
            max-height: 280px;
            overflow-y: auto;
            padding: 0 6px 8px;
        }

        .student-list::-webkit-scrollbar { width: 4px; }
        .student-list::-webkit-scrollbar-track { background: transparent; }
        .student-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

        .student-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-xs);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .student-item:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .student-item input[type="checkbox"] {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid var(--glass-border);
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
            position: relative;
        }

        .student-item input[type="checkbox"]:checked {
            border-color: transparent;
        }

        .cat-konsisten .student-item input:checked { background: var(--green-accent); }
        .cat-sering .student-item input:checked    { background: var(--blue-accent); }
        .cat-kadang .student-item input:checked    { background: var(--amber-accent); }
        .cat-belum .student-item input:checked     { background: var(--red-accent); }

        .student-item input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .empty-message {
            padding: 20px 16px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-style: italic;
        }

        /* ═══════════════════════════════════════════════════════════
           BUTTONS
           ═══════════════════════════════════════════════════════════ */
        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 28px;
            border: none;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-secondary);
            border: 1px solid var(--glass-border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }

        .btn-primary {
            background: var(--accent-gradient);
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35);
        }

        .btn-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            padding: 14px 36px;
            font-size: 1rem;
        }

        .btn-submit:hover {
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ═══════════════════════════════════════════════════════════
           SUMMARY (Step 5)
           ═══════════════════════════════════════════════════════════ */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .summary-item {
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
        }

        .summary-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 6px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            word-break: break-word;
        }

        .summary-students {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .summary-students { grid-template-columns: 1fr; }
        }

        .summary-cat {
            padding: 14px;
            border-radius: var(--radius-sm);
            border: 1px solid;
        }

        .summary-cat.sc-konsisten { border-color: var(--green-border); background: var(--green-bg); }
        .summary-cat.sc-sering    { border-color: var(--blue-border); background: var(--blue-bg); }
        .summary-cat.sc-kadang    { border-color: var(--amber-border); background: var(--amber-bg); }
        .summary-cat.sc-belum     { border-color: var(--red-border); background: var(--red-bg); }

        .summary-cat-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .sc-konsisten .summary-cat-title { color: var(--green-text); }
        .sc-sering .summary-cat-title    { color: var(--blue-text); }
        .sc-kadang .summary-cat-title    { color: var(--amber-text); }
        .sc-belum .summary-cat-title     { color: var(--red-text); }

        .summary-cat-list {
            font-size: 0.8rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .summary-cat-count {
            font-weight: 700;
            margin-left: 4px;
        }

        /* ═══════════════════════════════════════════════════════════
           PROGRESS OVERLAY
           ═══════════════════════════════════════════════════════════ */
        .progress-overlay {
            margin-top: 24px;
            padding: 24px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: var(--radius-sm);
            border: 1px solid var(--glass-border);
        }

        .progress-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 4px;
            width: 0%;
            transition: width 0.4s ease;
        }

        .progress-bar-fill.success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .progress-bar-fill.has-errors {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
        }

        .progress-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* ═══════════════════════════════════════════════════════════
           RESULTS TABLE
           ═══════════════════════════════════════════════════════════ */
        .results-section {
            margin-top: 24px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }

        .results-table th {
            text-align: left;
            padding: 10px 12px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--glass-border);
            font-weight: 600;
        }

        .results-table td {
            padding: 10px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            color: var(--text-secondary);
        }

        .results-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .status-badge.success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--green-text);
        }

        .status-badge.failed {
            background: rgba(239, 68, 68, 0.15);
            color: var(--red-text);
        }

        /* ═══════════════════════════════════════════════════════════
           LOADING SPINNER
           ═══════════════════════════════════════════════════════════ */
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-overlay {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 40px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* ═══════════════════════════════════════════════════════════
           TOAST / ALERT
           ═══════════════════════════════════════════════════════════ */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 14px 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.85rem;
            z-index: 9999;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            animation: toastIn 0.3s ease-out;
            max-width: 360px;
        }

        .toast.error { border-color: var(--red-border); }
        .toast.success { border-color: var(--green-border); }

        @keyframes toastIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ═══════════════════════════════════════════════════════════
           RESPONSIVE
           ═══════════════════════════════════════════════════════════ */
        @media (max-width: 600px) {
            .app { padding: 16px 12px 40px; }
            .card { padding: 20px 16px; }
            .header h1 { font-size: 1.35rem; }
            .step-dot { width: 32px; height: 32px; font-size: 0.7rem; }
            .step-line { width: 30px; }
            .month-grid { grid-template-columns: repeat(2, 1fr); }
            .summary-grid { grid-template-columns: 1fr; }
            .btn { padding: 10px 20px; font-size: 0.85rem; }
        }

        /* ═══════════════════════════════════════════════════════════
           CONFIRMATION CHECKBOX
           ═══════════════════════════════════════════════════════════ */
        .confirm-check {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 18px;
            background: rgba(139, 92, 246, 0.06);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            cursor: pointer;
        }

        .confirm-check input[type="checkbox"] {
            appearance: none;
            width: 22px;
            height: 22px;
            border: 2px solid var(--accent-solid);
            border-radius: 5px;
            cursor: pointer;
            flex-shrink: 0;
            transition: var(--transition);
            position: relative;
            margin-top: 1px;
        }

        .confirm-check input[type="checkbox"]:checked {
            background: var(--accent-solid);
        }

        .confirm-check input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .confirm-check label {
            font-size: 0.88rem;
            color: var(--text-secondary);
            line-height: 1.5;
            cursor: pointer;
        }

        /* ═══════════════════════════════════════════════════════════
           MODE TOGGLE
           ═══════════════════════════════════════════════════════════ */
        .mode-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 28px;
        }

        .mode-bar .mode-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
        }

        .mode-btn-group {
            display: flex;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            overflow: hidden;
        }

        .mode-btn {
            padding: 8px 18px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .mode-btn:hover {
            color: var(--text-secondary);
        }

        .mode-btn.active-debug {
            background: rgba(245, 158, 11, 0.18);
            color: var(--amber-text);
        }

        .mode-btn.active-prod {
            background: rgba(16, 185, 129, 0.18);
            color: var(--green-text);
        }

        .mode-indicator {
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .mode-indicator.debug {
            background: rgba(245, 158, 11, 0.15);
            color: var(--amber-text);
        }

        .mode-indicator.production {
            background: rgba(16, 185, 129, 0.15);
            color: var(--green-text);
        }

        /* ═══════════════════════════════════════════════════════════
           CATEGORY ACTION BUTTONS
           ═══════════════════════════════════════════════════════════ */
        .category-header {
            flex-wrap: wrap;
        }

        .category-actions {
            display: flex;
            gap: 4px;
            margin-left: auto;
        }

        .cat-action-btn {
            padding: 3px 9px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 4px;
            background: rgba(0,0,0,0.2);
            color: var(--text-muted);
            font-family: inherit;
            font-size: 0.65rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .cat-action-btn:hover {
            background: rgba(255,255,255,0.08);
            color: var(--text-secondary);
        }

        /* ═══════════════════════════════════════════════════════════
           DEBUG PAYLOAD VIEWER
           ═══════════════════════════════════════════════════════════ */
        .debug-payloads {
            margin-top: 24px;
        }

        .debug-payloads h3 {
            font-size: 1rem;
            margin-bottom: 14px;
            color: var(--amber-text);
        }

        .payload-card {
            margin-bottom: 12px;
            border: 1px solid rgba(245,158,11,0.2);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }

        .payload-header {
            padding: 10px 14px;
            background: rgba(245,158,11,0.08);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.82rem;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .payload-header:hover {
            background: rgba(245,158,11,0.12);
        }

        .payload-header .ph-quiz {
            font-weight: 600;
            color: var(--text-primary);
            margin-right: 8px;
        }

        .payload-header .ph-toggle {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .payload-body {
            display: none;
            padding: 12px 14px;
            background: rgba(0,0,0,0.2);
        }

        .payload-body.open {
            display: block;
        }

        .payload-body pre {
            font-size: 0.75rem;
            color: var(--text-secondary);
            white-space: pre-wrap;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            line-height: 1.5;
            max-height: 300px;
            overflow-y: auto;
        }

        .payload-body pre::-webkit-scrollbar { width: 4px; }
        .payload-body pre::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

        .status-badge.debug {
            background: rgba(245, 158, 11, 0.15);
            color: var(--amber-text);
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- ═══════════════ HEADER ═══════════════ -->
        <div class="header">
            <h1>Penilaian Karakter Siswa</h1>
            <p>Empati · Resilience · Inisiatif · 7 Kebiasaan Anak Indonesia Hebat</p>
        </div>

        <!-- ═══════════════ MODE TOGGLE ═══════════════ -->
        <!-- <div class="mode-bar">
            <span class="mode-label">Mode:</span>
            <div class="mode-btn-group">
                <button class="mode-btn" id="btnDebug" onclick="setMode('debug')">🔍 Debug</button>
                <button class="mode-btn" id="btnProduction" onclick="setMode('production')">🚀 Production</button>
            </div>
        </div> -->

        <!-- ═══════════════ STEP INDICATOR ═══════════════ -->
        <div class="step-indicator" id="stepIndicator">
            <div class="step-dot active" data-step="1" onclick="goToStep(1)">1</div>
            <div class="step-line" id="line1"></div>
            <div class="step-dot" data-step="2" onclick="goToStep(2)">2</div>
            <div class="step-line" id="line2"></div>
            <div class="step-dot" data-step="3" onclick="goToStep(3)">3</div>
            <div class="step-line" id="line3"></div>
            <div class="step-dot" data-step="4" onclick="goToStep(4)">4</div>
            <div class="step-line" id="line4"></div>
            <div class="step-dot" data-step="5" onclick="goToStep(5)">5</div>
        </div>

        <!-- ═══════════════ STEP 1: GURU ═══════════════ -->
        <div class="wizard-step active" id="wizardStep1">
            <div class="card">
                <h2>👨‍🏫 Pilih Guru</h2>
                <p class="subtitle">Pilih nama lengkap Anda yang melakukan observasi (sertakan gelar jika ada)</p>

                <div class="search-select" id="guruSelect">
                    <input type="text" id="guruSearch" placeholder="Cari nama guru..." autocomplete="off">
                    <span class="chevron">▼</span>
                    <div class="dropdown" id="guruDropdown"></div>
                </div>

                <div class="btn-row">
                    <button class="btn btn-primary" id="btnStep1Next" disabled onclick="goToStep(2)">
                        Lanjut →
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════ STEP 2: KELAS ═══════════════ -->
        <div class="wizard-step" id="wizardStep2">
            <div class="card">
                <h2>🏫 Pilih Kelas</h2>
                <p class="subtitle">Pilih kelas yang akan dinilai</p>

                <select class="form-select" id="kelasSelect">
                    <option value="">— Pilih Kelas —</option>
                </select>

                <div id="studentLoadingIndicator" style="display:none">
                    <div class="loading-overlay">
                        <div class="spinner"></div>
                        <span>Memuat data siswa...</span>
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn btn-secondary" onclick="goToStep(1)">← Kembali</button>
                    <button class="btn btn-primary" id="btnStep2Next" disabled onclick="goToStep(3)">
                        Lanjut →
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════ STEP 3: BULAN ═══════════════ -->
        <div class="wizard-step" id="wizardStep3">
            <div class="card">
                <h2>📅 Pilih Bulan Penilaian</h2>
                <p class="subtitle">Pilih periode bulan penilaian karakter</p>

                <div class="month-grid" id="monthGrid">
                    <div class="month-card" data-month="Maret 2026" onclick="selectMonth(this)">
                        <div style="font-size:1.5rem; margin-bottom:6px">🗓️</div>
                        Maret 2026
                    </div>
                    <div class="month-card" data-month="April 2026" onclick="selectMonth(this)">
                        <div style="font-size:1.5rem; margin-bottom:6px">🗓️</div>
                        April 2026
                    </div>
                    <div class="month-card" data-month="Mei 2026" onclick="selectMonth(this)">
                        <div style="font-size:1.5rem; margin-bottom:6px">🗓️</div>
                        Mei 2026
                    </div>
                    <div class="month-card" data-month="Juni 2026" onclick="selectMonth(this)">
                        <div style="font-size:1.5rem; margin-bottom:6px">🗓️</div>
                        Juni 2026
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn btn-secondary" onclick="goToStep(2)">← Kembali</button>
                    <button class="btn btn-primary" id="btnStep3Next" disabled onclick="goToStep(4)">
                        Lanjut →
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════ STEP 4: SISWA ═══════════════ -->
        <div class="wizard-step" id="wizardStep4">
            <div class="card">
                <h2>👥 Petakan Siswa</h2>
                <p class="subtitle">Pilih siswa untuk setiap kategori. Siswa yang sudah dipilih di satu kategori otomatis tidak muncul di kategori lain.</p>

                <div class="student-grid" id="studentGrid">
                    <!-- KONSISTEN MUNCUL -->
                    <div class="category-panel cat-konsisten">
                        <div class="category-header">
                            <span>✅ Konsisten Muncul</span>
                            <span class="category-badge" id="badgeKonsisten">0</span>
                            <div class="category-actions">
                                <button class="cat-action-btn" onclick="selectAllStudents('konsisten')">Pilih Semua</button>
                                <button class="cat-action-btn" onclick="clearStudents('konsisten')">Hapus</button>
                            </div>
                        </div>
                        <div class="category-search">
                            <input type="text" placeholder="Cari siswa..." oninput="filterStudents('konsisten', this.value)">
                        </div>
                        <div class="student-list" id="listKonsisten"></div>
                    </div>

                    <!-- SERING MUNCUL -->
                    <div class="category-panel cat-sering">
                        <div class="category-header">
                            <span>🔵 Sering Muncul</span>
                            <span class="category-badge" id="badgeSering">0</span>
                            <div class="category-actions">
                                <button class="cat-action-btn" onclick="selectAllStudents('sering')">Pilih Semua</button>
                                <button class="cat-action-btn" onclick="clearStudents('sering')">Hapus</button>
                            </div>
                        </div>
                        <div class="category-search">
                            <input type="text" placeholder="Cari siswa..." oninput="filterStudents('sering', this.value)">
                        </div>
                        <div class="student-list" id="listSering"></div>
                    </div>

                    <!-- KADANG MUNCUL -->
                    <div class="category-panel cat-kadang">
                        <div class="category-header">
                            <span>🟡 Kadang Muncul</span>
                            <span class="category-badge" id="badgeKadang">0</span>
                            <div class="category-actions">
                                <button class="cat-action-btn" onclick="selectAllStudents('kadang')">Pilih Semua</button>
                                <button class="cat-action-btn" onclick="clearStudents('kadang')">Hapus</button>
                            </div>
                        </div>
                        <div class="category-search">
                            <input type="text" placeholder="Cari siswa..." oninput="filterStudents('kadang', this.value)">
                        </div>
                        <div class="student-list" id="listKadang"></div>
                    </div>

                    <!-- BELUM MUNCUL -->
                    <div class="category-panel cat-belum">
                        <div class="category-header">
                            <span>🔴 Belum Muncul</span>
                            <span class="category-badge" id="badgeBelum">0</span>
                            <div class="category-actions">
                                <button class="cat-action-btn" onclick="selectAllStudents('belum')">Pilih Semua</button>
                                <button class="cat-action-btn" onclick="clearStudents('belum')">Hapus</button>
                            </div>
                        </div>
                        <div class="category-search">
                            <input type="text" placeholder="Cari siswa..." oninput="filterStudents('belum', this.value)">
                        </div>
                        <div class="student-list" id="listBelum"></div>
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn btn-secondary" onclick="goToStep(3)">← Kembali</button>
                    <button class="btn btn-primary" id="btnStep4Next" onclick="goToStep(5)">
                        Lanjut →
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════ STEP 5: KONFIRMASI ═══════════════ -->
        <div class="wizard-step" id="wizardStep5">
            <div class="card">
                <h2>📋 Konfirmasi & Kirim</h2>
                <p class="subtitle">Periksa ringkasan penilaian Anda sebelum mengirim</p>

                <div class="summary-grid" id="summaryInfo">
                    <div class="summary-item">
                        <div class="summary-label">Guru</div>
                        <div class="summary-value" id="sumGuru">-</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Kelas</div>
                        <div class="summary-value" id="sumKelas">-</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Bulan</div>
                        <div class="summary-value" id="sumBulan">-</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Siswa Dipetakan</div>
                        <div class="summary-value" id="sumTotal">-</div>
                    </div>
                </div>

                <div class="summary-students" id="summaryStudents">
                    <div class="summary-cat sc-konsisten">
                        <div class="summary-cat-title">✅ Konsisten Muncul <span class="summary-cat-count" id="sumCountKonsisten">0</span></div>
                        <div class="summary-cat-list" id="sumListKonsisten">-</div>
                    </div>
                    <div class="summary-cat sc-sering">
                        <div class="summary-cat-title">🔵 Sering Muncul <span class="summary-cat-count" id="sumCountSering">0</span></div>
                        <div class="summary-cat-list" id="sumListSering">-</div>
                    </div>
                    <div class="summary-cat sc-kadang">
                        <div class="summary-cat-title">🟡 Kadang Muncul <span class="summary-cat-count" id="sumCountKadang">0</span></div>
                        <div class="summary-cat-list" id="sumListKadang">-</div>
                    </div>
                    <div class="summary-cat sc-belum">
                        <div class="summary-cat-title">🔴 Belum Muncul <span class="summary-cat-count" id="sumCountBelum">0</span></div>
                        <div class="summary-cat-list" id="sumListBelum">-</div>
                    </div>
                </div>

                <label class="confirm-check" id="confirmCheck">
                    <input type="checkbox" id="confirmCheckbox" onchange="toggleSubmitBtn()">
                    <label for="confirmCheckbox">Dengan ini saya sampaikan bahwa informasi diatas saya isi dengan sebenar-benarnya dan dalam keadaan sadar</label>
                </label>

                <div class="btn-row">
                    <button class="btn btn-secondary" onclick="goToStep(4)">← Kembali</button>
                    <button class="btn btn-submit" id="btnSubmit" disabled onclick="submitPenilaian()">
                        🚀 Kirim Penilaian
                    </button>
                </div>

                <!-- Progress -->
                <div class="progress-overlay" id="progressOverlay" style="display:none">
                    <div class="progress-title" id="progressTitle">Mengirim penilaian...</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" id="progressBarFill"></div>
                    </div>
                    <div class="progress-text" id="progressText">Memproses...</div>
                </div>

                <!-- Results -->
                <div class="results-section" id="resultsSection" style="display:none">
                    <h3 style="font-size:1rem; margin-bottom:14px; color:var(--text-primary)" id="resultsTitle">Hasil Pengiriman</h3>
                    <div style="overflow-x:auto">
                        <table class="results-table" id="resultsTable">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Indikator</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="resultsBody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Debug Payloads -->
                <div class="debug-payloads" id="debugPayloads" style="display:none">
                    <h3>🔍 Data yang Di-generate (Debug Mode)</h3>
                    <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:14px">
                        Data berikut adalah payload yang akan dikirim ke API. Dalam mode debug, data ini <strong>tidak dikirim</strong>.
                    </p>
                    <div id="debugPayloadList"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    /* ═══════════════════════════════════════════════════════════════
       APPLICATION STATE
       ═══════════════════════════════════════════════════════════════ */
    const state = {
        currentStep: 1,
        guru: null,
        kodeKelas: null,
        namaKelas: null,
        bulan: null,
        allStudents: [],
        siswa: {
            konsisten: [],
            sering: [],
            kadang: [],
            belum: [],
        },
        teachers: [],
        classes: [],
        isSubmitting: false,
        mode: '{{ $debugMode ? "debug" : "production" }}',
    };

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    /* ═══════════════════════════════════════════════════════════════
       INITIALIZATION
       ═══════════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', () => {
        loadTeachers();
        loadClasses();
        setupGuruSearch();
        updateModeUI();
    });

    async function loadTeachers() {
        try {
            const res = await fetch('/api/teachers');
            const data = await res.json();
            state.teachers = data.options || [];
            renderGuruDropdown(state.teachers);
        } catch (e) {
            showToast('Gagal memuat data guru', 'error');
        }
    }

    async function loadClasses() {
        try {
            const res = await fetch('/api/classes');
            const data = await res.json();
            state.classes = data.classes || [];
            renderClassSelect();
        } catch (e) {
            showToast('Gagal memuat data kelas', 'error');
        }
    }

    async function loadStudents(kodeKelas) {
        const indicator = document.getElementById('studentLoadingIndicator');
        indicator.style.display = 'block';
        document.getElementById('btnStep2Next').disabled = true;

        try {
            const res = await fetch(`/api/students?kode_kelas=${encodeURIComponent(kodeKelas)}`);
            const data = await res.json();

            if (data.error) {
                showToast(data.error, 'error');
                return;
            }

            state.allStudents = data.options || [];
            // Reset student mapping
            state.siswa = { konsisten: [], sering: [], kadang: [], belum: [] };
            renderAllStudentLists();
            document.getElementById('btnStep2Next').disabled = false;
        } catch (e) {
            showToast('Gagal memuat data siswa', 'error');
        } finally {
            indicator.style.display = 'none';
        }
    }

    /* ═══════════════════════════════════════════════════════════════
       GURU SEARCHABLE SELECT
       ═══════════════════════════════════════════════════════════════ */
    function setupGuruSearch() {
        const input = document.getElementById('guruSearch');
        const container = document.getElementById('guruSelect');

        input.addEventListener('focus', () => {
            container.classList.add('open');
            renderGuruDropdown(filterList(state.teachers, input.value));
        });

        input.addEventListener('input', () => {
            container.classList.add('open');
            renderGuruDropdown(filterList(state.teachers, input.value));
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                container.classList.remove('open');
            }
        });
    }

    function renderGuruDropdown(list) {
        const dropdown = document.getElementById('guruDropdown');

        if (list.length === 0) {
            dropdown.innerHTML = '<div class="no-results">Tidak ditemukan</div>';
            return;
        }

        dropdown.innerHTML = list.map(name => `
            <div class="dropdown-item ${state.guru === name ? 'selected' : ''}"
                 onclick="selectGuru('${escapeHtml(name)}')">${escapeHtml(name)}</div>
        `).join('');
    }

    function selectGuru(name) {
        state.guru = name;
        document.getElementById('guruSearch').value = name;
        document.getElementById('guruSelect').classList.remove('open');
        document.getElementById('btnStep1Next').disabled = false;
    }

    /* ═══════════════════════════════════════════════════════════════
       CLASS SELECT
       ═══════════════════════════════════════════════════════════════ */
    function renderClassSelect() {
        const select = document.getElementById('kelasSelect');
        select.innerHTML = '<option value="">— Pilih Kelas —</option>';
        state.classes.forEach(c => {
            select.innerHTML += `<option value="${c.code}">${escapeHtml(c.name)}</option>`;
        });

        select.addEventListener('change', () => {
            const code = select.value;
            if (code) {
                state.kodeKelas = code;
                state.namaKelas = state.classes.find(c => c.code === code)?.name || code;
                loadStudents(code);
            } else {
                state.kodeKelas = null;
                state.namaKelas = null;
                document.getElementById('btnStep2Next').disabled = true;
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       MONTH SELECTION
       ═══════════════════════════════════════════════════════════════ */
    function selectMonth(el) {
        document.querySelectorAll('.month-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        state.bulan = el.dataset.month;
        document.getElementById('btnStep3Next').disabled = false;
    }

    /* ═══════════════════════════════════════════════════════════════
       STUDENT MAPPING
       ═══════════════════════════════════════════════════════════════ */
    function getAvailableStudents(category) {
        const otherCategories = ['konsisten', 'sering', 'kadang', 'belum'].filter(c => c !== category);
        const used = new Set();
        otherCategories.forEach(cat => {
            state.siswa[cat].forEach(s => used.add(s));
        });
        return state.allStudents.filter(s => !used.has(s));
    }

    function renderStudentList(category) {
        const listEl = document.getElementById('list' + capitalize(category));
        const available = getAvailableStudents(category);
        const selected = new Set(state.siswa[category]);
        const searchInput = listEl.parentElement.querySelector('.category-search input');
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';

        // Filter by search term
        const filtered = available.filter(s => s.toLowerCase().includes(searchTerm));

        // Separate: selected first, then unselected
        const selectedItems = filtered.filter(s => selected.has(s));
        const unselectedItems = filtered.filter(s => !selected.has(s));
        const ordered = [...selectedItems, ...unselectedItems];

        if (ordered.length === 0 && state.allStudents.length > 0) {
            listEl.innerHTML = '<div class="empty-message">Tidak ada murid dalam kategori ini</div>';
        } else if (state.allStudents.length === 0) {
            listEl.innerHTML = '<div class="empty-message">Belum ada data siswa. Pilih kelas terlebih dahulu.</div>';
        } else {
            listEl.innerHTML = ordered.map(name => `
                <label class="student-item">
                    <input type="checkbox"
                           ${selected.has(name) ? 'checked' : ''}
                           onchange="toggleStudent('${escapeAttr(name)}', '${category}', this.checked)">
                    <span>${escapeHtml(name)}</span>
                </label>
            `).join('');
        }

        // Update badge
        document.getElementById('badge' + capitalize(category)).textContent = state.siswa[category].length;
    }

    function renderAllStudentLists() {
        ['konsisten', 'sering', 'kadang', 'belum'].forEach(cat => renderStudentList(cat));
    }

    function selectAllStudents(category) {
        const available = getAvailableStudents(category);
        // Add all available students not already in this category
        available.forEach(name => {
            if (!state.siswa[category].includes(name)) {
                state.siswa[category].push(name);
            }
        });
        renderAllStudentLists();
    }

    function clearStudents(category) {
        state.siswa[category] = [];
        renderAllStudentLists();
    }

    function toggleStudent(name, category, checked) {
        if (checked) {
            if (!state.siswa[category].includes(name)) {
                state.siswa[category].push(name);
            }
        } else {
            state.siswa[category] = state.siswa[category].filter(s => s !== name);
        }
        // Re-render all lists to remove student from other panels
        renderAllStudentLists();
    }

    function filterStudents(category, searchTerm) {
        renderStudentList(category);
    }

    /* ═══════════════════════════════════════════════════════════════
       WIZARD NAVIGATION
       ═══════════════════════════════════════════════════════════════ */
    function goToStep(step) {
        // Validate can proceed
        if (step > state.currentStep) {
            if (state.currentStep === 1 && !state.guru) return;
            if (state.currentStep === 2 && !state.kodeKelas) return;
            if (state.currentStep === 3 && !state.bulan) return;
        }

        // Don't allow navigation during submission
        if (state.isSubmitting) return;

        // Update step
        state.currentStep = step;

        // Update step panels
        document.querySelectorAll('.wizard-step').forEach((el, i) => {
            el.classList.toggle('active', i + 1 === step);
        });

        // Update step indicator
        document.querySelectorAll('.step-dot').forEach((dot, i) => {
            const s = i + 1;
            dot.classList.remove('active', 'done');
            if (s === step) dot.classList.add('active');
            else if (s < step) dot.classList.add('done');
        });

        document.querySelectorAll('.step-line').forEach((line, i) => {
            line.classList.toggle('done', i + 1 < step);
        });

        // If entering step 5, render summary
        if (step === 5) {
            renderSummary();
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ═══════════════════════════════════════════════════════════════
       SUMMARY (Step 5)
       ═══════════════════════════════════════════════════════════════ */
    function renderSummary() {
        document.getElementById('sumGuru').textContent = state.guru || '-';
        document.getElementById('sumKelas').textContent = state.namaKelas || '-';
        document.getElementById('sumBulan').textContent = state.bulan || '-';

        const totalMapped = state.siswa.konsisten.length + state.siswa.sering.length
                          + state.siswa.kadang.length + state.siswa.belum.length;
        document.getElementById('sumTotal').textContent = `${totalMapped} / ${state.allStudents.length} siswa`;

        ['konsisten', 'sering', 'kadang', 'belum'].forEach(cat => {
            const list = state.siswa[cat];
            document.getElementById('sumCount' + capitalize(cat)).textContent = list.length;
            document.getElementById('sumList' + capitalize(cat)).textContent =
                list.length > 0 ? list.join(', ') : 'Tidak ada murid dalam kategori ini';
        });

        // Reset confirm checkbox
        document.getElementById('confirmCheckbox').checked = false;
        toggleSubmitBtn();
    }

    function toggleSubmitBtn() {
        document.getElementById('btnSubmit').disabled = !document.getElementById('confirmCheckbox').checked;
    }

    /* ═══════════════════════════════════════════════════════════════
       MODE TOGGLE
       ═══════════════════════════════════════════════════════════════ */
    function setMode(mode) {
        state.mode = mode;
        updateModeUI();
    }

    function updateModeUI() {
        const btnDebug = document.getElementById('btnDebug');
        const btnProduction = document.getElementById('btnProduction');
        const btnSubmit = document.getElementById('btnSubmit');

        btnDebug.classList.remove('active-debug');
        btnProduction.classList.remove('active-prod');

        if (state.mode === 'debug') {
            btnDebug.classList.add('active-debug');
            btnSubmit.innerHTML = '🔍 Preview Data (Debug)';
        } else {
            btnProduction.classList.add('active-prod');
            btnSubmit.innerHTML = '🚀 Kirim Penilaian';
        }
    }

    /* ═══════════════════════════════════════════════════════════════
       SUBMIT
       ═══════════════════════════════════════════════════════════════ */
    async function submitPenilaian() {
        if (state.isSubmitting) return;
        state.isSubmitting = true;

        const btnSubmit = document.getElementById('btnSubmit');
        const progressOverlay = document.getElementById('progressOverlay');
        const progressFill = document.getElementById('progressBarFill');
        const progressText = document.getElementById('progressText');
        const progressTitle = document.getElementById('progressTitle');
        const resultsSection = document.getElementById('resultsSection');

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner"></span> Mengirim...';
        progressOverlay.style.display = 'block';
        resultsSection.style.display = 'none';
        progressFill.style.width = '10%';
        progressFill.className = 'progress-bar-fill';
        progressText.textContent = 'Mengirim ke server...';
        progressTitle.textContent = '🚀 Mengirim penilaian...';

        try {
            const isDebug = state.mode === 'debug';
            const payload = {
                guru: state.guru,
                kode_kelas: state.kodeKelas,
                bulan: state.bulan,
                debug: isDebug,
                siswa: {
                    konsisten: state.siswa.konsisten,
                    sering: state.siswa.sering,
                    kadang: state.siswa.kadang,
                    belum: state.siswa.belum,
                },
            };

            progressFill.style.width = '30%';
            progressText.textContent = isDebug
                ? 'Generating payload (debug)...'
                : 'Memproses quiz dan indikator...';

            const res = await fetch('/api/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            progressFill.style.width = '90%';

            const data = await res.json();

            progressFill.style.width = '100%';

            const debugPayloads = document.getElementById('debugPayloads');
            debugPayloads.style.display = 'none';

            if (data.error) {
                progressTitle.textContent = '❌ Gagal';
                progressText.textContent = data.error;
                progressFill.classList.add('has-errors');
                showToast(data.error, 'error');
            } else if (data.debug) {
                // Debug mode — show generated payloads
                progressTitle.textContent = '🔍 Debug — Data berhasil di-generate';
                progressFill.classList.add('success');
                progressText.textContent = `${data.total} payload di-generate (tidak dikirim ke server)`;

                renderResults(data.results || []);
                document.getElementById('resultsTitle').textContent = 'Preview Payload';
                resultsSection.style.display = 'block';

                renderDebugPayloads(data.results || []);
                debugPayloads.style.display = 'block';

                showToast('Debug mode: data tidak dikirim', 'success');
            } else {
                // Production mode
                if (data.failed === 0) {
                    progressTitle.textContent = '✅ Berhasil!';
                    progressFill.classList.add('success');
                } else {
                    progressTitle.textContent = '⚠️ Sebagian Berhasil';
                    progressFill.classList.add('has-errors');
                }

                progressText.textContent = `${data.completed} berhasil, ${data.failed} gagal dari ${data.total} total pengiriman`;
                document.getElementById('resultsTitle').textContent = 'Hasil Pengiriman';

                renderResults(data.results || []);
                resultsSection.style.display = 'block';
            }
        } catch (e) {
            progressTitle.textContent = '❌ Error';
            progressText.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
            progressFill.style.width = '100%';
            progressFill.classList.add('has-errors');
            showToast('Terjadi kesalahan jaringan', 'error');
        } finally {
            state.isSubmitting = false;
            updateModeUI();
            btnSubmit.disabled = false;
        }
    }

    function renderResults(results) {
        const tbody = document.getElementById('resultsBody');
        tbody.innerHTML = results.map(r => {
            let icon = '✗';
            if (r.status === 'success') icon = '✓';
            if (r.status === 'debug') icon = '🔍';
            return `
            <tr>
                <td>${escapeHtml(r.quiz)}</td>
                <td style="max-width:300px">${escapeHtml(r.indicator)}</td>
                <td>
                    <span class="status-badge ${r.status}">
                        ${icon} ${escapeHtml(r.message)}
                    </span>
                </td>
            </tr>`;
        }).join('');
    }

    function renderDebugPayloads(results) {
        const container = document.getElementById('debugPayloadList');
        container.innerHTML = results
            .filter(r => r.payload)
            .map((r, i) => {
                const json = JSON.stringify(r.payload, null, 2);
                return `
                <div class="payload-card">
                    <div class="payload-header" onclick="this.nextElementSibling.classList.toggle('open'); this.querySelector('.ph-toggle').textContent = this.nextElementSibling.classList.contains('open') ? '▲' : '▼'">
                        <div>
                            <span class="ph-quiz">${escapeHtml(r.quiz)}</span>
                            <span>${escapeHtml(r.indicator)}</span>
                        </div>
                        <span class="ph-toggle">▼</span>
                    </div>
                    <div class="payload-body">
                        <pre>${escapeHtml(json)}</pre>
                    </div>
                </div>`;
            }).join('');
    }

    /* ═══════════════════════════════════════════════════════════════
       UTILITIES
       ═══════════════════════════════════════════════════════════════ */
    function filterList(list, query) {
        if (!query) return list;
        const q = query.toLowerCase();
        return list.filter(item => item.toLowerCase().includes(q));
    }

    function capitalize(s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escapeAttr(str) {
        return str.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    function showToast(message, type = 'error') {
        const existing = document.querySelector('.toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(12px)';
            toast.style.transition = '0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
    </script>
</body>
</html>
