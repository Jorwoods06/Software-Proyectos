<style>
    /* ============================================
       FOOTER STYLES - Mobile First
       ============================================ */

    footer {
        background-color: #F2F3F5;
        text-align: center;
        padding: 1rem;
        font-size: 0.8125rem;
        color: #6c757d;
        border-top: 1px solid #e0e0e0;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        margin-top: auto;
    }

    /* ============================================
       TABLET STYLES (768px and up)
       ============================================ */
    @media (min-width: 768px) {
        footer {
            padding: 1.25rem;
            font-size: 0.875rem;
        }
    }

    /* ============================================
       DESKTOP STYLES (992px and up)
       ============================================ */
    @media (min-width: 992px) {
        footer {
            padding: 1.5rem;
            max-width: 100%;
        }
    }

    /* ============================================
       HIGH RESOLUTION DISPLAYS (1440px and up)
       ============================================ */
    @media (min-width: 1440px) {
        footer {
            padding: 1.25rem;
            font-size: 0.8125rem;
        }
    }

    /* ============================================
       ULTRA HIGH RESOLUTION DISPLAYS (1920px and up)
       ============================================ */
    @media (min-width: 1920px) {
        footer {
            padding: 1rem;
            font-size: 0.75rem;
        }
    }
</style>

<footer>
    <p>&copy; {{ date('Y') }} - Sistema de Gestión. Todos los derechos reservados.</p>
</footer>
