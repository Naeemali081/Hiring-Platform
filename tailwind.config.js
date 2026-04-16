import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"DM Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Syne', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'monospace'],
            },
            colors: {
                navy: {
                    DEFAULT: '#0B0B1A',
                    2: '#13132A',
                    3: '#1C1C3A',
                    light: 'rgba(28,28,58,0.6)',
                },
                purple: {
                    DEFAULT: '#7C6EFA',
                    2: '#9B8FFB',
                    3: '#EAE7FF',
                    glow: '#8A78FF',
                    light: 'rgba(124,110,250,0.15)',
                },
                green: {
                    DEFAULT: '#22D3A0',
                    light: 'rgba(34,211,160,0.15)',
                },
                amber: {
                    DEFAULT: '#F5A623',
                    light: 'rgba(245,166,35,0.15)',
                },
                red: {
                    DEFAULT: '#F25C5C',
                    light: 'rgba(242,92,92,0.15)',
                },
                gray: {
                    1: '#1A1A2E',
                    2: '#2A2A4A',
                    3: '#9292B0',
                    4: '#A5A5C0',
                },
                dark: '#05050A',
                surface: {
                    DEFAULT: 'rgba(255, 255, 255, 0.03)',
                    elevated: 'rgba(255, 255, 255, 0.06)',
                    border: 'rgba(255, 255, 255, 0.08)',
                }
            },
            animation: {
                'blob': 'blob 7s infinite',
                'gradient-x': 'gradient-x 15s ease infinite',
                'fade-in-up': 'fadeInUp 0.5s ease-out forwards',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                blob: {
                    '0%': { transform: 'translate(0px, 0px) scale(1)' },
                    '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                    '100%': { transform: 'translate(0px, 0px) scale(1)' },
                },
                'gradient-x': {
                    '0%, 100%': {
                        'background-size': '200% 200%',
                        'background-position': 'left center'
                    },
                    '50%': {
                        'background-size': '200% 200%',
                        'background-position': 'right center'
                    },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                }
            }
        },
    },

    plugins: [forms],
};
