<style>
    .fi-wi-stats-overview-stat.inv-stat {
        position: relative;
        border: 1px solid color-mix(in srgb, var(--inv-accent, #2E4A7D) 24%, #E5E7EB);
        border-radius: 1rem;
        background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
        box-shadow: 0 8px 22px -14px rgba(31, 42, 68, 0.45);
        transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        overflow: hidden;
    }

    .fi-wi-stats-overview-stat.inv-stat::before {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 0.28rem;
        background: var(--inv-accent, #2E4A7D);
    }

    .fi-wi-stats-overview-stat.inv-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 30px -16px rgba(31, 42, 68, 0.5);
    }

    .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-label {
        letter-spacing: 0.01em;
        font-weight: 600;
    }

    .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-value {
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-description {
        font-size: 0.78rem;
    }

    .fi-wi-stats-overview-stat.inv-stat--hero {
        background:
            radial-gradient(120% 120% at 100% 0%, color-mix(in srgb, var(--inv-accent, #2E4A7D) 10%, #FFFFFF) 0%, #FFFFFF 48%),
            linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
    }

    .fi-wi-stats-overview-stat.inv-stat--hero .fi-wi-stats-overview-stat-value {
        font-size: clamp(1.35rem, 1.05rem + 1.2vw, 1.95rem);
    }

    .fi-wi-stats-overview-stat.inv-stat--inventory {
        --inv-accent: #2E4A7D;
    }

    .fi-wi-stats-overview-stat.inv-stat--structure {
        --inv-accent: #1F2A44;
    }

    .fi-wi-stats-overview-stat.inv-stat--pastoral {
        --inv-accent: #2E7D32;
    }

    .fi-wi-stats-overview-stat.inv-stat--security {
        --inv-accent: #B91C1C;
    }

    .fi-wi-stats-overview-stat.inv-stat--cost {
        --inv-accent: #C9A646;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat {
        border: 1px solid color-mix(in srgb, var(--inv-accent, #2E4A7D) 40%, #1f2937);
        background: linear-gradient(180deg, #0f1729 0%, #111d34 100%);
        box-shadow: 0 14px 30px -22px rgba(0, 0, 0, 0.85);
    }

    .dark .fi-wi-stats-overview-stat.inv-stat::before {
        opacity: 0.8;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat:hover {
        box-shadow: 0 20px 36px -20px rgba(0, 0, 0, 0.9);
    }

    .dark .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-label {
        color: #cdd8ef;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-value {
        color: #f4f7ff;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-description {
        color: #9aa8c4;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat--hero {
        background:
            radial-gradient(120% 120% at 100% 0%, color-mix(in srgb, var(--inv-accent, #2E4A7D) 24%, #0f1729) 0%, #0f1729 50%),
            linear-gradient(180deg, #0f1729 0%, #111d34 100%);
    }

    .dark .fi-wi-stats-overview-stat.inv-stat--inventory {
        --inv-accent: #6f8fca;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat--structure {
        --inv-accent: #5f739f;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat--pastoral {
        --inv-accent: #54a866;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat--security {
        --inv-accent: #d66a6a;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat--cost {
        --inv-accent: #d8bf70;
    }

    .dark .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-chart {
        border: 1px solid color-mix(in srgb, var(--inv-accent, #6f8fca) 35%, #24324b);
        border-radius: 0.7rem;
        background: color-mix(in srgb, #0f1729 88%, var(--inv-accent, #6f8fca));
    }

    @media (prefers-reduced-motion: reduce) {
        .fi-wi-stats-overview-stat.inv-stat {
            transition: none;
        }

        .fi-wi-stats-overview-stat.inv-stat:hover {
            transform: none;
        }
    }
</style>
