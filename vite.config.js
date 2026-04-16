import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 'resources/js/app.js',
                'resources/css/style.css', 
                'resources/js/app.js',  
                'resources/js/schedules/index.js', 
                'resources/js/patients/patients.js',
                'resources/js/patients/patients/edit.js',
                'resources/js/patients/patients/create.js',
                'resources/js/professionalSchedule/professionalSchedule.js',
                
            ],
            refresh: true,
        }),
    ],
    server: { // <--- AGREGÁ ESTE BLOQUE
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true, // Crucial si trabajás en Windows con WSL2 o Docker
            ignored: ['**/node_modules/**', '**/vendor/**'],
        },
    },
});
