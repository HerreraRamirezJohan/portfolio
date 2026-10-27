/*
| Entry point: one React app for the public site and the admin, mounted on
| the #app element of resources/views/app.blade.php. All data comes from
| routes/api.php; routes/web.php only serves this shell.
*/

import { lazy, StrictMode, Suspense } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Navigate, Route, Routes } from 'react-router';
import { config } from './lib/config';
import { CareerPath } from './pages/CareerPath';
import { Cv } from './pages/Cv';
import { Home } from './pages/Home';
import { NotFound } from './pages/NotFound';
import { PublicLayout } from './site/PublicLayout';

// Public visitors never download the admin: it loads on the first /admin URL.
const AdminRoutes = lazy(() => import('./admin/AdminRoutes'));

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route index element={<Navigate to={`/${config.defaultLocale}`} replace />} />

                <Route
                    path="admin/*"
                    element={
                        <Suspense fallback={null}>
                            <AdminRoutes />
                        </Suspense>
                    }
                />

                <Route path=":locale" element={<PublicLayout />}>
                    <Route index element={<Home />} />
                    <Route path="cv" element={<Cv />} />
                    <Route path=":path" element={<CareerPath />} />
                </Route>

                <Route path="*" element={<NotFound />} />
            </Routes>
        </BrowserRouter>
    );
}

createRoot(document.getElementById('app')).render(
    <StrictMode>
        <App />
    </StrictMode>,
);
