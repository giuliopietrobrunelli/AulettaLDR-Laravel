import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/account-settings-view.js',
                'resources/js/bookings-view.js',
                'resources/js/calendar-render.js',
                'resources/js/confirm.js',
                'resources/js/db.js',
                'resources/js/feedback.js',
                'resources/js/main-view.js',
                'resources/js/manage-dashboard.js',
                'resources/js/manage-guida.js',
                'resources/js/modal.js',
                'resources/js/profile-utils.js',
                'resources/js/toast.js',
                'resources/js/user-state.js'],
            refresh: true,
        }),
    ],
});
