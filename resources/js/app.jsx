/*
| Entry point: one React app for the public site and the admin, mounted on
| the #app element of resources/views/app.blade.php. All data comes from
| routes/api.php; routes/web.php only serves this shell.
*/

import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Navigate, Route, Routes } from 'react-router';
import { AdminLayout } from './admin/AdminLayout';
import { EducationManager, ExperienceManager, LanguageManager, ProjectManager, SkillGroupManager } from './admin/managers';
import { Dashboard, Login, ProfileForm } from './admin/pages';
import { config } from './lib/config';
import { CareerPath } from './pages/CareerPath';
import { NotFound } from './pages/NotFound';
import { Portfolio } from './pages/Portfolio';
import { PublicLayout } from './site/PublicLayout';

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route index element={<Navigate to={`/${config.defaultLocale}`} replace />} />

                <Route path="admin" element={<AdminLayout />}>
                    <Route index element={<Dashboard />} />
                    <Route path="login" element={<Login />} />
                    <Route path="profile" element={<ProfileForm />} />
                    <Route path="experience" element={<ExperienceManager />} />
                    <Route path="education" element={<EducationManager />} />
                    <Route path="skills" element={<SkillGroupManager />} />
                    <Route path="languages" element={<LanguageManager />} />
                    <Route path="projects" element={<ProjectManager />} />
                    <Route path="*" element={<NotFound />} />
                </Route>

                <Route path=":locale" element={<PublicLayout />}>
                    <Route index element={<Portfolio />} />
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
